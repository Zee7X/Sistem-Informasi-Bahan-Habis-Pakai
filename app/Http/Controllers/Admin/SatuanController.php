<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SatuanRequest;
use App\Services\SatuanService;
use App\Models\Satuan;
use Exception;
use Inertia\Inertia;

class SatuanController extends Controller
{
    protected SatuanService $service;

    public function __construct(SatuanService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $satuan = $this->service->list($search);
        $filters = $request->only('search');
        return Inertia::render('Admin/Satuan/Index', compact('satuan', 'filters'));
    }

    public function store(SatuanRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->back()->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function update(SatuanRequest $request, Satuan $satuan)
    {
        $this->service->update($satuan, $request->validated());
        return redirect()->back()->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Satuan $satuan)
    {
        try {
            $this->service->delete($satuan);
            return redirect()->back()->with('success', 'Satuan berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
