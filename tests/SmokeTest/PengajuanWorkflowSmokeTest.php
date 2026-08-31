<?php

namespace Tests\SmokeTest;

use App\Models\Bahan;
use App\Models\ModulPraktikum;
use App\Models\ModulPraktikumItem;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PengajuanWorkflowSmokeTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $mahasiswa1;
    private User $mahasiswa2;
    private Bahan $bahan;
    private ModulPraktikum $modul;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_flow@bhp.com'],
            ['name' => 'Admin Workflow Test', 'password' => bcrypt('12345'), 'role' => 'admin']
        );

        $this->mahasiswa1 = User::firstOrCreate(
            ['email' => 'mhs1_test_flow@bhp.com'],
            ['name' => 'Mahasiswa One', 'password' => bcrypt('12345'), 'role' => 'mahasiswa']
        );

        $this->mahasiswa2 = User::firstOrCreate(
            ['email' => 'mhs2_test_flow@bhp.com'],
            ['name' => 'Mahasiswa Two', 'password' => bcrypt('12345'), 'role' => 'mahasiswa']
        );

        $satuan = Satuan::firstOrCreate(['nama' => 'Gram']);

        $this->bahan = Bahan::create([
            'kode_bahan'   => 'FLW-BHN-' . uniqid(),
            'nama_bahan'   => 'Bahan Natrium Hidroksida ' . uniqid(),
            'satuan_id'    => $satuan->id,
            'stok'         => 100,
            'minimal_stok' => 10,
        ]);

        $this->modul = ModulPraktikum::create([
            'kode_modul' => 'MDL-FLOW-' . uniqid(),
            'nama_modul' => 'Modul Titrasi Asam Basa',
            'created_by' => $this->admin->id,
        ]);

        ModulPraktikumItem::create([
            'modul_id' => $this->modul->id,
            'bahan_id' => $this->bahan->id,
            'jumlah'   => 15.0,
        ]);
    }

    /** 1. Positive: Mahasiswa can submit Pengajuan Mandiri */
    public function test_mahasiswa_can_submit_pengajuan_mandiri(): void
    {
        $payload = [
            'jenis'          => 'mandiri',
            'mata_kuliah'    => 'Kimia Analitik',
            'kelas'          => 'KA-1A',
            'kelompok'       => 'Kelompok 1',
            'jumlah_anggota' => 4,
            'tanggal_pakai'  => now()->addDays(3)->toDateString(),
            'keterangan'     => 'Uji titrasi mandiri',
            'items'          => [
                ['bahan_id' => $this->bahan->id, 'jumlah' => 10.0],
            ],
        ];

        $response = $this->actingAs($this->mahasiswa1)->post(route('mahasiswa.pengajuan.store'), $payload);

        $response->assertRedirect(route('mahasiswa.pengajuan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan', [
            'user_id'     => $this->mahasiswa1->id,
            'jenis'       => 'mandiri',
            'status'      => 'pending_review',
            'mata_kuliah' => 'Kimia Analitik',
        ]);

        $pengajuan = Pengajuan::where('user_id', $this->mahasiswa1->id)->latest()->first();

        // Verify snapshot creation
        $this->assertDatabaseHas('pengajuan_items', [
            'pengajuan_id'        => $pengajuan->id,
            'bahan_id'            => $this->bahan->id,
            'nama_bahan_snapshot' => $this->bahan->nama_bahan,
            'jumlah'              => 10.0,
        ]);

        // Verify stock is NOT decremented yet
        $this->bahan->refresh();
        $this->assertEquals(100, $this->bahan->stok);
    }

    /** 2. Positive: Full state lifecycle Pending -> Approved -> Completed (with atomic stock deduction) */
    public function test_full_pengajuan_lifecycle_pending_to_approved_to_completed(): void
    {
        // 1. Create pengajuan
        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'BHP-2026-' . rand(1000, 9999),
            'user_id'        => $this->mahasiswa1->id,
            'jenis'          => 'mandiri',
            'tanggal_pakai'  => now()->addDays(2),
            'status'         => 'pending_review',
        ]);

        PengajuanItem::create([
            'pengajuan_id'        => $pengajuan->id,
            'bahan_id'            => $this->bahan->id,
            'nama_bahan_snapshot' => $this->bahan->nama_bahan,
            'satuan_snapshot'     => 'Gram',
            'jumlah'              => 25.0,
        ]);

        // 2. Admin approves
        $approveResponse = $this->actingAs($this->admin)->post(route('admin.pengajuan.approve', $pengajuan));
        $approveResponse->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertEquals('approved', $pengajuan->status);
        $this->assertEquals($this->admin->id, $pengajuan->approved_by);
        $this->assertNotNull($pengajuan->approved_at);

        // Stock must still be 100 before handover
        $this->bahan->refresh();
        $this->assertEquals(100, $this->bahan->stok);

        // 3. Admin completes transaction on physical handover
        $completeResponse = $this->actingAs($this->admin)->post(route('admin.pengajuan.complete', $pengajuan));
        $completeResponse->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertEquals('completed', $pengajuan->status);
        $this->assertEquals($this->admin->id, $pengajuan->completed_by);

        // CRITICAL: Stock is now decremented by 25 (100 - 25 = 75)
        $this->bahan->refresh();
        $this->assertEquals(75, $this->bahan->stok);

        // Audit log must exist
        $this->assertDatabaseHas('log_stok', [
            'bahan_id'        => $this->bahan->id,
            'jenis'           => 'keluar',
            'jumlah'          => 25.0,
            'stok_sebelum'    => 100,
            'stok_sesudah'    => 75,
            'reference_table' => 'pengajuan',
            'reference_id'    => $pengajuan->id,
        ]);
    }

    /** 3. Positive: Admin can reject a pending pengajuan with a reason */
    public function test_admin_can_reject_pengajuan_with_reason(): void
    {
        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'BHP-2026-' . rand(1000, 9999),
            'user_id'        => $this->mahasiswa1->id,
            'jenis'          => 'mandiri',
            'tanggal_pakai'  => now()->addDays(2),
            'status'         => 'pending_review',
        ]);

        $reason = 'Jadwal laboratorium penuh pada tanggal yang diajukan.';
        $response = $this->actingAs($this->admin)->post(route('admin.pengajuan.reject', $pengajuan), [
            'reject_reason' => $reason,
        ]);

        $response->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertEquals('rejected', $pengajuan->status);
        $this->assertEquals($reason, $pengajuan->reject_reason);
    }

    /** 4. Negative: Submission fails when date is in the past or items is empty */
    public function test_pengajuan_submission_fails_with_past_date_or_empty_items(): void
    {
        // Past date
        $responseDate = $this->actingAs($this->mahasiswa1)->post(route('mahasiswa.pengajuan.store'), [
            'jenis'         => 'mandiri',
            'tanggal_pakai' => now()->subDay()->toDateString(),
            'items'         => [['bahan_id' => $this->bahan->id, 'jumlah' => 1]],
        ]);
        $responseDate->assertSessionHasErrors(['tanggal_pakai']);

        // Empty items array
        $responseEmpty = $this->actingAs($this->mahasiswa1)->post(route('mahasiswa.pengajuan.store'), [
            'jenis'         => 'mandiri',
            'tanggal_pakai' => now()->addDay()->toDateString(),
            'items'         => [],
        ]);
        $responseEmpty->assertSessionHasErrors(['items']);
    }

    /** 5. Negative: Rejection fails when reason is omitted or too short */
    public function test_rejection_fails_with_empty_or_short_reason(): void
    {
        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'BHP-2026-' . rand(1000, 9999),
            'user_id'        => $this->mahasiswa1->id,
            'jenis'          => 'mandiri',
            'tanggal_pakai'  => now()->addDays(2),
            'status'         => 'pending_review',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.pengajuan.reject', $pengajuan), [
            'reject_reason' => 'Ditolak', // less than 10 chars
        ]);

        $response->assertSessionHasErrors(['reject_reason']);
    }

    /** 6. Negative: Completing transaction fails if stock is insufficient */
    public function test_complete_fails_when_stock_is_insufficient(): void
    {
        // Set available stock to 5
        $this->bahan->update(['stok' => 5]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'BHP-2026-' . rand(1000, 9999),
            'user_id'        => $this->mahasiswa1->id,
            'jenis'          => 'mandiri',
            'tanggal_pakai'  => now()->addDays(2),
            'status'         => 'approved', // already approved
        ]);

        PengajuanItem::create([
            'pengajuan_id'        => $pengajuan->id,
            'bahan_id'            => $this->bahan->id,
            'nama_bahan_snapshot' => $this->bahan->nama_bahan,
            'satuan_snapshot'     => 'Gram',
            'jumlah'              => 50.0, // Needs 50, but stock is only 5
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.pengajuan.complete', $pengajuan));

        $response->assertSessionHas('error');

        $pengajuan->refresh();
        $this->assertEquals('approved', $pengajuan->status); // Remains approved, not completed
        $this->bahan->refresh();
        $this->assertEquals(5, $this->bahan->stok); // Stock unchanged
    }

    /** 7. Negative: Illegal state transition (cannot complete directly from pending) */
    public function test_cannot_complete_pending_pengajuan_without_prior_approval(): void
    {
        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'BHP-2026-' . rand(1000, 9999),
            'user_id'        => $this->mahasiswa1->id,
            'jenis'          => 'mandiri',
            'tanggal_pakai'  => now()->addDays(2),
            'status'         => 'pending_review',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.pengajuan.complete', $pengajuan));

        $response->assertSessionHas('error');
    }

    /** 8. Negative: Privacy check - Mahasiswa cannot view another student's pengajuan (403) */
    public function test_mahasiswa_cannot_view_other_students_pengajuan(): void
    {
        $pengajuanMhs1 = Pengajuan::create([
            'kode_pengajuan' => 'BHP-2026-' . rand(1000, 9999),
            'user_id'        => $this->mahasiswa1->id,
            'jenis'          => 'mandiri',
            'tanggal_pakai'  => now()->addDays(2),
            'status'         => 'pending_review',
        ]);

        // Mahasiswa 2 attempts viewing Mahasiswa 1's submission
        $response = $this->actingAs($this->mahasiswa2)->get(route('mahasiswa.pengajuan.show', $pengajuanMhs1));
        $response->assertStatus(403);
    }
}
