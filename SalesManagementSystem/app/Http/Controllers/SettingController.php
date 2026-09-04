<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AuditService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $fields = $request->except(['_token']);

        foreach ($fields as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => 'general',
                ]
            );
        }

        AuditService::log(
            action: 'settings.updated',
            module: 'settings',
            description: 'General system settings updated'
        );

        return back()->with('success', 'General settings saved successfully!');
    }

    public function updateSmtp(Request $request)
    {
        $fields = $request->except(['_token']);

        foreach ($fields as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => 'smtp',
                ]
            );
        }

        AuditService::log(
            action: 'settings.smtp_updated',
            module: 'settings',
            description: 'SMTP settings updated'
        );

        return back()->with('success', 'Email / SMTP settings updated successfully!');
    }
}
