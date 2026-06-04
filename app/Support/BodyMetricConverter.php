<?php

namespace App\Support;

class BodyMetricConverter
{
    public const HEIGHT_UNITS = ['cm', 'm', 'in', 'ft'];

    public const WEIGHT_UNITS = ['kg', 'lb'];

    public static function heightToMeters(?string $value, ?string $unit): ?float
    {
        if (! is_numeric($value) || ! in_array($unit, self::HEIGHT_UNITS, true)) {
            return null;
        }

        $height = (float) $value;

        return match ($unit) {
            'cm' => $height / 100,
            'm' => $height,
            'in' => $height * 0.0254,
            'ft' => self::feetShorthandToInches($value) === null
                ? null
                : self::feetShorthandToInches($value) * 0.0254,
        };
    }

    public static function weightToKilograms(?string $value, ?string $unit): ?float
    {
        if (! is_numeric($value) || ! in_array($unit, self::WEIGHT_UNITS, true)) {
            return null;
        }

        $weight = (float) $value;

        return match ($unit) {
            'kg' => $weight,
            'lb' => $weight * 0.45359237,
        };
    }

    public static function feetShorthandToInches(string $value): ?int
    {
        if (! preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
            return null;
        }

        [$feet, $inches] = array_pad(explode('.', $value, 2), 2, '0');
        $feet = (int) $feet;
        $inches = (int) $inches;

        if ($inches >= 12) {
            return null;
        }

        return ($feet * 12) + $inches;
    }
}
