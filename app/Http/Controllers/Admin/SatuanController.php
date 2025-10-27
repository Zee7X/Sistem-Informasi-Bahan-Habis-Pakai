<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SatuanRequest;
use App\Services\SatuanService;
use Exception;

class SatuanController extends Controller
{
    protected $satuanService;

    public function __construct(SatuanService $satuanService)
    {
        $this->satuanService = $satuanService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $satuan = $this->satuanService->getAll($search);
        return view('admin.satuan', compact('satuan'));
    }

    public function store(SatuanRequest $request)
    {
        $this->satuanService->create($request->validated());
        return redirect()->back()->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function update(SatuanRequest $request, $id)
    {
        $this->satuanService->update($id, $request->validated());
        return redirect()->back()->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $this->satuanService->delete($id);
            return redirect()->back()->with('success', 'Satuan berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
