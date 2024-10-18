<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Html\Builder;

class RolesController extends Controller
{
    public function index(Request $request, Builder $builder)
    {
        $this->hasPermisstion('view');

        $roles = Role::all();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->hasPermisstion('create');

        $permissions = Permission::select(["*"])
            ->selectRaw(" SUBSTRING_INDEX(name,'.',-1) as task ")
            ->selectRaw(" SUBSTRING_INDEX(name,'.',1) as module ")
            ->orderBy('module', 'ASC')
            ->orderBy('task', 'ASC')
            ->get();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->hasPermisstion('create');

        $request->validate([
            'title' => ['required'],
            'name' => ['required', 'string', Rule::unique('roles', 'name')],
        ]);

        $role = new Role();

        $role->title = $request->title;
        $role->name = $request->name;

        $role->save();

        if ($request->exists('permissions')) {
            $role->syncPermissions($request->permissions);

            $staffPermissions = Permission::getStaffPermissions();
            foreach ($staffPermissions as $permission) {
                $role->addPermission($permission);
            }
        }

        alert_message('Role created successfully.', 'success');

        return redirect()->route('admin.roles.edit', $role->id);
    }

    public function edit($id)
    {
        $this->hasPermisstion('edit');

        $role = Role::where('id', $id)->first();

        //$permissions = Permission::withModel()->get();
        $permissions = Permission::select(["*"])
            ->selectRaw(" SUBSTRING_INDEX(name,'.',-1) as task ")
            ->selectRaw(" SUBSTRING_INDEX(name,'.',1) as module ")
            ->orderBy('module', 'ASC')
            ->orderBy('task', 'ASC')
            ->get();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request)
    {
        $this->hasPermisstion('edit');

        $request->validate([
            'title' => ['required'],
        ]);

        $role = Role::where('id', $request->input('id'))->first();

        $role->title = $request->title;

        $role->save();

        if ($request->exists('permissions')) {
            $role->syncPermissions($request->permissions);

            $staffPermissions = Permission::getStaffPermissions();
            foreach ($staffPermissions as $permission) {
                $role->addPermission($permission);
            }
        }

        alert_message('Role updated successfully.', 'success');

        return redirect()->back();
    }

    public function destroy($id)
    {
        $this->hasPermisstion('delete');

        $role = Role::where('id', $id)->first();

        if (Role::isDefaultRole($role)) {
            alert_message("This role cannot delete because its system default role.");
        } elseif ($role->users()->count()) {
            alert_message("This role cannot delete because its allocated to any user", 'error');
        } else {
            $role->delete();

            alert_message('Role deleted successfully.', 'success');
        }

        return redirect()->route('admin.roles.index');
    }

    public function resetPermissions()
    {

        $result = Permission::resetDefault();

        alert_message($result['message'], $result['status']);

        return redirect()->back();
    }
}
