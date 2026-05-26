<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $bahan = Bahan::with('satuan')
            ->when($request->search, fn ($q, $s) =>
                $q->where('nama_bahan', 'like', "%{$s}%")
                  ->orWhere('kode_bahan', 'like', "%{$s}%")
            )
            ->orderBy('nama_bahan')
            ->paginate(24)
            ->withQueryString();

        $filters = $request->only('search');

        return Inertia::render('Mahasiswa/Katalog/Index', compact('bahan', 'filters'));
    }
}
