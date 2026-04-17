@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard</h1>
            <p class="text-sm text-slate-500 mt-1 font-medium">Monitoring real-time aktivitas penggunaan bahan laboratorium.
            </p>
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistics Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        @php
            $statItems = [];
            if ($role === 'mahasiswa') {
                $statItems = [
                    [
                        'label' => 'Total Pengajuan',
                        'value' => $stats['total_pengajuan'],
                        'trend' => 'Personal',
                        'color' => 'blue',
                        'icon' =>
                            'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    ],
                    [
                        'label' => 'Disetujui',
                        'value' => $stats['approved_request'],
                        'trend' => 'Approved',
                        'color' => 'emerald',
                        'icon' => 'M5 13l4 4L19 7',
                    ],
                    [
                        'label' => 'Pending',
                        'value' => $stats['pending_request'],
                        'trend' => 'Reviewing',
                        'color' => 'amber',
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    [
                        'label' => 'Bahan Tersedia',
                        'value' => $stats['total_bahan'],
                        'trend' => 'Katalog',
                        'color' => 'indigo',
                        'icon' =>
                            'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                    ],
                ];
            } else {
                $statItems = [
                    [
                        'label' => 'Total Bahan',
                        'value' => $stats['total_bahan'],
                        'trend' => 'In Stock',
                        'color' => 'blue',
                        'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    ],
                    [
                        'label' => 'Total User',
                        'value' => $stats['total_user'],
                        'trend' => 'Active',
                        'color' => 'slate',
                        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                    ],
                    [
                        'label' => 'Pending Review',
                        'value' => $stats['pending_request'],
                        'trend' => 'Critical',
                        'color' => 'amber',
                        'icon' =>
                            'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    ],
                    [
                        'label' => 'Stok Kritis',
                        'value' => $stats['stok_kritis'],
                        'trend' => 'Alert',
                        'color' => 'rose',
                        'icon' =>
                            'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                    ],
                ];
            }

            $colorMapping = [
                'blue' => [
                    'gradient' => 'from-blue-600 to-indigo-700',
                    'shadow' => 'shadow-blue-500/20',
                    'text_light' => 'text-blue-100',
                    'text_dim' => 'text-blue-200/60',
                ],
                'emerald' => [
                    'gradient' => 'from-emerald-500 to-teal-600',
                    'shadow' => 'shadow-emerald-500/20',
                    'text_light' => 'text-emerald-100',
                    'text_dim' => 'text-emerald-200/60',
                ],
                'amber' => [
                    'gradient' => 'from-amber-500 to-orange-600',
                    'shadow' => 'shadow-amber-500/20',
                    'text_light' => 'text-amber-100',
                    'text_dim' => 'text-amber-200/60',
                ],
                'rose' => [
                    'gradient' => 'from-rose-500 to-red-700',
                    'shadow' => 'shadow-rose-500/20',
                    'text_light' => 'text-rose-100',
                    'text_dim' => 'text-rose-200/60',
                ],
                'indigo' => [
                    'gradient' => 'from-indigo-600 to-purple-700',
                    'shadow' => 'shadow-indigo-500/20',
                    'text_light' => 'text-indigo-100',
                    'text_dim' => 'text-indigo-200/60',
                ],
                'slate' => [
                    'gradient' => 'from-slate-600 to-slate-800',
                    'shadow' => 'shadow-slate-500/20',
                    'text_light' => 'text-slate-100',
                    'text_dim' => 'text-slate-200/60',
                ],
            ];
        @endphp

        @foreach ($statItems as $item)
            @php
                $c = $colorMapping[$item['color']] ?? $colorMapping['blue'];
            @endphp
            <div
                class="card-modern p-6 bg-gradient-to-br {{ $c['gradient'] }} text-white relative overflow-hidden transition-all hover:shadow-2xl {{ $c['shadow'] }} group cursor-default">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-widest mb-1 opacity-80 {{ $c['text_light'] }}">
                        {{ $item['label'] }}
                    </p>
                    <div class="flex items-baseline gap-1.5">
                        <h3
                            class="text-3xl font-black tracking-tight group-hover:scale-105 transition-transform duration-300">
                            {{ number_format($item['value']) }}
                        </h3>
                    </div>
                    <p class="text-[10px] font-medium mt-1 uppercase tracking-tighter {{ $c['text_dim'] }}">
                        {{ $item['trend'] }} Monitoring
                    </p>
                </div>
                <svg class="absolute -right-4 -bottom-4 w-28 h-28 text-white/10 transform rotate-12 group-hover:scale-110 transition-transform duration-500"
                    fill="currentColor" viewBox="0 0 24 24">
                    <path d="{{ $item['icon'] }}" />
                </svg>
            </div>
        @endforeach
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Usage Chart (Left) -->
        <div class="lg:col-span-12 xl:col-span-8">
            <div class="card-modern h-full overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-white">
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Tren Penggunaan 6 Bulan Terakhir</h3>
                </div>
                <div class="p-6">
                    <div class="w-full h-[320px]">
                        <canvas id="usageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity (Right) -->
        <div class="lg:col-span-12 xl:col-span-4">
            <div class="card-modern overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-white flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Aktivitas Terkini</h3>
                    <a href="{{ route('pengajuan.index') }}"
                        class="text-[10px] font-bold text-blue-600 hover:text-blue-700 uppercase tracking-widest">Detail</a>
                </div>
                <div class="p-4 space-y-4">
                    @forelse($recent_activity as $act)
                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                            <div
                                class="w-8 h-8 rounded-md shrink-0 flex items-center justify-center 
                            {{ $act->status === 'approved' ? 'bg-emerald-50 text-emerald-600' : ($act->status === 'rejected' ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600') }}">
                                @if ($act->status === 'approved')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                @elseif($act->status === 'rejected')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-800 truncate">
                                    {{ $role === 'mahasiswa' ? $act->bahan->nama_bahan : $act->nama_pengisi }}</p>
                                <p class="text-[10px] text-slate-400 mt-1 font-medium flex items-center gap-2">
                                    {{ $act->created_at->diffForHumans() }}
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider
                                        {{ $act->status === 'approved'
                                            ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20'
                                            : ($act->status === 'rejected'
                                                ? 'bg-rose-500/10 text-rose-600 border border-rose-500/20'
                                                : 'bg-amber-500/10 text-amber-600 border border-amber-500/20') }}">
                                        {{ $act->status }}
                                    </span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-[11px] font-bold text-slate-800">{{ $act->jumlah }}
                                    {{ $act->satuan->nama ?? '' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <p class="text-[11px] font-bold text-slate-300 uppercase tracking-widest">Belum ada aktivitas
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('turbo:load', function() {
            const canvas = document.getElementById('usageChart');
            if (!canvas) return;

            // Cleanup before re-init (important for Turbo)
            const existingChart = Chart.getChart(canvas);
            if (existingChart) {
                existingChart.destroy();
            }

            const ctx = canvas.getContext('2d');

            // Simple clinical gradient
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.08)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Penggunaan',
                        data: @json($chartData['values']),
                        borderColor: '#10b981',
                        borderWidth: 2.5,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#10b981',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: {
                                size: 12,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 10,
                            borderRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 10,
                                    weight: '600'
                                },
                                color: '#94a3b8',
                                padding: 8,
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10,
                                    weight: '600'
                                },
                                color: '#94a3b8',
                                padding: 8
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
