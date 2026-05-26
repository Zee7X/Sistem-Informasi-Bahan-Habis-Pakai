<?php

namespace Database\Seeders;

use App\Models\Bahan;
use App\Models\ModulPraktikum;
use App\Models\ModulPraktikumItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class ModulPraktikumSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $moduls = [
            [
                'kode_modul'  => 'KIMIA-01',
                'nama_modul'  => 'Praktikum Kimia Dasar I',
                'deskripsi'   => 'Praktikum pengenalan reaksi kimia dasar, identifikasi senyawa, dan keselamatan laboratorium.',
                'items'       => [
                    ['nama' => 'NaCl', 'jumlah' => 5],
                    ['nama' => 'HCl', 'jumlah' => 10],
                    ['nama' => 'Akuades', 'jumlah' => 100],
                ],
            ],
            [
                'kode_modul'  => 'KIMIA-02',
                'nama_modul'  => 'Praktikum Titrasi Asam-Basa',
                'deskripsi'   => 'Percobaan titrasi untuk menentukan konsentrasi asam atau basa menggunakan indikator.',
                'items'       => [
                    ['nama' => 'NaOH', 'jumlah' => 10],
                    ['nama' => 'HCl', 'jumlah' => 10],
                    ['nama' => 'Indikator Fenolftalein', 'jumlah' => 2],
                    ['nama' => 'Akuades', 'jumlah' => 200],
                ],
            ],
            [
                'kode_modul'  => 'BIO-01',
                'nama_modul'  => 'Praktikum Biologi Sel',
                'deskripsi'   => 'Pengamatan sel tumbuhan dan hewan menggunakan mikroskop.',
                'items'       => [
                    ['nama' => 'Etanol', 'jumlah' => 50],
                    ['nama' => 'Pewarna Metilen Biru', 'jumlah' => 5],
                    ['nama' => 'Akuades', 'jumlah' => 100],
                ],
            ],
            [
                'kode_modul'  => 'FIS-01',
                'nama_modul'  => 'Praktikum Fisika Listrik',
                'deskripsi'   => 'Percobaan rangkaian listrik dasar dan pengukuran tegangan serta arus.',
                'items'       => [
                    ['nama' => 'Kawat Tembaga', 'jumlah' => 2],
                    ['nama' => 'Resistor', 'jumlah' => 10],
                ],
            ],
        ];

        foreach ($moduls as $modulData) {
            $items = $modulData['items'];
            unset($modulData['items']);

            $modul = ModulPraktikum::create([
                ...$modulData,
                'created_by' => $admin?->id,
                'is_active'  => true,
            ]);

            foreach ($items as $itemData) {
                // Cari bahan berdasarkan nama (case insensitive, partial match)
                $bahan = Bahan::where('nama_bahan', 'like', "%{$itemData['nama']}%")->first();

                if ($bahan) {
                    ModulPraktikumItem::create([
                        'modul_id' => $modul->id,
                        'bahan_id' => $bahan->id,
                        'jumlah'   => $itemData['jumlah'],
                    ]);
                }
            }
        }
    }
}
