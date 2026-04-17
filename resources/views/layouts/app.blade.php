<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/tppl.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-slate-900 overflow-hidden">
    <div class="flex h-screen bg-slate-50/50 backdrop-blur-sm">

        @include('components.sidebar')

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            @include('layouts.navigation')

            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                <div class="max-w-7xl mx-auto space-y-6">
                    @hasSection('header')
                        <div class="mb-6">
                            @yield('header')
                        </div>
                    @endif

                    <turbo-frame id="main-content">
                        @yield('content')

                        <!-- Inline Toast Trigger for Turbo -->
                        <script>
                            (function() {
                                @if(session('success'))
                                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: @js(session('success')) } }));
                                @endif
                                @if(session('error'))
                                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: @js(session('error')) } }));
                                @endif
                            })();
                        </script>
                    </turbo-frame>
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')

    <!-- Global Toast System -->
    <div x-data="{ 
            toasts: [],
            add(msg, type = 'success') {
                const id = Date.now();
                this.toasts.push({ id, msg, type });
                setTimeout(() => this.remove(id), 5000);
            },
            remove(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }
         }" 
         @toast.window="add($event.detail.message, $event.detail.type)"
         class="fixed bottom-6 right-6 z-[100] flex flex-col gap-3 max-w-sm pointer-events-none">
        
        <template x-for="toast in toasts" :key="toast.id">
            <div x-data="{ show: false }" 
                 x-init="setTimeout(() => show = true, 50)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-y-4 opacity-0 scale-95"
                 x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90"
                 :class="toast.type === 'success' ? 'bg-white border-emerald-100 shadow-emerald-500/10' : 'bg-white border-rose-100 shadow-rose-500/10'"
                 class="pointer-events-auto p-4 rounded-2xl border shadow-2xl flex items-center gap-4 group min-w-[200px]">
                
                <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'"
                     class="w-8 h-8 rounded-xl flex items-center justify-center text-white shrink-0">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </template>
                </div>

                <div class="flex-1">
                    <span x-text="toast.msg" class="text-xs font-bold text-slate-700 tracking-tight"></span>
                </div>

                <button @click="show = false; setTimeout(() => remove(toast.id), 200)" class="p-1 hover:bg-slate-100 rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </template>
    </div>

    <script>
        // Global logic can stay here, but flash triggers must be inside frame
    </script>
</body>

</html>
