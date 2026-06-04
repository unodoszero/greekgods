<?php

namespace App\Http\Requests;

use App\Support\BodyMetricConverter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProfileBodyMetricsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $heightValue = $this->normalizedInput('height_value', 'height');
        $heightUnit = $this->normalizedInput('height_unit', 'heightMetrics') ?? 'm';
        $weightValue = $this->normalizedInput('weight_value', 'weight');
        $weightUnit = $this->normalizedInput('weight_unit', 'weightMetrics') ?? 'kg';

        $this->merge([
            'sex' => $this->filled('sex') ? $this->input('sex') : null,
            'height_value' => $heightValue,
            'height_unit' => $heightUnit,
            'weight_value' => $weightValue,
            'weight_unit' => $weightUnit,
            'height' => BodyMetricConverter::heightToMeters($heightValue, $heightUnit),
            'weight' => BodyMetricConverter::weightToKilograms($weightValue, $weightUnit),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'birthdate' => ['required', 'date', 'before_or_equal:2011-01-01', 'after_or_equal:1923-01-01'],
            'height_value' => ['required', 'numeric', 'min:0'],
            'height_unit' => ['required', 'in:cm,m,in,ft'],
            'weight_value' => ['required', 'numeric', 'min:0'],
            'weight_unit' => ['required', 'in:kg,lb'],
            'activity' => ['required', 'in:sedentary,light,moderate,active,very_active'],
            'sex' => ['nullable', 'in:male,female,prefer_not_to_say'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $validator->errors()->has('height_value') && ! $validator->errors()->has('height_unit')) {
                $this->validateConvertedHeight($validator);
            }

            if (! $validator->errors()->has('weight_value') && ! $validator->errors()->has('weight_unit')) {
                $this->validateConvertedWeight($validator);
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'height_value' => 'height',
            'height_unit' => 'height unit',
            'weight_value' => 'weight',
            'weight_unit' => 'weight unit',
        ];
    }

    private function normalizedInput(string $field, string $fallbackField): ?string
    {
        $value = $this->input($field, $this->input($fallbackField));

        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function validateConvertedHeight(Validator $validator): void
    {
        $height = $this->input('height');

        if ($height === null) {
            $validator->errors()->add(
                'height_value',
                'For ft, type 5.7 for 5 ft 7 in. Inches must be 0 through 11.'
            );

            return;
        }

        if ($height < 0.3 || $height > 2.7) {
            $validator->errors()->add(
                'height_value',
                'Height must convert to between 0.3 and 2.7 meters.'
            );
        }
    }

    private function validateConvertedWeight(Validator $validator): void
    {
        $weight = $this->input('weight');

        if ($weight === null || $weight < 20 || $weight > 500) {
            $validator->errors()->add(
                'weight_value',
                'Weight must convert to between 20 and 500 kilograms.'
            );
        }
    }
}
