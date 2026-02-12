<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateTrialSubscriptions extends Command
{
    protected $signature = 'subscriptions:create-trials';

    protected $description = 'Cria trial de 7 dias para usuarios sem assinatura';

    public function handle(): int
    {
        $users = User::whereDoesntHave('subscription')->get();

        if ($users->isEmpty()) {
            $this->info('Todos os usuarios ja possuem assinatura.');
            return 0;
        }

        $count = 0;
        foreach ($users as $user) {
            $user->createTrialSubscription();
            $count++;
        }

        $this->info("Trial criado para {$count} usuario(s).");
        return 0;
    }
}
