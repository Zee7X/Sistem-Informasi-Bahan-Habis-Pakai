<?php

namespace Tests\SmokeTest;

use App\Models\Bahan;
use App\Models\ModulPraktikum;
use App\Models\ModulPraktikumItem;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportsAndApiSmokeTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $kjur;
    private User $mahasiswa;
    private Bahan $bahan;
    private ModulPraktikum $modul;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_rep@bhp.com'],
            ['name' => 'Admin Test Reports', 'password' => bcrypt('12345'), 'role' => 'admin']
        );

        $this->kjur = User::firstOrCreate(
            ['email' => 'kjur_test_rep@bhp.com'],
            ['name' => 'Kjur Test Reports', 'password' => bcrypt('12345'), 'role' => 'ketua_jurusan']
        );

        $this->mahasiswa = User::firstOrCreate(
            ['email' => 'mhs_test_rep@bhp.com'],
            ['name' => 'Mahasiswa Test Reports', 'password' => bcrypt('12345'), 'role' => 'mahasiswa']
        );

        $satuan = Satuan::firstOrCreate(['nama' => 'Unit']);

        $this->bahan = Bahan::create([
            'kode_bahan'   => 'REP-BHN-' . uniqid(),
            'nama_bahan'   => 'Bahan Laporan Searchable ' . uniqid(),
            'satuan_id'    => $satuan->id,
            'stok'         => 40,
            'minimal_stok' => 5,
        ]);

        $this->modul = ModulPraktikum::create([
            'kode_modul' => 'MDL-REP-' . uniqid(),
            'nama_modul' => 'Modul Api Test',
            'created_by' => $this->admin->id,
        ]);

        ModulPraktikumItem::create([
            'modul_id' => $this->modul->id,
            'bahan_id' => $this->bahan->id,
            'jumlah'   => 5.0,
        ]);
    }

    /** 1. Positive: Dashboard renders properly for each role */
    public function test_dashboard_accessible_by_all_roles(): void
    {
        $this->actingAs($this->admin)->get(route('dashboard'))->assertStatus(200);
        $this->actingAs($this->kjur)->get(route('dashboard'))->assertStatus(200);
        $this->actingAs($this->mahasiswa)->get(route('dashboard'))->assertStatus(200);
    }

    /** 2. Positive: Admin can view Laporan & Log Stok & Export CSV */
    public function test_admin_laporan_and_exports(): void
    {
        // Index
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));
        $response->assertStatus(200);

        // CSV Export
        $csvResponse = $this->actingAs($this->admin)->get(route('admin.laporan.export', ['format' => 'csv']));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('content-type'));

        // Log Stok
        $logResponse = $this->actingAs($this->admin)->get(route('admin.log-stok.index'));
        $logResponse->assertStatus(200);
    }

    /** 3. Positive: Ketua Jurusan can view Rekap Semester & Transaksi list */
    public function test_ketua_jurusan_views_rekap_and_transaksi(): void
    {
        $responseRekap = $this->actingAs($this->kjur)->get(route('kjur.laporan.rekap'));
        $responseRekap->assertStatus(200);

        $responseTrx = $this->actingAs($this->kjur)->get(route('kjur.transaksi.index'));
        $responseTrx->assertStatus(200);
    }

    /** 4. Positive: Mahasiswa can view Katalog Bahan */
    public function test_mahasiswa_can_view_katalog(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('mahasiswa.katalog.index'));
        $response->assertStatus(200);
    }

    /** 5. Positive: Internal search API returns matching results in JSON format */
    public function test_api_bahan_search_and_modul_items(): void
    {
        // Search API
        $searchResponse = $this->actingAs($this->mahasiswa)->get(route('api.bahan.search', ['q' => 'Searchable']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertJsonStructure(['items', 'hasMore']);

        // Modul items API
        $modulResponse = $this->actingAs($this->mahasiswa)->get(route('api.modul.items', $this->modul));
        $modulResponse->assertStatus(200);
        $modulResponse->assertJsonStructure(['items' => [['bahan_id', 'nama_bahan', 'satuan', 'stok', 'jumlah']]]);
    }

    /** 6. Negative: Unauthenticated guest redirected to /login (302) */
    public function test_unauthenticated_guest_redirected(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect('/login');

        $responseApi = $this->get(route('api.bahan.search'));
        $responseApi->assertRedirect('/login');
    }

    /** 7. Negative: Role boundary check (Mahasiswa cannot access Admin Laporan) */
    public function test_mahasiswa_forbidden_from_admin_laporan(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('admin.laporan.index'));
        $response->assertStatus(403);

        $responseLog = $this->actingAs($this->mahasiswa)->get(route('admin.log-stok.index'));
        $responseLog->assertStatus(403);
    }
}
