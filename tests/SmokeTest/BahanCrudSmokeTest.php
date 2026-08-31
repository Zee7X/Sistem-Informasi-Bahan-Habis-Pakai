<?php

namespace Tests\SmokeTest;

use App\Models\Bahan;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BahanCrudSmokeTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $mahasiswa;
    private Satuan $satuan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_bahan@bhp.com'],
            ['name' => 'Admin Test Bahan', 'password' => bcrypt('12345'), 'role' => 'admin']
        );

        $this->mahasiswa = User::firstOrCreate(
            ['email' => 'mhs_test_bahan@bhp.com'],
            ['name' => 'Mahasiswa Test Bahan', 'password' => bcrypt('12345'), 'role' => 'mahasiswa']
        );

        $this->satuan = Satuan::firstOrCreate(['nama' => 'Botol']);
    }

    /** 1. Positive: Admin can view Bahan index with filter & search */
    public function test_admin_can_view_bahan_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.bahan.index', ['search' => 'Alkohol', 'sort' => 'latest']));
        $response->assertStatus(200);
    }

    /** 2. Positive: Admin can create new Bahan */
    public function test_admin_can_create_bahan(): void
    {
        $payload = [
            'kode_bahan'   => 'BHN-' . strtoupper(uniqid()),
            'nama_bahan'   => 'Asam Klorida ' . uniqid(),
            'satuan_id'    => $this->satuan->id,
            'spesifikasi'  => 'PA Grade 37%',
            'lokasi'       => 'Lemari Asam A1',
            'minimal_stok' => 5,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.bahan.store'), $payload);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bahan', [
            'kode_bahan'   => $payload['kode_bahan'],
            'nama_bahan'   => $payload['nama_bahan'],
            'minimal_stok' => 5,
        ]);
    }

    /** 3. Positive: Admin can update Bahan */
    public function test_admin_can_update_bahan(): void
    {
        $bahan = Bahan::create([
            'kode_bahan'   => 'BHN-' . strtoupper(uniqid()),
            'nama_bahan'   => 'Etanol ' . uniqid(),
            'satuan_id'    => $this->satuan->id,
            'stok'         => 20,
            'minimal_stok' => 4,
        ]);

        $updatePayload = [
            'kode_bahan'   => $bahan->kode_bahan,
            'nama_bahan'   => 'Etanol 96% Updated',
            'satuan_id'    => $this->satuan->id,
            'spesifikasi'  => 'Teknis 96%',
            'lokasi'       => 'Gudang B',
            'minimal_stok' => 8,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.bahan.update', $bahan), $updatePayload);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bahan', [
            'id'           => $bahan->id,
            'nama_bahan'   => 'Etanol 96% Updated',
            'minimal_stok' => 8,
            'lokasi'       => 'Gudang B',
        ]);
    }

    /** 4. Positive: Admin can delete unused Bahan */
    public function test_admin_can_delete_unused_bahan(): void
    {
        $bahan = Bahan::create([
            'kode_bahan'   => 'DEL-' . strtoupper(uniqid()),
            'nama_bahan'   => 'Bahan Hapus ' . uniqid(),
            'satuan_id'    => $this->satuan->id,
            'stok'         => 0,
            'minimal_stok' => 1,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.bahan.destroy', $bahan));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('bahan', ['id' => $bahan->id]);
    }

    /** 5. Negative: Validation fails when required fields are missing */
    public function test_create_bahan_fails_with_missing_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.bahan.store'), [
            'kode_bahan'   => '',
            'nama_bahan'   => '',
            'satuan_id'    => '',
            'minimal_stok' => '',
        ]);

        $response->assertSessionHasErrors(['kode_bahan', 'nama_bahan', 'satuan_id', 'minimal_stok']);
    }

    /** 6. Negative: Validation fails when duplicate kode_bahan is submitted */
    public function test_create_bahan_fails_with_duplicate_kode_bahan(): void
    {
        $existing = Bahan::create([
            'kode_bahan'   => 'DUP-KODE-' . strtoupper(uniqid()),
            'nama_bahan'   => 'Existing Bahan',
            'satuan_id'    => $this->satuan->id,
            'stok'         => 5,
            'minimal_stok' => 2,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.bahan.store'), [
            'kode_bahan'   => $existing->kode_bahan,
            'nama_bahan'   => 'Different Name',
            'satuan_id'    => $this->satuan->id,
            'minimal_stok' => 2,
        ]);

        $response->assertSessionHasErrors(['kode_bahan']);
    }

    /** 7. Negative: Validation fails with invalid satuan_id and negative minimal_stok */
    public function test_create_bahan_fails_with_invalid_foreign_key_and_negative_minimal_stock(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.bahan.store'), [
            'kode_bahan'   => 'INV-FK-' . uniqid(),
            'nama_bahan'   => 'Invalid FK Bahan',
            'satuan_id'    => 9999999, // non-existent
            'minimal_stok' => -1,      // negative
        ]);

        $response->assertSessionHasErrors(['satuan_id', 'minimal_stok']);
    }

    /** 8. Negative: Non-admin role cannot create or delete Bahan */
    public function test_non_admin_forbidden_from_modifying_bahan(): void
    {
        $response = $this->actingAs($this->mahasiswa)->post(route('admin.bahan.store'), [
            'kode_bahan'   => 'MHS-HACK',
            'nama_bahan'   => 'Hack Bahan',
            'satuan_id'    => $this->satuan->id,
            'minimal_stok' => 1,
        ]);
        $response->assertStatus(403);
    }
}
