<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppSetting;
use Illuminate\Http\Request;

class WhatsAppSettingsController extends Controller
{
    private const PLACEHOLDERS = ['{order_id}', '{customer_name}', '{items}', '{total}'];

    public function edit()
    {
        return view('admin.settings.whatsapp', ['settings' => WhatsAppSetting::current()]);
    }

    public function update(Request $request)
    {
        $request->merge([
            'order_number' => preg_replace('/[\s\-()]/', '', (string) $request->input('order_number')),
        ]);

        $validated = $request->validate([
            'order_number' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $digits = preg_replace('/\D/', '', (string) $value);
                    if ($digits !== config('honeybee.contact.whatsapp_number')) {
                        $fail('The HoneyBee contact number is fixed by the application contact configuration.');
                    }
                },
            ],
            'message_template' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    preg_match_all('/\{[^}]+\}/', (string) $value, $matches);
                    $unknown = array_diff(array_unique($matches[0]), self::PLACEHOLDERS);
                    if ($unknown !== []) {
                        $fail('The message template contains unsupported placeholders: '.implode(', ', $unknown).'.');
                    }
                },
            ],
        ]);

        WhatsAppSetting::current()->update([
            'message_template' => $validated['message_template'],
        ]);

        return redirect()->route('admin.settings.whatsapp.edit')
            ->with('success', 'WhatsApp order settings saved successfully.');
    }
}
