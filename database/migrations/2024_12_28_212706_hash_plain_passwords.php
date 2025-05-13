<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class HashPlainPasswords extends Migration
{
    public function up()
    {
        $users = User::all();

        foreach ($users as $user) {
            if (!Hash::needsRehash($user->password)) {
                $user->password = Hash::make($user->password);
                $user->save();
            }
        }
    }

    public function down()
    {
        // Bu migration geri alınamaz çünkü hashlenmiş şifreler düz metne dönüştürülemez.
    }
}
