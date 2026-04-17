<?php

namespace Database\Seeders;

use App\Models\Bahan;
use App\Models\PenggunaanBahan;
use App\Models\Satuan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectDummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil data referensi agar tidak orphan
        $mahasiswa = User::where('role', 'mahasiswa')->first();
        $admin = User::where('role', 'admin')->first();
        $bahanList = Bahan::all();
        $satuanList = Satuan::all();

        if (!$mahasiswa || !$admin || $bahanList->isEmpty()) {
            $this->command->warn('Pastikan User (Mahasiswa & Admin) dan Bahan sudah di-seed terlebih dahulu!');
            return;
        }

        $this->command->info('Memulai seeding data dummy penggunaan bahan...');

        // 2. Loop untuk 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            
            // Buat sekitar 10-15 transaksi per bulan
            $recordCount = rand(10, 15);
            
            for ($j = 0; $j < $recordCount; $j++) {
                $statusRand = rand(1, 10);
                $status = 'pending';
                if ($statusRand <= 7) $status = 'approved';
                if ($statusRand == 8) $status = 'rejected';

                $bahan = $bahanList->random();
                $tanggal = $monthDate->copy()->startOfMonth()->addDays(rand(0, 27));

                $data = [
                    'tanggal_pemakaian' => $tanggal->format('Y-m-d'),
                    'waktu_input' => $tanggal->copy()->setTime(rand(8, 16), rand(0, 59)),
                    'requester_user_id' => $mahasiswa->id,
                    'nama_pengisi' => $mahasiswa->name,
                    'nim_pengisi' => $mahasiswa->nim ?? '12345678',
                    'bahan_id' => $bahan->id,
                    'nama_bahan_text' => $bahan->nama_bahan,
                    'jumlah' => rand(1, 10),
                    'satuan_id' => $bahan->satuan_id ?? $satuanList->random()->id,
                    'mata_kuliah' => 'Praktikum Kimia Dasar',
                    'kelas' => $mahasiswa->kelas ?? 'TI-2022',
                    'kelompok' => 'Kelompok ' . rand(1, 5),
                    'keterangan' => 'Penggunaan untuk modul praktikum ke-' . rand(1, 10),
                    'status' => $status,
                    'created_at' => $tanggal,
                    'updated_at' => $tanggal,
                ];

                if ($status === 'approved') {
                    $data['approved_by'] = $admin->id;
                    $data['approved_at'] = $tanggal->copy()->addHours(rand(1, 4));
                }

                PenggunaanBahan::create($data);
            }
        }

        $this->command->info('Seeding data dummy selesai!');
    }
}
