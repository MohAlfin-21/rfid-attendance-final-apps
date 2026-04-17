import fs from 'node:fs';
import fsp from 'node:fs/promises';
import path from 'node:path';
import { spawn } from 'node:child_process';

const BASE_URL = process.env.AUDIT_BASE_URL ?? 'http://127.0.0.1:8000';
const CHROME_PATH = process.env.CHROME_PATH ?? 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const REMOTE_DEBUGGING_PORT = Number(process.env.AUDIT_DEBUG_PORT ?? '9229');
const AUDIT_MODE = process.env.AUDIT_MODE ?? 'full';
const ROOT_DIR = process.cwd();
const RUN_ID = new Date().toISOString().replace(/[:.]/g, '-');
const OUTPUT_DIR = path.join(ROOT_DIR, 'storage', 'app', 'public', 'debug-audit', RUN_ID);
const CHROME_PROFILE_DIR = path.join(ROOT_DIR, 'storage', 'app', 'chrome-audit-profile');

const accounts = {
  admin: {
    email: 'admin@rfid-attendance.test',
    password: 'password',
    home: '/admin',
  },
  teacher: {
    email: 'budi@rfid-attendance.test',
    password: 'password',
    home: '/teacher',
  },
  secretary: {
    email: 'siti@rfid-attendance.test',
    password: 'password',
    home: '/secretary',
  },
  student: {
    email: 'ahmad.fauzi@rfid-attendance.test',
    password: 'password',
    home: '/student',
  },
};

const report = {
  runId: RUN_ID,
  baseUrl: BASE_URL,
  outputDir: OUTPUT_DIR,
  startedAt: new Date().toISOString(),
  findings: [],
  steps: [],
  resources: {},
};

function slug(value) {
  return value
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .slice(0, 80);
}

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function ensureDir(dir) {
  await fsp.mkdir(dir, { recursive: true });
}

async function waitForHttp(url, timeoutMs = 15000) {
  const deadline = Date.now() + timeoutMs;

  while (Date.now() < deadline) {
    try {
      const response = await fetch(url, { redirect: 'follow' });
      if (response.ok) {
        return;
      }
    } catch {
      // Keep polling.
    }

    await sleep(250);
  }

  throw new Error(`Timed out waiting for ${url}`);
}

function relativeOutput(filePath) {
  return path.relative(ROOT_DIR, filePath).replace(/\\/g, '/');
}

class CDPPage {
  constructor(wsUrl) {
    this.wsUrl = wsUrl;
    this.ws = null;
    this.nextId = 1;
    this.pending = new Map();
    this.eventWaiters = [];
  }

  async connect() {
    this.ws = new WebSocket(this.wsUrl);

    await new Promise((resolve, reject) => {
      const timer = setTimeout(() => reject(new Error('Timed out connecting to Chrome DevTools.')), 10000);

      this.ws.addEventListener('open', () => {
        clearTimeout(timer);
        resolve();
      }, { once: true });

      this.ws.addEventListener('error', (event) => {
        clearTimeout(timer);
        reject(event.error ?? new Error('Failed to connect to Chrome DevTools.'));
      }, { once: true });
    });

    this.ws.addEventListener('message', (event) => {
      const payload = JSON.parse(String(event.data));

      if (typeof payload.id === 'number') {
        const pending = this.pending.get(payload.id);
        if (!pending) {
          return;
        }

        this.pending.delete(payload.id);

        if (payload.error) {
          pending.reject(new Error(payload.error.message ?? 'Chrome DevTools command failed.'));
          return;
        }

        pending.resolve(payload.result);
        return;
      }

      for (const waiter of [...this.eventWaiters]) {
        if (waiter.eventName !== payload.method) {
          continue;
        }

        let matched = false;
        try {
          matched = waiter.predicate(payload.params ?? {});
        } catch (error) {
          waiter.reject(error);
          this.eventWaiters = this.eventWaiters.filter((entry) => entry !== waiter);
          continue;
        }

        if (!matched) {
          continue;
        }

        clearTimeout(waiter.timer);
        waiter.resolve(payload.params ?? {});
        this.eventWaiters = this.eventWaiters.filter((entry) => entry !== waiter);
      }
    });
  }

  async close() {
    if (!this.ws) {
      return;
    }

    await new Promise((resolve) => {
      if (this.ws.readyState >= WebSocket.CLOSING) {
        resolve();
        return;
      }

      this.ws.addEventListener('close', () => resolve(), { once: true });
      this.ws.close();
    });
  }

  send(method, params = {}) {
    const id = this.nextId++;

    return new Promise((resolve, reject) => {
      this.pending.set(id, { resolve, reject });
      this.ws.send(JSON.stringify({ id, method, params }));
    });
  }

  waitForEvent(eventName, predicate = () => true, timeoutMs = 10000) {
    return new Promise((resolve, reject) => {
      const timer = setTimeout(() => {
        this.eventWaiters = this.eventWaiters.filter((entry) => entry !== waiter);
        reject(new Error(`Timed out waiting for event ${eventName}.`));
      }, timeoutMs);

      const waiter = {
        eventName,
        predicate,
        resolve,
        reject,
        timer,
      };

      this.eventWaiters.push(waiter);
    });
  }

  async initialize() {
    await this.send('Page.enable');
    await this.send('Runtime.enable');
    await this.send('Network.enable');
    await this.send('Log.enable');
    await this.send('Emulation.setDeviceMetricsOverride', {
      width: 1440,
      height: 1200,
      deviceScaleFactor: 1,
      mobile: false,
    });
  }

  async evaluate(expression) {
    const result = await this.send('Runtime.evaluate', {
      expression,
      awaitPromise: true,
      returnByValue: true,
    });

    return result.result?.value;
  }

  async waitForReady(timeoutMs = 15000) {
    const deadline = Date.now() + timeoutMs;

    while (Date.now() < deadline) {
      try {
        const readyState = await this.evaluate('document.readyState');
        if (readyState === 'complete') {
          try {
            await this.evaluate(`
              (async () => {
                if (document.fonts?.ready) {
                  await document.fonts.ready;
                }
                return true;
              })()
            `);
          } catch {
            // Fonts are not critical.
          }

          await sleep(300);
          return;
        }
      } catch {
        // The page can be in the middle of navigation.
      }

      await sleep(150);
    }

    throw new Error('Timed out waiting for page readiness.');
  }

  async goto(url) {
    const loadEvent = this.waitForEvent('Page.loadEventFired', () => true, 15000).catch(() => null);
    await this.send('Page.navigate', { url });
    await loadEvent;
    await this.waitForReady();
  }

  async submitForm(action, fields = {}, method = 'POST') {
    const loadEvent = this.waitForEvent('Page.loadEventFired', () => true, 15000).catch(() => null);

    await this.evaluate(`
      (() => {
        const action = ${JSON.stringify(action)};
        const method = ${JSON.stringify(method.toUpperCase())};
        const fields = ${JSON.stringify(fields)};
        const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        const form = document.createElement('form');

        form.method = 'POST';
        form.action = action;
        form.style.display = 'none';

        const append = (name, value) => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = name;
          input.value = value ?? '';
          form.appendChild(input);
        };

        if (token) {
          append('_token', token);
        }

        if (method !== 'POST') {
          append('_method', method);
        }

        for (const [name, value] of Object.entries(fields)) {
          append(name, String(value ?? ''));
        }

        document.body.appendChild(form);
        form.submit();
        return true;
      })()
    `);

    await loadEvent;
    await this.waitForReady();
  }

  async clearSession() {
    await this.send('Network.clearBrowserCookies');
    await this.send('Network.clearBrowserCache');
  }

  async state() {
    return this.evaluate(`
      (() => ({
        url: location.href,
        title: document.title,
        body: document.body?.innerText?.slice(0, 2000) ?? '',
      }))()
    `);
  }

  async firstMatchingRow(targetText) {
    return this.evaluate(`
      (() => {
        const targetText = ${JSON.stringify(targetText)};

        for (const row of document.querySelectorAll('tr')) {
          if (!row.innerText.includes(targetText)) {
            continue;
          }

          const links = [...row.querySelectorAll('a[href]')];
          const forms = [...row.querySelectorAll('form[action]')];

          return {
            text: row.innerText,
            links: links.map((link) => ({ href: link.href, text: link.innerText.trim() })),
            forms: forms.map((form) => ({ action: form.action })),
          };
        }

        return null;
      })()
    `);
  }

  async allActionTargets(fragment) {
    return this.evaluate(`
      (() => {
        const fragment = ${JSON.stringify(fragment)};
        return [...document.querySelectorAll('a[href], form[action]')]
          .map((element) => element.href ?? element.action ?? '')
          .filter((value) => value.includes(fragment));
      })()
    `);
  }

  async firstLink(fragment) {
    const targets = await this.allActionTargets(fragment);
    return targets[0] ?? null;
  }

  async screenshot(filePath) {
    const metrics = await this.send('Page.getLayoutMetrics');
    const width = Math.min(Math.ceil(metrics.contentSize.width || 1440), 1440);
    const height = Math.min(Math.ceil(metrics.contentSize.height || 1200), 5000);
    const result = await this.send('Page.captureScreenshot', {
      format: 'png',
      fromSurface: true,
      captureBeyondViewport: true,
      clip: {
        x: 0,
        y: 0,
        width,
        height,
        scale: 1,
      },
    });

    await ensureDir(path.dirname(filePath));
    await fsp.writeFile(filePath, Buffer.from(result.data, 'base64'));
  }
}

async function startChrome() {
  await ensureDir(CHROME_PROFILE_DIR);

  const chrome = spawn(CHROME_PATH, [
    '--headless=new',
    '--disable-gpu',
    '--hide-scrollbars',
    '--no-first-run',
    '--no-default-browser-check',
    `--remote-debugging-port=${REMOTE_DEBUGGING_PORT}`,
    `--user-data-dir=${CHROME_PROFILE_DIR}`,
    'about:blank',
  ], {
    stdio: 'ignore',
    windowsHide: true,
  });

  chrome.unref();
  await waitForHttp(`http://127.0.0.1:${REMOTE_DEBUGGING_PORT}/json/version`, 15000);
  return chrome;
}

async function connectToPage() {
  const newTarget = await fetch(`http://127.0.0.1:${REMOTE_DEBUGGING_PORT}/json/new?${encodeURIComponent('about:blank')}`, {
    method: 'PUT',
  });
  const target = await newTarget.json();
  const page = new CDPPage(target.webSocketDebuggerUrl);
  await page.connect();
  await page.initialize();
  return page;
}

function recordStep({ name, status, screenshot = null, note = null, url = null, error = null }) {
  report.steps.push({ name, status, screenshot, note, url, error });
}

async function capture(page, section, label, note = null) {
  const filePath = path.join(OUTPUT_DIR, section, `${slug(label)}.png`);
  await page.screenshot(filePath);
  const state = await page.state();

  recordStep({
    name: `${section}:${label}`,
    status: 'passed',
    screenshot: relativeOutput(filePath),
    note,
    url: state.url,
  });

  return { filePath, state };
}

async function captureFailure(page, section, label, error) {
  const filePath = path.join(OUTPUT_DIR, section, `${slug(label)}-error.png`);

  try {
    await page.screenshot(filePath);
  } catch {
    // Ignore screenshot failures while already handling an error.
  }

  recordStep({
    name: `${section}:${label}`,
    status: 'failed',
    screenshot: fs.existsSync(filePath) ? relativeOutput(filePath) : null,
    error: error.message,
  });
}

async function withStep(page, section, label, action) {
  try {
    return await action();
  } catch (error) {
    await captureFailure(page, section, label, error);
    throw error;
  }
}

async function login(page, role) {
  const account = accounts[role];

  await page.clearSession();
  await page.goto(`${BASE_URL}/login`);
  await page.submitForm(`${BASE_URL}/login`, {
    email: account.email,
    password: account.password,
  }, 'POST');

  const state = await page.state();
  if (state.url.includes('/login')) {
    throw new Error(`Login failed for ${role}: ${state.body}`);
  }

  return state;
}

async function ensureSecretaryUser(page) {
  await login(page, 'admin');
  await page.goto(`${BASE_URL}/admin/users?search=${encodeURIComponent(accounts.secretary.email)}`);

  const existing = await page.firstMatchingRow(accounts.secretary.email);
  if (existing) {
    report.findings.push({
      severity: 'info',
      message: `Secretary account already existed: ${accounts.secretary.email}`,
    });
    await capture(page, 'admin', 'users-secretary-existing', 'Secretary account was already present.');
    return;
  }

  await page.goto(`${BASE_URL}/admin/users/create`);
  await capture(page, 'admin', 'users-secretary-create-form');

  await page.submitForm(`${BASE_URL}/admin/users`, {
    name: 'Siti Rahayu',
    username: 'siti.rahayu',
    email: accounts.secretary.email,
    nis: '',
    password: accounts.secretary.password,
    password_confirmation: accounts.secretary.password,
    role: 'secretary',
    is_active: '1',
  }, 'POST');

  await page.goto(`${BASE_URL}/admin/users?search=${encodeURIComponent(accounts.secretary.email)}`);
  const created = await page.firstMatchingRow(accounts.secretary.email);

  if (!created) {
    throw new Error('Secretary account could not be created from the admin UI.');
  }

  report.findings.push({
    severity: 'warning',
    message: 'Provided secretary credentials were missing in the database; created the account through admin users.',
  });

  await capture(page, 'admin', 'users-secretary-created', 'Created the missing secretary account from the admin panel.');
}

async function createTempAdminResources(page) {
  const token = Date.now();
  const temp = {
    user: {
      name: 'Audit Temp User',
      username: `audit.user.${token}`,
      email: `audit.user.${token}@rfid-attendance.test`,
    },
    classroom: {
      code: `AUDIT-${String(token).slice(-6)}`,
      name: `Audit Class ${String(token).slice(-4)}`,
    },
    device: {
      code: `AUDIT-DEV-${String(token).slice(-6)}`,
      name: `Audit Device ${String(token).slice(-4)}`,
    },
  };

  report.resources.temp = temp;

  await page.goto(`${BASE_URL}/admin/users`);
  await capture(page, 'admin', 'users-index');

  await page.goto(`${BASE_URL}/admin/users/create`);
  await capture(page, 'admin', 'users-create-form');

  await page.submitForm(`${BASE_URL}/admin/users`, {
    name: temp.user.name,
    username: temp.user.username,
    email: temp.user.email,
    nis: '',
    password: 'password',
    password_confirmation: 'password',
    role: 'student',
    is_active: '1',
  }, 'POST');

  await page.goto(`${BASE_URL}/admin/users?search=${encodeURIComponent(temp.user.email)}`);
  await capture(page, 'admin', 'users-created-temp');

  const userRow = await page.firstMatchingRow(temp.user.email);
  const userEditUrl = userRow?.links.find((link) => link.href.includes('/edit'))?.href;
  const userDeleteAction = userRow?.forms[0]?.action ?? null;

  if (!userEditUrl || !userDeleteAction) {
    throw new Error('Temporary user actions could not be discovered in the users table.');
  }

  temp.user.editUrl = userEditUrl;
  temp.user.deleteAction = userDeleteAction;

  await page.goto(userEditUrl);
  await capture(page, 'admin', 'users-edit-temp');

  await page.submitForm(userEditUrl.replace('/edit', ''), {
    name: 'Audit Temp User Updated',
    username: temp.user.username,
    email: temp.user.email,
    nis: '',
    password: '',
    password_confirmation: '',
    role: 'student',
    is_active: '1',
  }, 'PUT');

  await page.goto(`${BASE_URL}/admin/users?search=${encodeURIComponent(temp.user.email)}`);
  await capture(page, 'admin', 'users-updated-temp');

  await page.goto(`${BASE_URL}/admin/classrooms`);
  await capture(page, 'admin', 'classrooms-index');

  await page.goto(`${BASE_URL}/admin/classrooms/create`);
  await capture(page, 'admin', 'classrooms-create-form');

  await page.submitForm(`${BASE_URL}/admin/classrooms`, {
    code: temp.classroom.code,
    name: temp.classroom.name,
    grade: '12',
    major: 'RPL',
    homeroom_teacher_id: '2',
    is_active: '1',
  }, 'POST');

  await page.goto(`${BASE_URL}/admin/classrooms?search=${encodeURIComponent(temp.classroom.code)}`);
  await capture(page, 'admin', 'classrooms-created-temp');

  const classroomRow = await page.firstMatchingRow(temp.classroom.code);
  const classroomDetailUrl = classroomRow?.links.find((link) => link.text.includes('Detail'))?.href;
  const classroomEditUrl = classroomRow?.links.find((link) => link.href.includes('/edit'))?.href;
  const classroomDeleteAction = classroomRow?.forms[0]?.action ?? null;

  if (!classroomDetailUrl || !classroomEditUrl || !classroomDeleteAction) {
    throw new Error('Temporary classroom actions could not be discovered in the classrooms table.');
  }

  temp.classroom.detailUrl = classroomDetailUrl;
  temp.classroom.editUrl = classroomEditUrl;
  temp.classroom.deleteAction = classroomDeleteAction;

  await page.goto(classroomDetailUrl);
  await capture(page, 'admin', 'classrooms-detail-temp');

  await page.goto(classroomEditUrl);
  await capture(page, 'admin', 'classrooms-edit-temp');

  await page.submitForm(classroomEditUrl.replace('/edit', ''), {
    code: temp.classroom.code,
    name: `${temp.classroom.name} Updated`,
    grade: '12',
    major: 'RPL',
    homeroom_teacher_id: '2',
    is_active: '1',
  }, 'PUT');

  temp.classroom.name = `${temp.classroom.name} Updated`;

  await page.goto(`${BASE_URL}/admin/classrooms?search=${encodeURIComponent(temp.classroom.code)}`);
  await capture(page, 'admin', 'classrooms-updated-temp');

  await page.submitForm(classroomEditUrl.replace('/edit', ''), {
    code: temp.classroom.code,
    name: temp.classroom.name,
    grade: '12',
    major: 'RPL',
    homeroom_teacher_id: '2',
    is_active: '1',
    add_student_id: '3',
  }, 'PUT');

  await page.goto(classroomDetailUrl);
  await capture(page, 'admin', 'classrooms-added-student-temp');

  await page.submitForm(classroomEditUrl.replace('/edit', ''), {
    code: temp.classroom.code,
    name: temp.classroom.name,
    grade: '12',
    major: 'RPL',
    homeroom_teacher_id: '2',
    is_active: '1',
    remove_student_id: '3',
  }, 'PUT');

  await page.goto(classroomDetailUrl);
  await capture(page, 'admin', 'classrooms-removed-student-temp');

  await page.goto(`${BASE_URL}/admin/devices`);
  await capture(page, 'admin', 'devices-index');

  await page.goto(`${BASE_URL}/admin/devices/create`);
  await capture(page, 'admin', 'devices-create-form');

  await page.submitForm(`${BASE_URL}/admin/devices`, {
    code: temp.device.code,
    name: temp.device.name,
    location: 'Audit Lab',
    is_active: '1',
  }, 'POST');

  await page.goto(`${BASE_URL}/admin/devices`);
  await capture(page, 'admin', 'devices-created-temp');

  const deviceRow = await page.firstMatchingRow(temp.device.code);
  const deviceDetailUrl = deviceRow?.links.find((link) => link.text.includes('Detail'))?.href;
  const deviceEditUrl = deviceRow?.links.find((link) => link.href.includes('/edit'))?.href;
  const deviceDeleteAction = deviceRow?.forms[0]?.action ?? null;

  if (!deviceDetailUrl || !deviceEditUrl || !deviceDeleteAction) {
    throw new Error('Temporary device actions could not be discovered in the devices table.');
  }

  temp.device.detailUrl = deviceDetailUrl;
  temp.device.editUrl = deviceEditUrl;
  temp.device.deleteAction = deviceDeleteAction;

  await page.goto(deviceDetailUrl);
  await capture(page, 'admin', 'devices-detail-temp');

  await page.goto(deviceEditUrl);
  await capture(page, 'admin', 'devices-edit-temp');

  await page.submitForm(deviceEditUrl.replace('/edit', ''), {
    code: temp.device.code,
    name: `${temp.device.name} Updated`,
    location: 'Audit Lab Updated',
    is_active: '1',
    rotate_token: '0',
  }, 'PUT');

  temp.device.name = `${temp.device.name} Updated`;

  await page.goto(`${BASE_URL}/admin/devices`);
  await capture(page, 'admin', 'devices-updated-temp');

  return temp;
}

async function runAdminAudit(page) {
  await login(page, 'admin');

  await page.goto(`${BASE_URL}/admin`);
  await capture(page, 'admin', 'dashboard');

  await createTempAdminResources(page);

  await page.goto(`${BASE_URL}/admin/attendances`);
  await capture(page, 'admin', 'attendances-index');

  const attendanceOverrideTargets = await page.allActionTargets('/admin/attendances/');
  const attendanceOverrideAction = attendanceOverrideTargets.find((target) => target.includes('/override')) ?? null;

  if (attendanceOverrideAction) {
    await page.submitForm(attendanceOverrideAction, {
      status: 'late',
      override_note: 'Audit override by Codex',
    }, 'POST');
    await capture(page, 'admin', 'attendances-overridden');
  } else {
    report.findings.push({
      severity: 'warning',
      message: 'Admin attendance override was skipped because no attendance row was rendered.',
    });
  }

  await page.goto(`${BASE_URL}/admin/absence-requests`);
  await capture(page, 'admin', 'absence-requests-empty');

  await page.goto(`${BASE_URL}/admin/settings`);
  await capture(page, 'admin', 'settings');

  await page.goto(`${BASE_URL}/profile`);
  await capture(page, 'admin', 'profile');
}

async function runStudentAudit(page) {
  await login(page, 'student');

  await page.goto(`${BASE_URL}/student`);
  await capture(page, 'student', 'dashboard');

  await page.goto(`${BASE_URL}/student/attendance`);
  await capture(page, 'student', 'attendance');

  await page.goto(`${BASE_URL}/student/absence-requests`);
  await capture(page, 'student', 'absence-requests-index-before');

  const requests = [
    {
      type: 'permission',
      date_start: '2026-04-13',
      date_end: '2026-04-13',
      reason: 'Audit request for teacher review',
    },
    {
      type: 'sick',
      date_start: '2026-04-14',
      date_end: '2026-04-14',
      reason: 'Audit request for secretary review',
    },
    {
      type: 'other',
      date_start: '2026-04-15',
      date_end: '2026-04-15',
      reason: 'Audit request for admin review',
    },
  ];

  for (const request of requests) {
    await page.goto(`${BASE_URL}/student/absence-requests/create`);
    await capture(page, 'student', `absence-requests-create-${request.type}`);

    await page.submitForm(`${BASE_URL}/student/absence-requests`, {
      type: request.type,
      date_start: request.date_start,
      date_end: request.date_end,
      reason: request.reason,
    }, 'POST');

    await capture(page, 'student', `absence-requests-created-${request.type}`);
  }

  await page.goto(`${BASE_URL}/student`);
  await capture(page, 'student', 'dashboard-after-create');

  await page.goto(`${BASE_URL}/profile`);
  await capture(page, 'student', 'profile');
}

async function runTeacherAudit(page) {
  await login(page, 'teacher');

  await page.goto(`${BASE_URL}/teacher`);
  await capture(page, 'teacher', 'dashboard');

  await page.goto(`${BASE_URL}/teacher/classroom`);
  await capture(page, 'teacher', 'classroom');

  await page.goto(`${BASE_URL}/teacher/attendance`);
  await capture(page, 'teacher', 'attendance');

  await page.goto(`${BASE_URL}/teacher/absence-requests`);
  await capture(page, 'teacher', 'absence-requests-before-review');

  const teacherReviewAction = (await page.allActionTargets('/teacher/absence-requests/'))[0] ?? null;
  if (!teacherReviewAction) {
    throw new Error('Teacher review action was not found.');
  }

  await page.submitForm(teacherReviewAction, {
    status: 'approved',
    review_note: 'Approved during browser audit',
  }, 'PUT');

  await capture(page, 'teacher', 'absence-requests-after-review');

  await page.goto(`${BASE_URL}/profile`);
  await capture(page, 'teacher', 'profile');
}

async function runSecretaryAudit(page) {
  await login(page, 'secretary');

  await page.goto(`${BASE_URL}/secretary`);
  await capture(page, 'secretary', 'dashboard');

  await page.goto(`${BASE_URL}/secretary/absence-requests`);
  await capture(page, 'secretary', 'absence-requests-before-review');

  const reviewTargets = await page.allActionTargets('/secretary/absence-requests/');
  const secretaryReviewAction = reviewTargets[0] ?? null;
  if (!secretaryReviewAction) {
    throw new Error('Secretary review action was not found.');
  }

  await page.submitForm(secretaryReviewAction, {
    status: 'rejected',
    review_note: 'Rejected during browser audit',
  }, 'PUT');

  await capture(page, 'secretary', 'absence-requests-after-review');

  await page.goto(`${BASE_URL}/profile`);
  await capture(page, 'secretary', 'profile');
}

async function runAdminAbsenceReviewAndCleanup(page) {
  await login(page, 'admin');

  await page.goto(`${BASE_URL}/admin/absence-requests`);
  await capture(page, 'admin', 'absence-requests-pending');

  const detailUrl = await page.firstLink('/admin/absence-requests/');
  if (!detailUrl) {
    throw new Error('Admin absence request detail page was not found.');
  }

  await page.goto(detailUrl);
  await capture(page, 'admin', 'absence-requests-detail');

  await page.submitForm(detailUrl, {
    status: 'approved',
    review_note: 'Approved during admin browser audit',
  }, 'PUT');

  await capture(page, 'admin', 'absence-requests-after-admin-review');

  await page.goto(`${BASE_URL}/admin/absence-requests?status=approved`);
  await capture(page, 'admin', 'absence-requests-approved-tab');

  await page.goto(`${BASE_URL}/admin/absence-requests?status=rejected`);
  await capture(page, 'admin', 'absence-requests-rejected-tab');

  const temp = report.resources.temp;
  if (!temp) {
    return;
  }

  await page.submitForm(temp.user.deleteAction, {}, 'DELETE');
  await page.goto(`${BASE_URL}/admin/users?search=${encodeURIComponent(temp.user.email)}`);
  await capture(page, 'admin', 'users-deleted-temp');

  await page.submitForm(temp.classroom.deleteAction, {}, 'DELETE');
  await page.goto(`${BASE_URL}/admin/classrooms?search=${encodeURIComponent(temp.classroom.code)}`);
  await capture(page, 'admin', 'classrooms-deleted-temp');

  await page.submitForm(temp.device.deleteAction, {}, 'DELETE');
  await page.goto(`${BASE_URL}/admin/devices`);
  await capture(page, 'admin', 'devices-deleted-temp');
}

async function runAttendanceOnlyAudit(page) {
  await withStep(page, 'student', 'attendance-only-student', async () => {
    await login(page, 'student');
    await page.goto(`${BASE_URL}/student`);
    await capture(page, 'attendance-only', 'student-dashboard');
    await page.goto(`${BASE_URL}/student/attendance`);
    await capture(page, 'attendance-only', 'student-attendance');
  });

  await withStep(page, 'teacher', 'attendance-only-teacher', async () => {
    await login(page, 'teacher');
    await page.goto(`${BASE_URL}/teacher`);
    await capture(page, 'attendance-only', 'teacher-dashboard');
    await page.goto(`${BASE_URL}/teacher/attendance`);
    await capture(page, 'attendance-only', 'teacher-attendance');
  });

  await withStep(page, 'admin', 'attendance-only-admin', async () => {
    await login(page, 'admin');
    await page.goto(`${BASE_URL}/admin/attendances`);
    await capture(page, 'attendance-only', 'admin-attendances-before-override');

    const attendanceOverrideTargets = await page.allActionTargets('/admin/attendances/');
    const attendanceOverrideAction = attendanceOverrideTargets.find((target) => target.includes('/override')) ?? null;

    if (!attendanceOverrideAction) {
      throw new Error('Attendance override action was still not rendered on the admin page.');
    }

    await page.submitForm(attendanceOverrideAction, {
      status: 'late',
      override_note: 'Attendance-only audit override by Codex',
    }, 'POST');

    await capture(page, 'attendance-only', 'admin-attendances-after-override');
    await page.goto(`${BASE_URL}/admin`);
    await capture(page, 'attendance-only', 'admin-dashboard-after-override');
  });
}

async function writeReport() {
  report.finishedAt = new Date().toISOString();
  await ensureDir(OUTPUT_DIR);
  await fsp.writeFile(path.join(OUTPUT_DIR, 'report.json'), JSON.stringify(report, null, 2));
}

async function main() {
  await ensureDir(OUTPUT_DIR);
  await waitForHttp(`${BASE_URL}/login`, 15000);

  const chrome = await startChrome();
  const page = await connectToPage();

  try {
    await withStep(page, 'public', 'login-page', async () => {
      await page.goto(`${BASE_URL}/login`);
      await capture(page, 'public', 'login-page');
    });

    if (AUDIT_MODE === 'attendance-only') {
      await runAttendanceOnlyAudit(page);
      return;
    }

    await withStep(page, 'admin', 'ensure-secretary-user', async () => {
      await ensureSecretaryUser(page);
    });

    await withStep(page, 'admin', 'admin-audit', async () => {
      await runAdminAudit(page);
    });

    await withStep(page, 'student', 'student-audit', async () => {
      await runStudentAudit(page);
    });

    await withStep(page, 'teacher', 'teacher-audit', async () => {
      await runTeacherAudit(page);
    });

    await withStep(page, 'secretary', 'secretary-audit', async () => {
      await runSecretaryAudit(page);
    });

    await withStep(page, 'admin', 'admin-absence-review-cleanup', async () => {
      await runAdminAbsenceReviewAndCleanup(page);
    });
  } finally {
    await writeReport();
    await page.close();

    if (chrome.pid) {
      try {
        process.kill(chrome.pid);
      } catch {
        // Ignore shutdown races.
      }
    }
  }
}

main().catch(async (error) => {
  report.findings.push({
    severity: 'error',
    message: error.message,
  });
  await writeReport();
  console.error(error);
  process.exitCode = 1;
});
