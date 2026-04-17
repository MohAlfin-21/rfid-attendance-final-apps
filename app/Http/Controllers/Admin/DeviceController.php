<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Devices\Services\DeviceHealthService;
use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    public function index(DeviceHealthService $healthService)
    {
        $devices = Device::latest()->paginate(15);
        $snapshots = [];
        foreach ($devices as $device) {
            $snapshots[$device->id] = $healthService->snapshot($device);
        }
        return view('admin.devices.index', compact('devices', 'snapshots'));
    }

    public function create()
    {
        return view('admin.devices.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:devices',
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $plainToken = 'dev-' . Str::random(40);
        Device::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'token_hash' => hash('sha256', $plainToken),
            'token_plain_encrypted' => $plainToken,
            'is_active' => $request->boolean('is_active', true),
            'heartbeat_interval_seconds' => (int) config('devices.default_heartbeat_interval', 60),
            'error_count' => 0,
        ]);

        return redirect()->route('admin.devices.index')->with('success', __('Perangkat berhasil ditambahkan. Token: :token', ['token' => $plainToken]));
    }

    public function show(Device $device, DeviceHealthService $healthService)
    {
        $snapshot = $healthService->snapshot($device);
        $logs = $device->attendanceLogs()->with('attendance.user')->latest()->take(20)->get();
        return view('admin.devices.show', compact('device', 'snapshot', 'logs'));
    }

    public function edit(Device $device)
    {
        return view('admin.devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('devices')->ignore($device)],
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'rotate_token' => 'boolean',
        ]);

        $device->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $message = __('Perangkat berhasil diperbarui.');

        if ($request->boolean('rotate_token')) {
            $plainToken = 'dev-' . Str::random(40);
            $device->update([
                'token_hash' => hash('sha256', $plainToken),
                'token_plain_encrypted' => $plainToken,
            ]);
            $message .= ' ' . __('Token baru: :token', ['token' => $plainToken]);
        }

        return redirect()->route('admin.devices.index')->with('success', $message);
    }

    public function destroy(Device $device): RedirectResponse
    {
        $device->delete();
        return redirect()->route('admin.devices.index')->with('success', __('Perangkat berhasil dihapus.'));
    }
}
