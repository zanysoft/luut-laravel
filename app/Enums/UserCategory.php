<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum UserCategory: string
{
    use BaseEnum;

    case Admin = 'admin';
    case Business = 'business';
    case User = 'user';
}
