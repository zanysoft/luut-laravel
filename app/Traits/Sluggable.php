<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Sluggable
{
    protected string $nameColumn = 'name';

    protected string $slugColumn = 'slug';

    protected bool $generateUniqueSlug = true;

    protected $preventOverwriteSlug = true;

    public static function bootSluggable(): void
    {
        static::creating(function (Model $model) {
            $model->generateSlugOnCreate($model);
        });

        static::updating(function (Model $model) {
            $model->generateSlugOnUpdate($model);
        });
    }

    protected function scopeSlug($query, string $slug)
    {
        $query->where($this->getSlugColumn(), $slug);
    }

    protected function generateSlugOnCreate(): void
    {
        $slugColumn = $this->getSlugColumn();

        if ($this->preventOverwriteSlug) {
            if ($this->{$slugColumn} !== null) {
                return;
            }
        }

        $this->addSlug();
    }

    protected function generateSlugOnUpdate(Model $model): void
    {
        $slugColumn = $model->getSlugColumn();

        if ($this->preventOverwriteSlug) {
            if ($model->{$slugColumn} !== null) {
                return;
            }
        }

        $this->addSlug();
    }


    protected function addSlug(): void
    {
        $slugColumn = $this->getSlugColumn();
        $nameColumn = $this->getNameColumn();

        $slug = Str::slug($this->{$nameColumn});;

        if ($this->generateUniqueSlug) {
            $slug = $this->makeSlugUnique($slug);
        }

        $this->{$slugColumn} = $slug;
    }

    protected function makeSlugUnique(string $slug): string
    {
        $originalSlug = $slug;
        $i = 1;

        while ($this->recordExistsWithSlug($slug) || $slug === '') {
            $slug = $originalSlug . '-' . $i++;
        }

        return $slug;
    }

    protected function recordExistsWithSlug(string $slug): bool
    {
        $query = static::where($this->getSlugColumn(), $slug)
            ->withoutGlobalScopes();

        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        if ($this->usesSoftDeletes()) {
            $query->withTrashed();
        }

        return $query->exists();
    }

    protected function usesSoftDeletes(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this), true);
    }


    protected function getNameColumn(): string
    {
        return $this->nameColumn;
    }

    protected function getSlugColumn(): string
    {
        return $this->slugColumn;
    }
}
