<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HashPlainPasswords extends Command
{
    protected $signature = 'hash:plain-passwords';
    protected $description = 'Hash all plain text passwords in the database';

    public function handle()
    {
        $users = User::all();
        $updatedCount = 0;

        foreach ($users as $user) {
            // Şifreyi doğrudan hashle
            $user->password = Hash::make($user->password);
            $user->save();
            $updatedCount++;
        }

        $this->info("Toplam {$updatedCount} kullanıcının şifresi hashlendi.");
    }
}
