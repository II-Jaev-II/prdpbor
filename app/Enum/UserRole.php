<?php

namespace App\Enum;

enum UserRole: string
{
    case Admin = 'admin';
    case Superior = 'superior';
    case User = 'user';
}
