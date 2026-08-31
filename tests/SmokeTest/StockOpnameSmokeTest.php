<?php

namespace Tests\SmokeTest;

use App\Models\Bahan;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StockOpnameSmokeTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $mahasiswa;
    private Bahan $bahan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_opname@bhp.com'],
            ['name' => 'Admin Test Opname', 'password' => bcrypt('12345'), 'role' => 'admin']
        );

        $this->mahasiswa = User::firstOrCreate(
            ['email' => 'mhs_test_opname@bhp.com'],
            ['name' => 'Mahasiswa Test Opname', 'password' => bcrypt('12345'), 'role' => 'mahasiswa']
        );

        $satuan = Satuan::firstOrCreate(['nama' => 'Kotak']);

        $this->bahan = Bahan::create([
            'kode_bahan'   => 'OPN-BHN-' . uniqid(),
            'nama_bahan'   => 'Bahan Opname ' . uniqid(),
            'satuan_id'    => $satuan->id,
            'stok'         => 30,
            'minimal_stok' => 5,
        ]);
    }

    /** 1. Positive: Admin can view Stock Opname list */
    public function test_admin_can_view_stock_opname_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.stock-opname.index'));
        $response->assertStatus(200);
    }

    /** 2. Positive: Admin can perform stock opname */
    public function test_admin_can_perform_stock_opname(): void
    {
        $payload = [
            'bahan_id'          => $this->bahan->id,
            'stok_sesuai'       => 24, // Physical count is 24 (difference = -6)
            'alasan'            => 'Ditemukan 6 botol pecah saat inspeksi lab.',
            'jenis_penyesuaian' => 'rusak',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.stock-opname.store'), $payload);

        $response->assertSessionHas('success');

        // Check stock updated
        $this->bahan->refresh();
        $this->assertEquals(24, $this->bahan->stok);

        // Check stock_opname record created
        $this->assertDatabaseHas('stock_opname', [
            'bahan_id'          => $this->bahan->id,
            'stok_sebelum'      => 30,
            'stok_sesuai'       => 24,
            'selisih'           => -6,
            'jenis_penyesuaian' => 'rusak',
        ]);

        // Check log_stok recorded
        $this->assertDatabaseHas('log_stok', [
            'bahan_id'     => $this->bahan->id,
            'jenis'        => 'opname',
            'stok_sebelum' => 30,
            'stok_sesudah' => 24,
        ]);
    }

    /** 3. Negative: Validation fails when required inputs are missing */
    public function test_stock_opname_fails_with_missing_inputs(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.stock-opname.store'), [
            'bahan_id'          => '',
            'stok_sesuai'       => '',
            'alasan'            => '',
            'jenis_penyesuaian' => '',
        ]);

        $response->assertSessionHasErrors(['bahan_id', 'stok_sesuai', 'alasan', 'jenis_penyesuaian']);
    }

    /** 4. Negative: Validation fails when reason is under 10 chars or invalid adjustment type */
    public function test_stock_opname_fails_with_short_reason_and_invalid_type(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.stock-opname.store'), [
            'bahan_id'          => $this->bahan->id,
            'stok_sesuai'       => 10,
            'alasan'            => 'Pecah', // less than 10 characters
            'jenis_penyesuaian' => 'invalid_type',
        ]);

        $response->assertSessionHasErrors(['alasan', 'jenis_penyesuaian']);
    }

    /** 5. Negative: Validation fails with negative physical stock */
    public function test_stock_opname_fails_with_negative_stock(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.stock-opname.store'), [
            'bahan_id'          => $this->bahan->id,
            'stok_sesuai'       => -5,
            'alasan'            => 'Penghitungan fisik salah total.',
            'jenis_penyesuaian' => 'hilang',
        ]);

        $response->assertSessionHasErrors(['stok_sesuai']);
    }

    /** 6. Negative: Non-admin role cannot perform stock opname */
    public function test_non_admin_cannot_access_stock_opname(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('admin.stock-opname.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->mahasiswa)->post(route('admin.stock-opname.store'), [
            'bahan_id'          => $this->bahan->id,
            'stok_sesuai'       => 10,
            'alasan'            => 'Penyesuaian ilegal oleh mahasiswa.',
            'jenis_penyesuaian' => 'koreksi_lain',
        ]);
        $response->assertStatus(403);
    }
}
