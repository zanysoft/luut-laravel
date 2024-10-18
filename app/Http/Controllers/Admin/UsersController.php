<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;

class UsersController extends Controller
{
    public function index(Request $request, Builder $builder)
    {
        $model = User::query();

        if ($request->ajax()) {
            return DataTables::eloquent($model)
                ->editColumn('avatar', function ($model) {
                    return '<img src="' . $model->avatar_url . '" class="thumb" />';
                })
                ->addColumn('name', function ($model) {
                    return $model->name;
                })
                ->addColumn('action', function ($model) {
                    return dtButtons([
                        'edit' => [
                            'url' => route("admin.users.edit", [$model->id]),
                            'title' => 'Edit User',
                            'can' => 'users.edit',
                        ],
                        'delete' => [
                            'url' => route("admin.users.destroy", [$model->id]),
                            'title' => 'Delete User',
                            'can' => 'users.delete',
                            'data-method' => 'DELETE',
                        ]
                    ]);
                })->toJson();
        }

        $html = $builder->columns([
            Column::make('avatar')->title('')->orderable(false),
            Column::make('name'),
            Column::make('email'),
            Column::make('action')->addClass('text-center')->orderable(false),
        ])->orderBy(1, 'ASC');

        return view('admin.users.index', compact('html'));
    }

    public function create()
    {
        $this->hasPermisstion('create');

        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->hasPermisstion('create');

        $request->validate([
            'first_name' => ['required'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'username' => ['nullable', 'min:6', Rule::unique('users', 'username')],
            'password' => ['required', 'min:6'],
        ]);

        $user = new User();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);

        $user->gender = $request->gender;
        $user->dob = $request->date_of_birth;

        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->country_code = $request->country_code ?? 'UK';
        $user->zipcode = $request->zipcode;
        $user->status = $request->status ?? 1;

        if ($request->hasFile('avatar')) {
            $user->avatar = $request->file('avatar');
        }

        $user->save();

        if ($request->exists('roles')) {
            $user->syncRoles($request->roles);
        }

        alert_message('User created successfully.', 'success');

        return redirect()->route('admin.users.edit', $user->id);
    }

    public function edit($id)
    {
        $this->hasPermisstion('edit');

        $user = User::where('id', $id)->first();

        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request)
    {
        $this->hasPermisstion('edit');

        $id = $request->input('id');

        $request->validate([
            'first_name' => ['required'],
            'password' => ['nullable', 'min:6'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'username' => ['nullable', 'min:5', Rule::unique('users', 'username')->ignore($id)],
        ]);

        $user = User::where('id', $request->input('id'))->first();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->username = $request->username;

        $user->gender = $request->gender;
        $user->dob = $request->date_of_birth;

        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->country_code = $request->country_code ?? 'UK';
        $user->zipcode = $request->zipcode;
        $user->status = $request->status ?? 1;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $user->avatar = $request->file('avatar');
        }

        $user->save();

        if ($request->exists('roles')) {
            $user->syncRoles($request->roles);
        }

        alert_message('User updated successfully.', 'success');

        return redirect()->back();
    }

    public function destroy($id)
    {
        $this->hasPermisstion('delete');

        $auth = Auth::user();
        $user = User::where('id', $id)->first();

        if ($user->id == $auth->id) {
            alert_message("You cannot delete yourself.");
        } elseif ($user->hasRole('super-admin') && !$auth->hasRole('super-admin')) {
            alert_message("You cannot delete super admin");
        } else {
            $user->delete();

            alert_message('User deleted successfully.', 'success');
        }
        return redirect()->route('admin.users.index');
    }
}
