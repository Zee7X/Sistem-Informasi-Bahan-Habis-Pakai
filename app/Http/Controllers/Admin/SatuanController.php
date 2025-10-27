<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Satuan;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $satuan = Satuan::when($search, function ($query, $search) {
            return $query->where('nama', 'like', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate(10) 
            ->withQueryString();

        return view('admin.satuan', compact('satuan'));
    }
}
