<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $groups = [
            'attendance' => SystemSetting::where('group', 'attendance')->get(),
            'academic' => SystemSetting::where('group', 'academic')->get(),
            'device' => SystemSetting::where('group', 'device')->get(),
            'general' => SystemSetting::where('group', 'general')->get(),
        ];

        return view('admin.settings.localized', compact('groups'));
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value ?? '']);
                \Illuminate\Support\Facades\Cache::forget("system_setting:{$key}");
            }
        }

        \Illuminate\Support\Facades\Cache::forget('system_settings:all');

        return redirect()->route('admin.settings.index')->with('success', __('Pengaturan berhasil disimpan.'));
    }
}
