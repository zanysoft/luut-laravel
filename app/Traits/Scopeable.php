<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Core\Enums\FeaturedEnum;
use Modules\Core\Enums\TrendingModulesEnum;
use Modules\Core\Traits\FeaturedScope;

trait Scopeable
{
    public static $withoutAppends = false;

    /**
     * Scope Filters to get trashed or withoutTrashed data
     *
     * @return void
     */
    public function scopeTrashed(Builder $query, ?array $filters = null)
    {
        $trashed = is_array($filters) ? ($filters['trashed'] ?? '') : $filters;
        if (! is_null($trashed)) {
            switch ($trashed) {
                case 'with':
                    $query->withTrashed();
                    break;
                case 'only':
                    $query->onlyTrashed();
                    break;
                case 'without':
                    $query->withoutTrashed();
                    break;
                default:
                    $query->where('status', $trashed);
            }
        }
    }

    /** Sorting scope filter for all models */
    public function scopeSort($query, Request $request)
    {
        $query->when(request()->has(['order', 'sort']), function ($query) use ($request) {
            $query->orderBy($request['sort'], $request['order']);
        });

        $query->orderByDESC('id');
    }

    /**
     * @return void
     */
    public function scopeActive(Builder $query)
    {
        $query->where('status', 1);
    }

    /**
     * @return void
     */
    public function scopeInActive(Builder $query)
    {
        $query->where('status', 0);
    }

    /**
     * @return void
     */
    public function scopeByRelevance(Builder $query, $keyword, array|string $columns = 'name')
    {
        $keyword = trim($keyword);

        if (! is_array($columns)) {
            $columns = array_map('trim', explode(',', $columns));
        }

        if ($keyword && ! empty($columns)) {
            $table = $this->getTable();
            $relevance = 10;
            $_select = [];
            $bindings = [];

            $words_tab = preg_split('/[\s,\+]+/', $keyword);

            foreach ($columns as $column) {
                $_select[] = "(CASE WHEN $table.$column LIKE ? THEN 300 ELSE 0 END) ";
                $bindings[] = $keyword.'%';
                $_select[] = "(CASE WHEN $table.$column LIKE ? THEN 200 ELSE 0 END) ";
                $bindings[] = '%'.$keyword.'%';

                if (count($words_tab) > 1) {
                    $_tmp = [];
                    foreach ($words_tab as $word) {
                        $_tmp[] = " $table.$column LIKE ? ";
                        $bindings[] = '%'.$word.'%';
                    }

                    if (count($_tmp) > 0) {
                        $_select[] = '(CASE WHEN '.implode(' || ', $_tmp).' THEN '.$relevance.' ELSE 0 END) ';
                    }
                }
            }

            if (count($_select) > 0) {
                $query->selectRaw(' ('.implode("+\n", $_select).') as relevance', $bindings);
                $query->orderByRaw('relevance DESC');
            }
            //dd($query->toRawSql());
        }
    }

    /**
     * @return Builder
     */
    public function scopeWithoutAppends(Builder $query)
    {
        self::$withoutAppends = true;

        return $query;
    }

    /**
     * Regarding scopeWithoutAppends
     *
     * @return array
     */
    protected function getArrayableAppends()
    {
        if (self::$withoutAppends) {
            return [];
        }

        return parent::getArrayableAppends();
    }
}
