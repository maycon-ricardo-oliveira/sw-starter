<?php

namespace App\Enums\People;

enum PeopleGenderEnum: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case NA = 'n/a';
    case UNKNOWN = 'unknown';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $type): bool
    {
        return in_array($type, self::values(), true);
    }

}
