<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait ActiveTrait
{

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 1);
    }

    public function getActiveHtml($column = null, $label = false)
    {
        if (is_array($column)) {
            $disabled = isset($column['disabled']) ? $column['disabled'] : false;
            $label = isset($column['label']) ? $column['label'] : $label;
            $column = $column['column'] ?? 'status';
        } else {
            $disabled = false;
            if (!$column) {
                $column = 'status';
            }
        }

        if (!isset($this->{$column})) return false;

        $model = class_basename($this);
        if ($model && Str::contains($model, ['Model'])) {
            $model = str_replace(['Models', 'Model'], '', $model);
        }
        $value = $this->{$column};
        $id = $this->{$this->primaryKey};
        $table = $this->getTable();

        if (!$disabled) {
            switch ($table) {
                case 'users':
                    $disabled = $this->type =='admin' && !auth()->user()->checkPermissionTo('users.edit');
                    break;
            }
        }

        return ajaxCheckboxDisplay($id, $table, $column, $value, $label, $model, $disabled);
    }
}
