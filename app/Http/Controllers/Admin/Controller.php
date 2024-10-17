<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $title = '';

    public function __construct()
    {
        $this->setup();
    }

    public function setup()
    {
        view()->share('title', $this->getTitle());
    }

    protected function getTitle()
    {
        if (!$this->title) {
            $class_name = str_replace('Controller', '', class_basename($this));

            $class_name = Str::title(Str::snake($class_name, ' '));
            $action = Route::current()->getActionMethod();
            if ($action == 'index') {
                $title = $class_name . ' List';
            } else {
                $title = ucfirst($action) . ' ' . Str::singular($class_name);
            }

            $this->title = $title;
        }

        return $this->title;
    }

    /**
     * @param $permission
     * @return void
     */
    protected function hasPermisstion($permission = '')
    {
        if (!$permission || !Str::contains($permission, '.')) {
            $route_name = Route::current()->getName();

            if (!$permission) {
                $permission = Route::current()->getActionMethod();
                if ($permission == 'destroy') {
                    $permission = 'delete';
                }
            }

            if (Str::startsWith($route_name, 'admin.')) {
                $route_name = substr($route_name, strlen(''));
            }
            if (Str::contains($route_name, '.')) {
                $route_name = substr($route_name, 0, strrpos($route_name, '.'));
            }

            $permission = trim($route_name, '.') . '.' . $permission;
        }

        if (Str::endsWith($permission, '.destroy')) {
            $permission = str_replace('.destroy', '.delete', $permission);
        }

        if (!hasPermission($permission)) {
            abort('403');
        }
    }
}
