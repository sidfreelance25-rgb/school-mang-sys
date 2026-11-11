<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Principal = 'principal';
    case Teacher = 'teacher';
    case Accountant = 'accountant';
    case Student = 'student';
    case Parent = 'parent';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'School Admin',
            self::Principal => 'Principal',
            self::Teacher => 'Teacher',
            self::Accountant => 'Accountant',
            self::Student => 'Student',
            self::Parent => 'Parent',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
