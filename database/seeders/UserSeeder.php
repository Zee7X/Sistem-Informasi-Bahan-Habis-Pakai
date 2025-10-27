<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bhp.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('12345'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'ketua@bhp.com'],
            [
                'name' => 'Ketua Jurusan',
                'password' => Hash::make('12345'),
                'role' => 'ketua_jurusan',
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@bhp.com'],
            [
                'name' => 'Mahasiswa',
                'password' => Hash::make('12345'),
                'role' => 'mahasiswa',
                'nim' => '12345678',
                'kelas' => 'TI-2022',
            ]
        );

        // for ($i = 1; $i <= 25; $i++) {
        //     User::updateOrCreate(
        //         ['email' => "mahasiswa{$i}@bhp.com"],
        //         [
        //             'name' => "Mahasiswa {$i}",
        //             'password' => Hash::make('12345'),
        //             'role' => 'mahasiswa',
        //             'nim' => '2022' . str_pad($i, 4, '0', STR_PAD_LEFT),
        //             'kelas' => 'TI-2022',
        //         ]
        //     );
        // }
    }
}
