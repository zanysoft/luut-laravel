<?php

namespace App\Observers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class SettingObserver
{
    /**
     * Handle the Setting "created" event.
     */
    public function created(Setting $setting): void
    {
        // ...
    }

    /**
     * Handle the Setting "updated" event.
     */
    public function updating(Setting $setting): void
    {
        $original = $setting->getOriginal();

        $this->clearCache();

        $this->updateRobotsTxt($setting);

        $this->checkDotEnvEntries($setting, $original);
    }

    /**
     * Handle the Setting "deleted" event.
     */
    public function saved(Setting $setting): void
    {
        $this->clearCache();
    }

    protected function clearCache()
    {
        Cache::forget('settings');

    }

    /**
     * @param $setting
     * @param $original
     */
    public function checkDotEnvEntries($setting, $original)
    {
        $original_value = $original['values'];
        $new_value = $setting->values;
        $key = $setting->key;

        /**
         * @var $file \Jackiedo\DotenvEditor\DotenvEditor
         */
        $file = DotenvEditor::load();

        $isChanged = false;

        $array = [];

        try {

            if ($key == 'app') {
                $app_mapping = [
                    'APP_NAME' => 'name',
                ];
                foreach ($app_mapping as $env => $key) {
                    $new_val = data_get($new_value, $key);
                    $old_val = data_get($original_value, $key);
                    $env_value = $file->keyExists($env) ? DotenvEditor::getValue($env) : '';
                    if ($old_val != $new_val || $env_value != $new_val || !$file->keyExists($env)) {
                        $file->setKey($env, ($new_val ? $new_val : null), 'Change from admin panel');
                        $isChanged = true;
                    }
                }
            }

            if ($key == 'mail') {
                $mail_mapping = [
                    'MAIL_MAILER' => 'driver',
                    'MAIL_HOST' => 'host',
                    'MAIL_PORT' => 'port',
                    'MAIL_USERNAME' => 'username',
                    'MAIL_ENCRYPTION' => 'encryption',
                    'MAIL_FROM_ADDRESS' => 'from_email',
                ];

                foreach ($mail_mapping as $env => $key) {
                    $new_val = data_get($new_value, $key);
                    $old_val = data_get($original_value, $key);
                    $env_value = $file->keyExists($env) ? DotenvEditor::getValue($env) : '';
                    if ($old_val != $new_val || $env_value != $new_val || !$file->keyExists($env)) {
                        $file->setKey($env, ($new_val ? $new_val : null), 'Change from admin panel');
                        $isChanged = true;
                    }
                }
            }
            
            if ($isChanged) {
                $file->save();
            }
        } catch (\Exception $e) {
        }

    }

    /**
     * @param $setting
     * @param $original
     */
    public function updateRobotsTxt($setting)
    {
        try {
            if ($setting->key == 'seo') {
                $path = public_path('robots.txt');
                $old_content = file_get_contents($path);
                $new_content = data_get($setting->values, 'robots_txt');
                if (preg_replace('/\s+/', '', $new_content) != preg_replace('/\s+/', '', $old_content)) {
                    $file = fopen($path, "w");
                    fwrite($file, $new_content);
                    fclose($file);
                }
            }
        } catch (\Exception $e) {
            //alert_message($e->getMessage());
        }
    }
}
