<?php

namespace App\Enums;

enum SearchTypeEnum: string
{
    case PEOPLE = 'people';
    case MOVIE = 'movie';

    /**
     * Retorna todos os valores do enum (útil para validação)
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Valida se o valor é um tipo válido
     */
    public static function isValid(string $type): bool
    {
        return in_array($type, self::values(), true);
    }
}

