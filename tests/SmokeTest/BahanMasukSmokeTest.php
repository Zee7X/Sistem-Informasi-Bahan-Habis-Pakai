<?php

namespace Tests\SmokeTest;

use App\Models\Bahan;
use App\Models\BahanMasuk;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BahanMasukSmokeTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $kjur;
    private User $mahasiswa;
    private Bahan $bahan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_masuk@bhp.com'],
            ['name' => 'Admin Test Masuk', 'password' => bcrypt('12345'), 'role' => 'admin']
        );

        $this->kjur = User::firstOrCreate(
            ['email' => 'kjur_test_masuk@bhp.com'],
            ['name' => 'Kjur Test Masuk', 'password' => bcrypt('12345'), 'role' => 'ketua_jurusan']
        );

        $this->mahasiswa = User::firstOrCreate(
            ['email' => 'mhs_test_masuk@bhp.com'],
            ['name' => 'Mhs Test Masuk', 'password' => bcrypt('12345'), 'role' => 'mahasiswa']
        );

        $satuan = Satuan::firstOrCreate(['nama' => 'Liter']);

        $this->bahan = Bahan::create([
            'kode_bahan'   => 'MSK-BHN-' . uniqid(),
            'nama_bahan'   => 'Bahan Restock ' . uniqid(),
            'satuan_id'    => $satuan->id,
            'stok'         => 10,
            'minimal_stok' => 5,
        ]);
    }

    /** 1. Positive: Admin can record Bahan Masuk and stock increments immediately */
    public function test_admin_can_record_bahan_masuk(): void
    {
        $payload = [
            'bahan_id'      => $this->bahan->id,
            'jumlah'        => 20,
            'tanggal_masuk' => now()->toDateString(),
            'pemasok'       => 'PT Kimia Farma',
            'no_faktur'     => 'FAK-12345',
            'harga_satuan'  => 50000,
            'keterangan'    => 'Pengadaan semester ganjil',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.bahan-masuk.store'), $payload);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bahan_masuk', [
            'bahan_id'  => $this->bahan->id,
            'jumlah'    => 20,
            'no_faktur' => 'FAK-12345',
        ]);

        // Verify stock has increased from 10 to 30
        $this->bahan->refresh();
        $this->assertEquals(30, $this->bahan->stok);

        // Verify audit log recorded
        $this->assertDatabaseHas('log_stok', [
            'bahan_id'     => $this->bahan->id,
            'jenis'        => 'masuk',
            'jumlah'       => 20,
            'stok_sebelum' => 10,
            'stok_sesudah' => 30,
        ]);
    }

    /** 2. Positive: Ketua Jurusan can approve Bahan Masuk */
    public function test_kjur_can_approve_bahan_masuk(): void
    {
        $masuk = BahanMasuk::create([
            'bahan_id'      => $this->bahan->id,
            'jumlah'        => 15,
            'tanggal_masuk' => now()->toDateString(),
            'status_kjur'   => 'pending',
            'created_by'    => $this->admin->id,
        ]);

        $response = $this->actingAs($this->kjur)->post(route('kjur.bahan-masuk.approve', $masuk));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bahan_masuk', [
            'id'          => $masuk->id,
            'status_kjur' => 'approved',
        ]);
    }

    /** 3. Positive: Admin can delete a Bahan Masuk record */
    public function test_admin_can_delete_bahan_masuk(): void
    {
        $masuk = BahanMasuk::create([
            'bahan_id'      => $this->bahan->id,
            'jumlah'        => 5,
            'tanggal_masuk' => now()->toDateString(),
            'status_kjur'   => 'pending',
            'created_by'    => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.bahan-masuk.destroy', $masuk));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('bahan_masuk', ['id' => $masuk->id]);
    }

    /** 4. Negative: Validation fails when required input is missing or negative */
    public function test_bahan_masuk_fails_with_invalid_inputs(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.bahan-masuk.store'), [
            'bahan_id'      => 9999999, // invalid FK
            'jumlah'        => -5,      // negative
            'tanggal_masuk' => 'invalid-date',
            'harga_satuan'  => -100,    // negative
        ]);

        $response->assertSessionHasErrors(['bahan_id', 'jumlah', 'tanggal_masuk', 'harga_satuan']);
    }

    /** 5. Negative: Kjur cannot approve an already approved record */
    public function test_cannot_approve_already_approved_bahan_masuk(): void
    {
        $masuk = BahanMasuk::create([
            'bahan_id'         => $this->bahan->id,
            'jumlah'           => 10,
            'tanggal_masuk'    => now()->toDateString(),
            'status_kjur'      => 'approved',
            'created_by'       => $this->admin->id,
            'approved_by_kjur' => $this->kjur->id,
        ]);

        $response = $this->actingAs($this->kjur)->post(route('kjur.bahan-masuk.approve', $masuk));

        $response->assertSessionHas('error');
    }

    /** 6. Negative: Mahasiswa is forbidden from managing or approving bahan masuk */
    public function test_mahasiswa_forbidden_from_bahan_masuk_actions(): void
    {
        $response = $this->actingAs($this->mahasiswa)->post(route('admin.bahan-masuk.store'), [
            'bahan_id'      => $this->bahan->id,
            'jumlah'        => 5,
            'tanggal_masuk' => now()->toDateString(),
        ]);
        $response->assertStatus(403);
    }
}
