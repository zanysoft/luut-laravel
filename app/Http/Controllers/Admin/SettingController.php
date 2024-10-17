<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        //$this->hasPermisstion('view');

        $settings = Setting::where('status', '1')->get();

        //dd($settings);

        return view('settings.index', compact('settings'));
    }

    public function edit($key)
    {
        $this->hasPermisstion('edit');

        $setting = Setting::where('key', $key)->first();

        $settings = Setting::where('status', '1')->get();

        return view('settings.edit', compact('setting', 'settings'));
    }

    public function update(Request $request)
    {
        $this->hasPermisstion('edit');

        $setting = Setting::where('key', $request->input('key'))->first();

        $setting->values = $request->values;

        $setting->save();

        return redirect()->back();
    }

    public function testEmail(Request $request)
    {
        $email = $request->get('email');
        try {
            $content = '<p style="text-align: center;font-weight: bold;font-size:1.2rem">Test email for checking the mail settings.</p>';

            $default_driver = config('mail.default');
            $mail_configs = config('mail.mailers.' . $default_driver);

            if ($default_driver == 'sendmail') {
                $content .= "<p>Email sent via sendmail driver</p>";
            } elseif ($mail_configs && is_array($mail_configs)) {
                $content .= "<p><b>Email sent via $default_driver </b></p>";
                foreach ($mail_configs as $key => $val) {
                    if (Str::contains($key, 'password')) {
                        $val = mask_string($val);
                    }
                    $content .= "<p>" . $key . ': ' . (is_array($val) ? json_encode($val) : $val) . "</p>";
                }
            }

            Mail::send('emails.general', ['content' => $content], function ($message) use ($email) {
                $message->to($email);
                $message->subject('Test Email');
            });

            return response()->json(['success' => true, 'msg' => 'Email sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => 'Email not sent.<br>Error: ' . $e->getMessage()]);
        }

    }
}
