<?php

namespace Tests\SmokeTest;

use App\Models\Bahan;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SatuanCrudSmokeTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_satuan@bhp.com'],
            ['name' => 'Admin Test', 'password' => bcrypt('12345'), 'role' => 'admin']
        );

        $this->mahasiswa = User::firstOrCreate(
            ['email' => 'mhs_test_satuan@bhp.com'],
            ['name' => 'Mahasiswa Test', 'password' => bcrypt('12345'), 'role' => 'mahasiswa']
        );
    }

    /** 1. Positive: Admin can view Satuan list */
    public function test_admin_can_view_satuan_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.satuan.index'));
        $response->assertStatus(200);
    }

    /** 2. Positive: Admin can create a new Satuan */
    public function test_admin_can_create_satuan(): void
    {
        $payload = ['nama' => 'TestSatuan_' . uniqid()];

        $response = $this->actingAs($this->admin)->post(route('admin.satuan.store'), $payload);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('satuan', ['nama' => $payload['nama']]);
    }

    /** 3. Positive: Admin can update an existing Satuan */
    public function test_admin_can_update_satuan(): void
    {
        $satuan = Satuan::create(['nama' => 'OrigSatuan_' . uniqid()]);
        $newName = 'UpdatedSatuan_' . uniqid();

        $response = $this->actingAs($this->admin)->put(route('admin.satuan.update', $satuan), [
            'nama' => $newName,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('satuan', ['id' => $satuan->id, 'nama' => $newName]);
    }

    /** 4. Positive: Admin can delete an unused Satuan */
    public function test_admin_can_delete_unused_satuan(): void
    {
        $satuan = Satuan::create(['nama' => 'UnusedSatuan_' . uniqid()]);

        $response = $this->actingAs($this->admin)->delete(route('admin.satuan.destroy', $satuan));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('satuan', ['id' => $satuan->id]);
    }

    /** 5. Negative: Validation fails when creating Satuan with empty nama */
    public function test_create_satuan_fails_with_empty_name(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.satuan.store'), ['nama' => '']);

        $response->assertSessionHasErrors(['nama']);
    }

    /** 6. Negative: Validation fails with duplicate Satuan name */
    public function test_create_satuan_fails_with_duplicate_name(): void
    {
        $existing = Satuan::create(['nama' => 'DupSatuan_' . uniqid()]);

        $response = $this->actingAs($this->admin)->post(route('admin.satuan.store'), [
            'nama' => $existing->nama,
        ]);

        $response->assertSessionHasErrors(['nama']);
    }

    /** 7. Negative: Cannot delete Satuan if it is referenced by Bahan */
    public function test_delete_satuan_fails_when_used_by_bahan(): void
    {
        $satuan = Satuan::create(['nama' => 'UsedSatuan_' . uniqid()]);
        Bahan::create([
            'kode_bahan'   => 'TST_' . uniqid(),
            'nama_bahan'   => 'Bahan Ref ' . uniqid(),
            'satuan_id'    => $satuan->id,
            'stok'         => 10,
            'minimal_stok' => 2,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.satuan.destroy', $satuan));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('satuan', ['id' => $satuan->id]);
    }

    /** 8. Negative: Unauthorized access by Mahasiswa is forbidden (403) */
    public function test_non_admin_cannot_access_satuan(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('admin.satuan.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->mahasiswa)->post(route('admin.satuan.store'), [
            'nama' => 'HackedSatuan',
        ]);
        $response->assertStatus(403);
    }
}
