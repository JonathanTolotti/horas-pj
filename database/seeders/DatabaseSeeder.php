<?php

namespace Database\Seeders;

use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create main user
        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create sample time entries for current month
        $currentMonth = Carbon::now();
        $entries = [
            [
                'date' => $currentMonth->copy()->startOfMonth()->addDays(1),
                'start_time' => '09:00',
                'end_time' => '12:00',
                'hours' => 3,
                'description' => 'Desenvolvimento de features',
            ],
            [
                'date' => $currentMonth->copy()->startOfMonth()->addDays(1),
                'start_time' => '13:00',
                'end_time' => '18:00',
                'hours' => 5,
                'description' => 'Code review e testes',
            ],
            [
                'date' => $currentMonth->copy()->startOfMonth()->addDays(2),
                'start_time' => '09:00',
                'end_time' => '13:30',
                'hours' => 4.5,
                'description' => 'Reuniao com cliente e ajustes',
            ],
            [
                'date' => $currentMonth->copy()->startOfMonth()->addDays(3),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'hours' => 2,
                'description' => 'Correcao de bugs',
            ],
            [
                'date' => $currentMonth->copy()->startOfMonth()->addDays(3),
                'start_time' => '14:00',
                'end_time' => '17:30',
                'hours' => 3.5,
                'description' => 'Refatoracao de codigo',
            ],
            [
                'date' => $currentMonth->copy()->startOfMonth()->addDays(4),
                'start_time' => '09:00',
                'end_time' => '11:00',
                'hours' => 2,
                'description' => 'Documentacao tecnica',
            ],
            [
                'date' => $currentMonth->copy()->startOfMonth()->addDays(4),
                'start_time' => '13:00',
                'end_time' => '18:00',
                'hours' => 5,
                'description' => 'Deploy e configuracao',
            ],
            [
                'date' => $currentMonth->copy()->startOfMonth()->addDays(5),
                'start_time' => '09:30',
                'end_time' => '12:30',
                'hours' => 3,
                'description' => 'Analise de requisitos',
            ],
        ];

        foreach ($entries as $entry) {
            TimeEntry::create([
                'user_id' => $user->id,
                'date' => $entry['date']->format('Y-m-d'),
                'start_time' => $entry['start_time'],
                'end_time' => $entry['end_time'],
                'hours' => $entry['hours'],
                'description' => $entry['description'],
                'month_reference' => $entry['date']->format('Y-m'),
            ]);
        }

        // Create some entries for last month
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthEntries = [
            [
                'date' => $lastMonth->copy()->startOfMonth()->addDays(10),
                'start_time' => '09:00',
                'end_time' => '17:00',
                'hours' => 8,
                'description' => 'Desenvolvimento completo de modulo',
            ],
            [
                'date' => $lastMonth->copy()->startOfMonth()->addDays(15),
                'start_time' => '10:00',
                'end_time' => '15:00',
                'hours' => 5,
                'description' => 'Integracao com API externa',
            ],
        ];

        foreach ($lastMonthEntries as $entry) {
            TimeEntry::create([
                'user_id' => $user->id,
                'date' => $entry['date']->format('Y-m-d'),
                'start_time' => $entry['start_time'],
                'end_time' => $entry['end_time'],
                'hours' => $entry['hours'],
                'description' => $entry['description'],
                'month_reference' => $entry['date']->format('Y-m'),
            ]);
        }
    }
}
