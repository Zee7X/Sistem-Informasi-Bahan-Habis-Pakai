import { Head, router, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Search, FlaskConical } from 'lucide-react';
import { useState } from 'react';

export default function Index({ bahan, filters }) {
    const [search, setSearch] = useState(filters?.search ?? '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/mahasiswa/katalog', { search }, { preserveState: true, replace: true });
    };

    return (
        <AppLayout title="Katalog Bahan">
            <Head title="Katalog Bahan Laboratorium" />
            <div className="p-5 space-y-4">
                {/* Search */}
                <form onSubmit={handleSearch} className="relative max-w-72">
                    <Search size={13} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                    <input value={search} onChange={e => setSearch(e.target.value)} placeholder="Cari bahan..." className="input pl-8" />
                </form>

                {/* Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                    {bahan?.data?.map(b => (
                        <div key={b.id} className="card p-4 flex flex-col gap-2.5 hover:border-violet/40 transition-colors">
                            <div className="flex items-start gap-2.5">
                                <div className="w-8 h-8 bg-violet/10 border border-violet/20 rounded flex items-center justify-center flex-shrink-0">
                                    <FlaskConical size={13} className="text-violet" />
                                </div>
                                <div className="min-w-0 flex-1">
                                    <p className="text-sm font-medium text-text-primary truncate">{b.nama_bahan}</p>
                                    <p className="identifier text-xs">{b.kode_bahan}</p>
                                </div>
                            </div>
                            {b.spesifikasi && (
                                <p className="text-xs text-text-secondary leading-relaxed line-clamp-2">{b.spesifikasi}</p>
                            )}
                            <div className="flex items-center justify-between pt-1 border-t border-border">
                                <div>
                                    <p className="text-2xs text-text-secondary">Stok Tersedia</p>
                                    <p className={`text-sm font-semibold ${b.stok <= b.minimal_stok ? 'text-error' : 'text-success'}`}>
                                        {b.stok} <span className="text-xs font-normal text-text-secondary">{b.satuan?.nama}</span>
                                    </p>
                                </div>
                                {b.lokasi && (
                                    <div className="text-right">
                                        <p className="text-2xs text-text-secondary">Lokasi</p>
                                        <p className="text-xs text-text-primary">{b.lokasi}</p>
                                    </div>
                                )}
                            </div>
                            {b.stok <= b.minimal_stok && (
                                <span className="chip chip-error w-full justify-center">Stok Kritis</span>
                            )}
                        </div>
                    ))}
                    {!bahan?.data?.length && (
                        <div className="col-span-full py-16 text-center text-sm text-text-secondary">
                            Tidak ada bahan ditemukan untuk pencarian ini.
                        </div>
                    )}
                </div>

                {bahan?.total > 0 && (
                    <div className="card overflow-hidden mt-4">
                        <Pagination pagination={bahan} />
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
