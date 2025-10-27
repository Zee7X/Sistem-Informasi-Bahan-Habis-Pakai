<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Satuan;

class SatuanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Liter'],
            ['nama' => 'Pcs'],
            ['nama' => 'Box'],
            ['nama' => 'Gram'],
        ];

        foreach ($data as $satuan) {
            Satuan::create($satuan);
        }
    }
}
