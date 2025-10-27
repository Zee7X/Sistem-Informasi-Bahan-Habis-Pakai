<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $bahan = Bahan::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('kode_bahan', 'like', "%{$search}%")
                    ->orWhere('nama_bahan', 'like', "%{$search}%")
                    ->orWhere('spesifikasi', 'like', "%{$search}%")
                    ->orWhere('stok', 'like', "%{$search}%")
                    ->orWhere('minimal_stok', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        })
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('bahan', compact('bahan'));
    }
}
