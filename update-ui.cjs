const fs = require('fs');
const path = require('path');

const files = [
    'resources/views/components/layouts/admin.blade.php',
    'resources/views/components/layouts/student.blade.php',
    'resources/views/components/layouts/teacher.blade.php',
    'resources/views/components/layouts/secretary.blade.php'
];

files.forEach(file => {
    let p = path.resolve(__dirname, file);
    if (!fs.existsSync(p)) return;
    let content = fs.readFileSync(p, 'utf8');

    // 1. Update <style>
    if (!content.includes('.mini-sidebar')) {
        content = content.replace(
            /<style>body \{ font-family: 'Inter', sans-serif; \} \[x-cloak\] \{ display: none !important; \}<\/style>/,
            `<style>body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; }
        @media (min-width: 1024px) {
            .mini-sidebar .sidebar-text, .mini-sidebar p { display: none !important; opacity: 0; }
            .mini-sidebar a { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }
            .mini-sidebar .logo-box { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; cursor: pointer; }
            .mini-sidebar .user-box { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }
        }
    </style>`
        );
    }

    // 2. Update body x-data
    content = content.replace(
        '<body class="bg-slate-100 antialiased" x-data="{ sidebarOpen: false }">',
        '<body class="bg-slate-100 antialiased" x-data="{ sidebarOpen: false, miniSidebar: localStorage.getItem(\'miniSidebar\') === \'true\' }" x-init="$watch(\'miniSidebar\', val => localStorage.setItem(\'miniSidebar\', val))">'
    );

    // 3. Update <aside>
    content = content.replace(
        /<aside :class="sidebarOpen[^\n]+\n\s+class="[^"]+w-64 bg-slate-950[^"]*flex flex-col shadow-2xl">/m,
        `<aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', miniSidebar ? 'lg:w-[72px] w-64 mini-sidebar' : 'w-64']" class="fixed inset-y-0 left-0 z-50 bg-slate-950 transform transition-all duration-300 lg:translate-x-0 lg:static lg:inset-auto flex flex-col shadow-2xl overflow-hidden truncate">`
    );
    // Fallback if one-liner
    content = content.replace(
        /<aside :class="sidebarOpen \? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-950 transform transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-auto flex flex-col shadow-2xl">/,
        `<aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', miniSidebar ? 'lg:w-[72px] w-64 mini-sidebar' : 'w-64']" class="fixed inset-y-0 left-0 z-50 bg-slate-950 transform transition-all duration-300 lg:translate-x-0 lg:static lg:inset-auto flex flex-col shadow-2xl overflow-hidden truncate">`
    );

    // 4. Update logo box
    content = content.replace(
        /<div class="flex items-center gap-3 px-6 py-4 border-b border-slate-800">/,
        `<div class="logo-box flex items-center gap-3 px-6 py-4 border-b border-slate-800 transition-all duration-300 select-none" @click="miniSidebar = !miniSidebar" title="Toggle Sidebar">`
    );
    if (!content.includes('sidebar-text">\n                    <h1 class="text-white')) {
        content = content.replace(
            /<div>\s*<h1 class="text-white font-bold text-sm leading-tight">/,
            `<div class="sidebar-text">\n                    <h1 class="text-white font-bold text-sm leading-tight">`
        );
        content = content.replace(
            /<div><h1 class="text-white font-bold text-sm leading-tight">/,
            `<div class="sidebar-text"><h1 class="text-white font-bold text-sm leading-tight">`
        );
    }

    // 5. Update user box
    if (!content.includes('user-box')) {
        content = content.replace(
            /<div class="px-4 py-3 border-t border-slate-800">/,
            '<div class="user-box px-4 py-3 border-t border-slate-800 transition-all duration-300">'
        );
    }
    
    // Add sidebar-text to min-w-0 div
    if (!content.includes('sidebar-text truncate') && !content.includes('sidebar-text"><p')) {
        content = content.replace(
            /<div class="flex-1 min-w-0">/,
            '<div class="flex-1 min-w-0 sidebar-text">'
        );
    }

    // Add sidebar-text to form logout
    if (!content.includes('form method="POST" action="{{ route(\'logout\') }}" class="sidebar-text"')) {
        content = content.replace(
            /<form method="POST" action="\{\{ route\('logout'\) \}\}">/,
            '<form method="POST" action="{{ route(\'logout\') }}" class="sidebar-text">'
        );
    }

    // 6. Wrap missing texts in <a> with sidebar-text
    content = content.replace(/(<svg[^>]+>[\s\S]*?<\/svg>)\s*([^<]+)\s*(<\/a>)/g, function(match, svg, text, endA) {
        let cleanText = text.trim();
        if (cleanText && !cleanText.includes('<span')) {
            return `${svg}\n                    <span class="sidebar-text">${cleanText}</span>\n                ${endA}`;
        }
        return match;
    });

    // 7. Insert Header Toggle Button just before mobile toggle
    if (!content.includes('@click="miniSidebar = !miniSidebar" class="hidden lg:block hidden-mobile-toggle')) {
        content = content.replace(
            /<button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">/,
            `<button @click="miniSidebar = !miniSidebar" class="hidden lg:block hidden-mobile-toggle text-gray-500 hover:bg-slate-100 p-1.5 rounded-lg transition-colors mr-3" title="Tata Letak Sidebar">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">`
        );
    }

    fs.writeFileSync(p, content, 'utf8');
});

console.log('UI Updated successfully!');
