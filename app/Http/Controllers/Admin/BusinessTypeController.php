<?php

namespace App\Http\Controllers\Admin;

use App\Models\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;

class BusinessTypeController extends Controller
{
    public function index(Request $request, Builder $builder)
    {
        $model = BusinessType::query();

        if ($request->ajax()) {
            return DataTables::eloquent($model)
                ->addColumn('id', function ($model) {
                    return $model->id;
                })
                ->addColumn('name', function ($model) {
                    return $model->name;
                })
                ->addColumn('status', function ($model) {
                    return $model->getStatusHtml();
                })
                ->addColumn('action', function ($model) {
                    return dtButtons([
                        'edit' => [
                            'url' => route("business-type.edit", [$model->id]),
                            'title' => 'Edit',
                            'can' => 'business-types.edit',
                        ],
                        'delete' => [
                            'url' => route("business-type.destroy", [$model->id]),
                            'title' => 'Delete',
                            'can' => 'business-types.delete',
                            'data-method' => 'DELETE',
                        ]
                    ]);
                })->toJson();
        }

        $html = $builder->columns([
            Column::make('id')->title('#'),
            Column::make('name'),
            Column::make('status'),
            Column::make('action')->addClass('text-center')->orderable(false),
        ])->orderBy(1, 'ASC');

        return view('business-types.index', compact('html'));
    }

    public function create()
    {
        $this->hasPermisstion('create');

        return $this->edit(new BusinessType());
    }

    public function store(Request $request)
    {
        $this->hasPermisstion('create');

        $request->validate([
            'name' => ['required', Rule::unique('business_types', 'name')],
        ]);

        $model = new BusinessType();

        $model->name = $request->name;
        $model->status = $request->status ?? 1;
        $model->save();

        alert_message('Business type created successfully.', 'success');

        return redirect()->route('business-types.edit', $model->id);
    }

    public function edit(BusinessType $businessType)
    {
        $this->hasPermisstion('edit');

        return view('business-types.form', [
            'item' => $businessType
        ]);
    }

    public function update(Request $request)
    {
        $this->hasPermisstion('edit');

        $id = $request->input('id');

        $request->validate([
            'name' => ['required', Rule::unique('business_types', 'name')->ignore($id)],
        ]);

        $model = BusinessType::where('id', $request->input('id'))->first();

        $model->name = $request->name;

        $model->status = $request->status ?? 1;

        $model->save();

        alert_message('Recored updated successfully.', 'success');

        return redirect()->back();
    }

    public function destroy($id)
    {
        $this->hasPermisstion('delete');

        $model = BusinessType::where('id', $id)->first();

        if ($model) {
            $model->delete();

            alert_message('Record deleted successfully.', 'success');
        }

        return redirect()->route('business-types.index');
    }
}
