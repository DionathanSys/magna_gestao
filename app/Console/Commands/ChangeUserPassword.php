<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangeUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:change-password {user_id} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change user password by user ID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $password = $this->argument('password');

        $user = User::find($userId);

        if (!$user) {
            $this->error("Usuário com ID {$userId} não encontrado.");
            return 1;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("Senha do usuário {$user->name} (ID: {$userId}) alterada com sucesso!");
        
        return 0;
    }
}
