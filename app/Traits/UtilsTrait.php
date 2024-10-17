<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

trait UtilsTrait
{
    /** Make created at field human-readable */
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : $value
        );
    }

    /** Make created at field human-readable */
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : $value
        );
    }

    public function getStatusHtml(){
        return $this->status ?
            '<span class="badge badge-success">Active</span>' :
            '<span class="badge badge-danger">InActive</span>';
    }

    public function hasSoftDeletes(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this), true);
    }
}
