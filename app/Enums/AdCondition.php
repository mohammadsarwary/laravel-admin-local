<?php

namespace App\Enums;

enum AdCondition: string
{
    case NEW = 'new';
    case LIKE_NEW = 'like_new';
    case GOOD = 'good';
    case FAIR = 'fair';
    case POOR = 'poor';

    public function getLabel(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::LIKE_NEW => 'Like New',
            self::GOOD => 'Good',
            self::FAIR => 'Fair',
            self::POOR => 'Poor',
        };
    }

    public static function values(): string
    {
        return implode(',', array_map(fn ($case) => $case->value, self::cases()));
    }

    public static function options(): array
    {
        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->getLabel(),
        ], self::cases());
    }
}
