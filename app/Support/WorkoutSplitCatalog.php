<?php

namespace App\Support;

class WorkoutSplitCatalog
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'full-body' => [
                'label' => 'Full Body',
                'shortLabel' => 'FB',
                'description' => 'Train the whole body each session with rest days between hard efforts.',
                'schedules' => [
                    '2-day' => [
                        'label' => '2 day schedule',
                        'summary' => 'Low frequency, simple recovery.',
                        'days' => self::week(['Monday' => 'Full Body', 'Thursday' => 'Full Body']),
                    ],
                    '3-day-eod' => [
                        'label' => '3 day every-other-day',
                        'summary' => 'Classic Monday, Wednesday, Friday rhythm.',
                        'days' => self::week(['Monday' => 'Full Body', 'Wednesday' => 'Full Body', 'Friday' => 'Full Body']),
                    ],
                    '4-day' => [
                        'label' => '4 day schedule',
                        'summary' => 'Higher practice frequency with the weekend open.',
                        'days' => self::week(['Monday' => 'Full Body A', 'Tuesday' => 'Full Body B', 'Thursday' => 'Full Body A', 'Friday' => 'Full Body B']),
                    ],
                ],
            ],
            'upper-lower' => [
                'label' => 'Upper / Lower',
                'shortLabel' => 'UL',
                'description' => 'Alternate upper and lower body sessions for balanced weekly volume.',
                'schedules' => [
                    '3-day' => [
                        'label' => '3 day schedule',
                        'summary' => 'A lighter upper/lower rotation.',
                        'days' => self::week(['Monday' => 'Upper', 'Wednesday' => 'Lower', 'Friday' => 'Upper']),
                    ],
                    '4-day' => [
                        'label' => '4 day schedule',
                        'summary' => 'Most common upper/lower setup.',
                        'days' => self::week(['Monday' => 'Upper', 'Tuesday' => 'Lower', 'Thursday' => 'Upper', 'Friday' => 'Lower']),
                    ],
                    '5-day' => [
                        'label' => '5 day schedule',
                        'summary' => 'Extra upper emphasis for more weekly volume.',
                        'days' => self::week(['Monday' => 'Upper', 'Tuesday' => 'Lower', 'Wednesday' => 'Upper', 'Friday' => 'Lower', 'Saturday' => 'Upper']),
                    ],
                ],
            ],
            'ppl' => [
                'label' => 'Push / Pull / Legs',
                'shortLabel' => 'PPL',
                'description' => 'Split training by movement pattern: push, pull, and legs.',
                'schedules' => [
                    '3-day' => [
                        'label' => '3 day schedule',
                        'summary' => 'One push, pull, and legs session per week.',
                        'days' => self::week(['Monday' => 'Push', 'Wednesday' => 'Pull', 'Friday' => 'Legs']),
                    ],
                    '6-day' => [
                        'label' => '6 day schedule',
                        'summary' => 'Repeat PPL twice, with one rest day.',
                        'days' => self::week(['Monday' => 'Push', 'Tuesday' => 'Pull', 'Wednesday' => 'Legs', 'Thursday' => 'Push', 'Friday' => 'Pull', 'Saturday' => 'Legs']),
                    ],
                ],
            ],
            'pplul' => [
                'label' => 'PPL / Upper / Lower',
                'shortLabel' => 'PPLUL',
                'description' => 'A 5-day hybrid that blends PPL with upper/lower frequency.',
                'schedules' => [
                    '5-day' => [
                        'label' => '5 day schedule',
                        'summary' => 'Push, Pull, Legs, Upper, Lower.',
                        'days' => self::week(['Monday' => 'Push', 'Tuesday' => 'Pull', 'Wednesday' => 'Legs', 'Friday' => 'Upper', 'Saturday' => 'Lower']),
                    ],
                ],
            ],
            'ppl-upper-sharms' => [
                'label' => 'PPL / Upper / ShArms',
                'shortLabel' => 'PPLUS',
                'description' => 'A 5-day hybrid that blends PPL, upper body, and focused shoulders and arms.',
                'schedules' => [
                    '5-day' => [
                        'label' => '5 day schedule',
                        'summary' => 'Push, Pull, Legs, Upper, ShArms.',
                        'days' => self::week(['Monday' => 'Push', 'Tuesday' => 'Pull', 'Wednesday' => 'Legs', 'Friday' => 'Upper', 'Saturday' => 'ShArms']),
                    ],
                ],
            ],
            'arnold' => [
                'label' => 'Arnold Split',
                'shortLabel' => 'AP',
                'description' => 'Chest/back, shoulders/arms, and legs in an Arnold-style rotation.',
                'schedules' => [
                    '3-day' => [
                        'label' => '3 day schedule',
                        'summary' => 'Run each Arnold day once per week.',
                        'days' => self::week(['Monday' => 'Chest + Back', 'Wednesday' => 'Shoulders + Arms', 'Friday' => 'Legs']),
                    ],
                    '6-day' => [
                        'label' => '6 day schedule',
                        'summary' => 'Repeat the Arnold rotation twice.',
                        'days' => self::week(['Monday' => 'Chest + Back', 'Tuesday' => 'Shoulders + Arms', 'Wednesday' => 'Legs', 'Thursday' => 'Chest + Back', 'Friday' => 'Shoulders + Arms', 'Saturday' => 'Legs']),
                    ],
                ],
            ],
            'bro-split' => [
                'label' => 'Body Part Split',
                'shortLabel' => 'BRO',
                'description' => 'Focus each session on one major muscle group or region.',
                'schedules' => [
                    '5-day' => [
                        'label' => '5 day schedule',
                        'summary' => 'One focused body-part session per weekday.',
                        'days' => self::week(['Monday' => 'Chest', 'Tuesday' => 'Back', 'Wednesday' => 'Legs', 'Thursday' => 'Shoulders', 'Friday' => 'Arms + Abs']),
                    ],
                    '6-day' => [
                        'label' => '6 day schedule',
                        'summary' => 'Adds a dedicated core day.',
                        'days' => self::week(['Monday' => 'Chest', 'Tuesday' => 'Back', 'Wednesday' => 'Legs', 'Thursday' => 'Shoulders', 'Friday' => 'Arms', 'Saturday' => 'Abs']),
                    ],
                ],
            ],
        ];
    }

    public static function normalizeSplit(?string $split): ?string
    {
        $aliases = [
            'push-pull-legs' => 'ppl',
            'ppl-ul' => 'pplul',
            'ppl/ul' => 'pplul',
            'ppl-upper-sharms-split' => 'ppl-upper-sharms',
            'ppl/us' => 'ppl-upper-sharms',
            'pplus' => 'ppl-upper-sharms',
            'ap' => 'arnold',
            'arnold-program' => 'arnold',
        ];

        $split = trim((string) $split);
        $split = $aliases[$split] ?? $split;

        return array_key_exists($split, self::all()) ? $split : null;
    }

    public static function normalizeSchedule(string $split, ?string $schedule): ?string
    {
        $schedule = trim((string) $schedule);
        $schedules = self::all()[$split]['schedules'] ?? [];

        if (array_key_exists($schedule, $schedules)) {
            return $schedule;
        }

        $normalizedLabel = strtolower(preg_replace('/\s+/', ' ', $schedule) ?? $schedule);

        foreach ($schedules as $id => $definition) {
            $label = strtolower((string) $definition['label']);
            if ($normalizedLabel === $label) {
                return $id;
            }
        }

        $legacyAliases = [
            'full-body' => [
                '3 day schedule' => '3-day-eod',
            ],
            'bro-split' => [
                '5 day body part split' => '5-day',
                '6 day body part split' => '6-day',
            ],
        ];

        return $legacyAliases[$split][$normalizedLabel] ?? null;
    }

    /**
     * @return array<string, string>|null
     */
    public static function daysFor(?string $split, ?string $schedule): ?array
    {
        $split = self::normalizeSplit($split);
        if ($split === null) {
            return null;
        }

        $schedule = self::normalizeSchedule($split, $schedule);
        if ($schedule === null) {
            return null;
        }

        return self::all()[$split]['schedules'][$schedule]['days'];
    }

    public static function isTrainingDay(string $split, string $schedule, string $day): bool
    {
        $days = self::daysFor($split, $schedule);

        return isset($days[$day]) && $days[$day] !== 'Rest';
    }

    /**
     * @return array<string, string>
     */
    private static function week(array $trainingDays): array
    {
        $days = [
            'Monday' => 'Rest',
            'Tuesday' => 'Rest',
            'Wednesday' => 'Rest',
            'Thursday' => 'Rest',
            'Friday' => 'Rest',
            'Saturday' => 'Rest',
            'Sunday' => 'Rest',
        ];

        foreach ($trainingDays as $day => $label) {
            $days[$day] = $label;
        }

        return $days;
    }
}
