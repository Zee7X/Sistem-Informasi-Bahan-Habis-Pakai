<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BahanSeeder extends Seeder
{
    public function run(): void
    {
        $bahan = [
            ['kode_bahan' => 'BHN001', 'nama_bahan' => 'NaOH', 'spesifikasi' => 'Natrium Hidroksida'],
            ['kode_bahan' => 'BHN002', 'nama_bahan' => 'Mg(OH)₂', 'spesifikasi' => 'Magnesium Hidroksida'],
            ['kode_bahan' => 'BHN003', 'nama_bahan' => 'Al₂(SO₄)₃', 'spesifikasi' => 'Tawas'],
            ['kode_bahan' => 'BHN004', 'nama_bahan' => 'CH₄N₂O', 'spesifikasi' => 'Urea'],
            ['kode_bahan' => 'BHN005', 'nama_bahan' => 'NaCl', 'spesifikasi' => 'Natrium Klorida'],
            ['kode_bahan' => 'BHN006', 'nama_bahan' => 'C₁₂H₂₂O₁₁', 'spesifikasi' => 'Sukrosa'],
            ['kode_bahan' => 'BHN007', 'nama_bahan' => 'Sulfadiazin', 'spesifikasi' => 'Senyawa antibakteri'],
            ['kode_bahan' => 'BHN008', 'nama_bahan' => 'CuSO₄·5H₂O', 'spesifikasi' => 'Cooper Sulfat Pentahidrat'],
            ['kode_bahan' => 'BHN009', 'nama_bahan' => 'CuSO₄', 'spesifikasi' => 'Cooper Sulfat'],
            ['kode_bahan' => 'BHN010', 'nama_bahan' => 'Na₂CO₃', 'spesifikasi' => 'Natrium Karbonat'],
            ['kode_bahan' => 'BHN011', 'nama_bahan' => 'NH₄Cl', 'spesifikasi' => 'Amonium Klorida'],
            ['kode_bahan' => 'BHN012', 'nama_bahan' => 'Na₂SO₄', 'spesifikasi' => 'Natrium Sulfat'],
            ['kode_bahan' => 'BHN013', 'nama_bahan' => 'Na₂S₂O₃', 'spesifikasi' => 'Natrium Tiosulfat'],
            ['kode_bahan' => 'BHN014', 'nama_bahan' => 'MgSO₄', 'spesifikasi' => 'Magnesium Sulfat'],
            ['kode_bahan' => 'BHN015', 'nama_bahan' => 'NaNO₂', 'spesifikasi' => 'Natrium Nitrit'],
            ['kode_bahan' => 'BHN016', 'nama_bahan' => 'NaNO₃', 'spesifikasi' => 'Natrium Nitrat'],
            ['kode_bahan' => 'BHN017', 'nama_bahan' => 'KOH', 'spesifikasi' => 'Kalium Hidroksida'],
            ['kode_bahan' => 'BHN018', 'nama_bahan' => 'Karbon Aktif', 'spesifikasi' => 'Bahan penyerap (adsorben)'],
            ['kode_bahan' => 'BHN019', 'nama_bahan' => 'SrCl₂', 'spesifikasi' => 'Strontium Klorida'],
            ['kode_bahan' => 'BHN020', 'nama_bahan' => 'C₂H₂O₄', 'spesifikasi' => 'Asam Oksalat'],
            ['kode_bahan' => 'BHN021', 'nama_bahan' => 'PAC', 'spesifikasi' => 'Poly Aluminium Chloride'],
            ['kode_bahan' => 'BHN022', 'nama_bahan' => 'C₆H₈O₇', 'spesifikasi' => 'Asam Sitrat'],
            ['kode_bahan' => 'BHN023', 'nama_bahan' => 'C₆H₁₂O₆', 'spesifikasi' => 'Glukosa Teknis'],
            ['kode_bahan' => 'BHN024', 'nama_bahan' => 'SO₂', 'spesifikasi' => 'Sulfur Dioksida'],
            ['kode_bahan' => 'BHN025', 'nama_bahan' => 'CaCl₂', 'spesifikasi' => 'Kalsium Klorida'],
            ['kode_bahan' => 'BHN026', 'nama_bahan' => 'KMnO₄', 'spesifikasi' => 'Kalium Permanganat'],
            ['kode_bahan' => 'BHN027', 'nama_bahan' => 'Fe₂O₃', 'spesifikasi' => 'Besi (III) Oksida'],
            ['kode_bahan' => 'BHN028', 'nama_bahan' => 'Fe', 'spesifikasi' => 'Besi Powder'],
            ['kode_bahan' => 'BHN029', 'nama_bahan' => 'C₆H₅COOH', 'spesifikasi' => 'Asam Benzoat'],
            ['kode_bahan' => 'BHN030', 'nama_bahan' => 'MgSO₄·7H₂O', 'spesifikasi' => 'Magnesium Sulfat Heptahidrat'],
            ['kode_bahan' => 'BHN031', 'nama_bahan' => 'C₈H₅KO₄', 'spesifikasi' => 'Potassium Hydrogen Phthalate (KHP)'],
            ['kode_bahan' => 'BHN032', 'nama_bahan' => 'Na₃PO₄·12H₂O', 'spesifikasi' => 'Trisodium Phosphate Dodecahydrate'],
            ['kode_bahan' => 'BHN033', 'nama_bahan' => 'NH₄NO₃', 'spesifikasi' => 'Amonium Nitrat'],
            ['kode_bahan' => 'BHN034', 'nama_bahan' => 'Na₂H₂CO₃', 'spesifikasi' => 'Natrium Perkarbonat'],
            ['kode_bahan' => 'BHN035', 'nama_bahan' => 'FeSO₄', 'spesifikasi' => 'Besi Sulfat'],
            ['kode_bahan' => 'BHN036', 'nama_bahan' => 'FeCl₃', 'spesifikasi' => 'Besi (III) Klorida'],
            ['kode_bahan' => 'BHN037', 'nama_bahan' => '(NH₄)₂Fe(SO₄)₂·6H₂O', 'spesifikasi' => 'Ferrous Ammonium Sulfat (FAS)'],
            ['kode_bahan' => 'BHN038', 'nama_bahan' => 'KSCN', 'spesifikasi' => 'Kalium Tiosianat'],
            ['kode_bahan' => 'BHN039', 'nama_bahan' => 'KNaC₄H₄O₆·4H₂O', 'spesifikasi' => 'Potassium Sodium Tartrate'],
            ['kode_bahan' => 'BHN040', 'nama_bahan' => 'Pb(C₂H₃O₂)₂·3H₂O', 'spesifikasi' => 'Timbal (II) Asetat Trihidrat'],
            ['kode_bahan' => 'BHN041', 'nama_bahan' => 'C₆H₁₂O₆', 'spesifikasi' => 'Fruktosa'],
            ['kode_bahan' => 'BHN042', 'nama_bahan' => 'Na₂HPO₄·12H₂O', 'spesifikasi' => 'Dinatrium Hidrogen Fosfat'],
        ];

        $getSatuanId = function ($nama, $spesifikasi) {
            $namaLower = Str::lower($nama . ' ' . $spesifikasi);

            if (Str::contains($namaLower, ['asam', 'chloride', 'oxide', 'sulfate', 'solution', 'so₂', 'pac'])) {
                return 1; 
            } elseif (Str::contains($namaLower, ['karbon aktif', 'powder', 'tawas', 'urea', 'glukosa', 'benzoat', 'sukrosa', 'magnesium', 'natrium', 'kalium', 'besi'])) {
                return 4;
            } else {
                return 2; 
            }
        };

        $data = collect($bahan)->map(function ($item) use ($getSatuanId) {
            $item['stok'] = rand(500, 2000);
            $item['minimal_stok'] = 200;
            $item['satuan_id'] = $getSatuanId($item['nama_bahan'], $item['spesifikasi']);
            return $item;
        })->toArray();

        DB::table('bahan')->insert($data);
    }
}
