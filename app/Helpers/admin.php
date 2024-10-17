<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Prologue\Alerts\Facades\Alert;

function isAdminUri()
{

    if (request()->segment(1) == 'admin') {
        return true;
    }

    return false;
}

if (!function_exists('hasPermission')) {
    function hasPermission($roleOrPermission)
    {

        if (Auth::check()) {
            $user = auth()->user();
            $operator = 'or';

            if ($user->hasRole('super-admin')) {
                return true;
            }

            if (!is_array($roleOrPermission)) {
                $roleOrPermission = preg_replace('/[\s\,\&]+/', ',', $roleOrPermission);

                if (Str::contains($roleOrPermission, ',')) {
                    $operator = 'and';
                    $roleOrPermission = preg_replace('/[\,\|]+/', ',', $roleOrPermission);
                    $rolesOrPermissions = explode(',', $roleOrPermission);
                } else {
                    $rolesOrPermissions = explode('|', $roleOrPermission);
                }
            } else {
                $rolesOrPermissions = $roleOrPermission;
            }

            if ($operator == 'and') {
                if ($user->hasAllRoles($rolesOrPermissions)) {
                    return true;
                }
                if ($user->hasAllPermissions($rolesOrPermissions)) {
                    return true;
                }
            } else {
                if ($user->hasAnyRole($rolesOrPermissions)) {
                    return true;
                }
                if ($user->hasAnyPermission($rolesOrPermissions)) {
                    return true;
                }
            }
        }
        return false;
    }
}

/**
 * Ajax Checkbox Display
 *
 * @param $id
 * @param $table
 * @param $field
 * @param null $fieldValue
 * @return string
 */
function ajaxCheckboxDisplay($id, $table, $field, $fieldValue = null, $label = 'Active', $model = null, $disabled = false)
{
    $lineId = $field . $id;
    $lineId = str_replace('.', '', $lineId); // fix JS bug
    $data = 'data-table="' . $table . '"
			data-field="' . $field . '"
			data-line-id="' . $lineId . '"
			data-id="' . $id . '"
			data-value="' . (isset($fieldValue) ? $fieldValue : 0) . '"';

    if ($model) {
        $data .= ' data-model="' . Str::kebab($model) . '" ';
    }

    // Decoration
    if (isset($fieldValue) && $fieldValue == 1) {
        $html = '<i class="icon fa fa-toggle-on" aria-hidden="true"></i>';
    } else {
        $html = '<i class="icon fa fa-toggle-off" aria-hidden="true"></i>';
    }


    if ($label) {
        $type = 'success';
        if (Str::contains(strtolower($label), ['unpublish', 'un-publish', 'draft', 'disable', 'warning'])) {
            $type = 'warning';
        }
        if (Str::contains(strtolower($label), ['inactive', 'disable', 'trash'])) {
            $type = 'danger';
        }
        $label = '<span class="badge badge-' . $type . '">' . $label . '</span>';

        if (Str::contains(strtolower($label), ['trashed', 'deleted', 'trash'])) {
            return $label;
        }
    }

    /*if ($disabled) {
        $html = $html . ' ' . $label;
    } else {
        $html = '<a href="" id="' . $lineId . '" class="ajax-request" ' . $data . '>' . $html . ' ' . $label . '</a>';
    }*/

    if ($disabled) {
        $html = $label ?: $html;
    } else {
        $html = '<a href="" id="' . $lineId . '" class="ajax-request" ' . $data . '>' . ($label ?: $html) . '</a>';
    }

    return $html;
}


if (!function_exists('alert_message')) {
    /**
     * @param $message
     * @param string $status
     */
    function alert_message($message, $status = 'error')
    {
        if (!in_array($status, config('prologue.alerts.levels'))) {
            $status = 'error';
        }

        \Alert::$status($message)->flash();
    }
}

if (!function_exists('show_alert')) {
    /**
     * @param $message
     * @param string $status
     */
    function show_alert()
    {
        $_messages = Alert::getMessages();

        $errors = request()->session()->get('errors');
        if ($errors && $errors->any()) {
            foreach ($errors->all() as $error) {
                if (!empty($error)) {
                    $_messages['error'][] = $error;
                }
            }
            request()->session()->forget(['errors']);
        }

        $html = '';
        foreach ($_messages as $status => $messages) {
            $id = $status == 'error' ? 'errors' : $status;
            if ($status == 'error') {
                $status = 'danger';
            }
            $html .= '<div class="alert alert-' . $status . '" id="' . $id . '">';
            $html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            if (count($messages) > 1) {
                $html .= '<ul>';
                foreach ($messages as $msg) {
                    $html .= '<li>' . $msg . '</li>';
                }
                $html .= '</ul>';
            } else {
                $html .= array_shift($messages);
            }

            $html .= '</div>';
        }

        Alert::flush();

        return $html;
    }
}


/**
 * @param $task
 * @param $url
 * @param array $data
 * @return string
 */
function dtButton($task, $url, array $data = [])
{
    $viewData = [
        'action' => $task,
        'url' => $url,
        'dropdown' => $data['dropdown'] ?? false,
        'label' => $data['label'] ?? '',
        'icon' => trim_word(($data['icon'] ?? ''), 'fa-', 'l'),
    ];

    $permission = $data['permission'] ?? $data['can'] ?? null;
    $role = $data['role'] ?? null;

    if ($role && !auth()->user()->hasRole($role)) {
        return false;
    }

    if ($permission && !auth()->user()->hasPermissionTo($permission)) {
        return false;
    }

    $remove_attributes = ['action', 'task', 'dropdown', 'label', 'icon', 'url', 'hide', 'can', 'permission'];

    if (!empty($data)) {
        foreach ($remove_attributes as $var) {
            unset($data[$var]);
        }
        $viewData['attributes'] = $data;
    }

    return view("vendor.datatables.actions", $viewData)->render();
}


/**
 * @param array $links
 * @param bool $dropdown
 * @return string
 */
function dtButtons(array $links, bool $dropdown = false): string
{
    $html = '';
    $links_count = count($links);
    $links = array_filter($links, function ($a) {
        $can = $a['can'] ?? $a['permission'] ?? null;
        $hide = $a['hide'] ?? false;
        $role = $a['role'] ?? false;
        return !$hide && (!$can || auth()->user()->hasPermissionTo($can)) && (!$role || auth()->user()->hasRole($role));
    });

    if ($dropdown && $links_count == 1) {
        $dropdown = false;
    }

    $remove_vars = ['task', 'action', 'url', 'hide', 'can', 'role', 'permission'];

    foreach ($links as $task => $data) {

        if (!in_array($task, ['delete', 'edit', 'view', 'revise', 'send', 'resend'])) {
            $task = ($data['task'] ?? $data['action'] ?? $task);
        }

        $url = ($data['url'] ?? '#');
        $data['dropdown'] = $dropdown;

        foreach ($remove_vars as $var) {
            unset($data[$var]);
        }

        $_html = dtButton($task, $url, $data);
        if ($_html) {
            $html .= $_html;
        }
    }

    if ($html && $dropdown) {
        return '<div class="btn-group dropdown"><button type="button" class="btn btn-xs btn-secondary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
                  <div class="dropdown-menu dropdown-menu-right">' . $html . '</div></div>';
    }

    return $html;
}

/**
 * @param $str
 * @param $word
 * @param string $position
 * @return false|string
 */
function trim_word($str, $word, $position = 'b')
{
    $length = strlen($word);
    if (in_array($position, ['b', 'l', 'both', 'left'])) {
        if ($length > 0 && substr($str, 0, $length) === (string)$word) {
            $str = $str = trim_word(substr($str, $length), $word, $position);
        }
    }
    if (in_array($position, ['b', 'r', 'both', 'right'])) {
        if ($length > 0 && substr($str, -$length) === (string)$word) {
            $str = trim_word(substr($str, 0, -$length), $word, $position);
        }
    }

    return $str;
}

if (!function_exists('random_str')) {
    function random_str($length = 6, $keyspace = '')
    {
        $keyspace = $keyspace ? $keyspace : '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces [] = $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }
}


if (!function_exists('unique_number')) {
    /**
     * @return string
     */
    function unique_number()
    {
        list($usec, $sec) = explode(" ", microtime());

        return $sec . str_replace('.', '', $usec);
    }
}

function isActive($modules, $tasks = null)
{
    $_module = Request::segment(2);
    $_task = Request::segment(3);
    if (is_numeric($_task)) {
        $_task = Request::segment(4);
    }

    if (!$_task) {
        $_task = 'list';
    }

    if (!is_array($modules)) {
        $modules = explode(',', $modules);
    }

    if ($tasks) {
        if (!is_array($tasks)) {
            $tasks = explode(',', $tasks);
        }
        if (in_array($_module, $modules) && in_array($_task, $tasks)) {
            return ' active';
        }
    } else {
        if (in_array($_module, $modules)) {
            return 'active';
        }
    }

    return '';
}
