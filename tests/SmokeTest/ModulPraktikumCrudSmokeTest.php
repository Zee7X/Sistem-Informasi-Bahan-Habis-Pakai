<?php

namespace Tests\SmokeTest;

use App\Models\Bahan;
use App\Models\ModulPraktikum;
use App\Models\ModulPraktikumItem;
use App\Models\Pengajuan;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModulPraktikumCrudSmokeTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $mahasiswa;
    private Bahan $bahan1;
    private Bahan $bahan2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_modul@bhp.com'],
            ['name' => 'Admin Test Modul', 'password' => bcrypt('12345'), 'role' => 'admin']
        );

        $this->mahasiswa = User::firstOrCreate(
            ['email' => 'mhs_test_modul@bhp.com'],
            ['name' => 'Mahasiswa Test Modul', 'password' => bcrypt('12345'), 'role' => 'mahasiswa']
        );

        $satuan = Satuan::firstOrCreate(['nama' => 'Pcs']);

        $this->bahan1 = Bahan::create([
            'kode_bahan'   => 'MDL-B1-' . uniqid(),
            'nama_bahan'   => 'Bahan Modul 1 ' . uniqid(),
            'satuan_id'    => $satuan->id,
            'stok'         => 50,
            'minimal_stok' => 5,
        ]);

        $this->bahan2 = Bahan::create([
            'kode_bahan'   => 'MDL-B2-' . uniqid(),
            'nama_bahan'   => 'Bahan Modul 2 ' . uniqid(),
            'satuan_id'    => $satuan->id,
            'stok'         => 50,
            'minimal_stok' => 5,
        ]);
    }

    /** 1. Positive: Admin can view modul list & create form */
    public function test_admin_can_view_modul_index_and_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.modul-praktikum.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('admin.modul-praktikum.create'));
        $response->assertStatus(200);
    }

    /** 2. Positive: Admin can create modul with items */
    public function test_admin_can_create_modul_with_items(): void
    {
        $payload = [
            'kode_modul' => 'MDL-' . strtoupper(uniqid()),
            'nama_modul' => 'Praktikum Kimia Dasar ' . uniqid(),
            'deskripsi'  => 'Modul praktikum pengenalan alat dan bahan.',
            'is_active'  => true,
            'items'      => [
                ['bahan_id' => $this->bahan1->id, 'jumlah' => 2.5],
                ['bahan_id' => $this->bahan2->id, 'jumlah' => 1.0],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.modul-praktikum.store'), $payload);

        $response->assertRedirect(route('admin.modul-praktikum.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('modul_praktikum', ['kode_modul' => strtoupper($payload['kode_modul'])]);
        $modul = ModulPraktikum::where('kode_modul', strtoupper($payload['kode_modul']))->first();
        $this->assertCount(2, $modul->items);
    }

    /** 3. Positive: Admin can update modul header info */
    public function test_admin_can_update_modul(): void
    {
        $modul = ModulPraktikum::create([
            'kode_modul' => 'MOD-UPD-' . uniqid(),
            'nama_modul' => 'Modul Original',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.modul-praktikum.update', $modul), [
            'kode_modul' => 'MOD-UPD-NEW',
            'nama_modul' => 'Modul Updated Name',
            'deskripsi'  => 'Updated description',
            'is_active'  => false,
        ]);

        $response->assertRedirect(route('admin.modul-praktikum.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('modul_praktikum', ['id' => $modul->id, 'nama_modul' => 'Modul Updated Name']);
    }

    /** 4. Positive: Admin can add and remove items from existing modul */
    public function test_admin_can_add_and_remove_modul_items(): void
    {
        $modul = ModulPraktikum::create([
            'kode_modul' => 'MOD-ITM-' . uniqid(),
            'nama_modul' => 'Modul Manage Items',
            'created_by' => $this->admin->id,
        ]);

        // Add item
        $response = $this->actingAs($this->admin)->post(route('admin.modul-praktikum.items.store', $modul), [
            'bahan_id' => $this->bahan1->id,
            'jumlah'   => 3.0,
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('modul_praktikum_items', ['modul_id' => $modul->id, 'bahan_id' => $this->bahan1->id]);

        $item = ModulPraktikumItem::where('modul_id', $modul->id)->where('bahan_id', $this->bahan1->id)->first();

        // Delete item
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.modul-praktikum.items.destroy', [$modul, $item]));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('modul_praktikum_items', ['id' => $item->id]);
    }

    /** 5. Negative: Validation fails when items list is empty or invalid */
    public function test_create_modul_fails_with_empty_items_or_duplicate_bahan(): void
    {
        // Empty items
        $response = $this->actingAs($this->admin)->post(route('admin.modul-praktikum.store'), [
            'kode_modul' => 'ERR-MOD-1',
            'nama_modul' => 'Modul Error Items',
            'items'      => [],
        ]);
        $response->assertSessionHasErrors(['items']);

        // Duplicate bahan in same creation payload
        $responseDup = $this->actingAs($this->admin)->post(route('admin.modul-praktikum.store'), [
            'kode_modul' => 'ERR-MOD-2',
            'nama_modul' => 'Modul Dup Items',
            'items'      => [
                ['bahan_id' => $this->bahan1->id, 'jumlah' => 1],
                ['bahan_id' => $this->bahan1->id, 'jumlah' => 2], // duplicate
            ],
        ]);
        $responseDup->assertSessionHasErrors(['items.0.bahan_id', 'items.1.bahan_id']);
    }

    /** 6. Negative: Adding duplicate item to existing modul returns error flash */
    public function test_add_duplicate_item_to_existing_modul_fails(): void
    {
        $modul = ModulPraktikum::create([
            'kode_modul' => 'MOD-DUP-' . uniqid(),
            'nama_modul' => 'Modul Duplicate Item Test',
            'created_by' => $this->admin->id,
        ]);

        ModulPraktikumItem::create([
            'modul_id' => $modul->id,
            'bahan_id' => $this->bahan1->id,
            'jumlah'   => 1,
        ]);

        // Attempt adding the same bahan again
        $response = $this->actingAs($this->admin)->post(route('admin.modul-praktikum.items.store', $modul), [
            'bahan_id' => $this->bahan1->id,
            'jumlah'   => 5,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, $modul->items()->where('bahan_id', $this->bahan1->id)->count());
    }

    /** 7. Negative: Cannot delete modul if referenced by existing pengajuan */
    public function test_cannot_delete_modul_used_in_pengajuan(): void
    {
        $modul = ModulPraktikum::create([
            'kode_modul' => 'MOD-USE-' . uniqid(),
            'nama_modul' => 'Modul In Use',
            'created_by' => $this->admin->id,
        ]);

        Pengajuan::create([
            'kode_pengajuan' => 'BHP-2026-9999',
            'user_id'        => $this->mahasiswa->id,
            'modul_id'       => $modul->id,
            'jenis'          => 'modul',
            'tanggal_pakai'  => now()->addDays(2),
            'status'         => 'pending_review',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.modul-praktikum.destroy', $modul));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('modul_praktikum', ['id' => $modul->id]);
    }
}
