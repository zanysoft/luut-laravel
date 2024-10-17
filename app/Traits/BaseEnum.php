<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait BaseEnum
{
    public static function toArray(): array
    {
        return collect(self::cases())->map(function ($item) {
            $name = preg_replace('/([^A-Z_])(?=[A-Z])/u', '$1_', $item->name);

            return [
                'value' => $item->value,
                'name' => Str::replace('_', ' ', $name),
            ];
        })->toArray();
    }

    public static function toArrayByValue(): array
    {
        $list = [];

        foreach (self::cases() as $case) {
            $name = preg_replace('/([^A-Z_])(?=[A-Z])/u', '$1_', $case->name);
            switch ($name) {
                case 'Pending':
                    $name = 'Provisional';
                    break;
                case 'Complete':
                    $name = 'Confirmed';
                    break;
                case 'Failed':
                    $name = 'Rejected';
                    break;
            }
            $list[$case->value] = $name;
        }

        return $list;
    }

    public static function getValues(): array
    {
        return collect(self::cases())->map(function ($item) {
            return $item->value;
        })->toArray();
    }

    public static function getNames(): array
    {
        return collect(self::cases())->map(function ($item) {
            return static::titleCase($item->name);
        })->toArray();
    }

    public static function toString($name = false): string
    {
        return collect(self::cases())->map(function ($item) use ($name) {
            return $name ? static::titleCase($item->name) : $item->value;
        })->join(', ');
    }

    protected static function titleCase($str)
    {
        return preg_replace('/([^A-Z_])(?=[A-Z])/u', '$1_', $str);
    }
}
