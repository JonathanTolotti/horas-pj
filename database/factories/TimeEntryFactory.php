<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-1 month', 'now');
        $startHour = $this->faker->numberBetween(8, 14);
        $duration = $this->faker->randomFloat(1, 1, 8);
        $endHour = $startHour + floor($duration);
        $endMinute = ($duration - floor($duration)) * 60;

        return [
            'user_id' => \App\Models\User::factory(),
            'date' => $date->format('Y-m-d'),
            'start_time' => sprintf('%02d:00', $startHour),
            'end_time' => sprintf('%02d:%02d', $endHour, $endMinute),
            'hours' => $duration,
            'description' => $this->faker->randomElement([
                'Desenvolvimento de features',
                'Code review e testes',
                'Reunião com cliente',
                'Correção de bugs',
                'Refatoração de código',
                'Documentação técnica',
                'Deploy e configuração',
                'Análise de requisitos',
            ]),
            'month_reference' => $date->format('Y-m'),
        ];
    }
}
