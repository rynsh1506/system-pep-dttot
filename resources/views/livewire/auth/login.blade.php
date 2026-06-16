<div
    class="min-h-screen flex items-center justify-center bg-cover bg-center"
    style="background-image: url('{{ asset('assets/background_pep.png') }}');"
>
    {{-- Semi-transparent overlay agar teks mudah dibaca di atas background --}}
    <div class="w-full h-full min-h-screen flex items-center justify-center bg-base-100/60 backdrop-blur-sm">
        <div class="card w-full max-w-sm bg-base-100 shadow-2xl">
            <div class="card-body items-center text-center">

                {{-- Security Badge (Tahap 3) --}}
                <div class="inline-flex items-center gap-2 mb-4 px-4 py-2 bg-base-content/5 rounded-full text-xs text-primary font-semibold uppercase">
                    <span class="text-lg">🔒</span> Security Verification
                </div>

                {{-- Title & Subtitle (Tahap 3) --}}
                <h2 class="card-title justify-center text-2xl font-bold mb-1">Portal PEP &amp; DTTOT System</h2>
                <p class="text-sm text-base-content/70 mb-6">Silakan masuk untuk mengakses Portal PEP &amp; DTTOT.</p>

                {{-- Form Livewire (Tahap 4) --}}
                <form wire:submit.prevent="login" class="w-full">
                    <div class="form-control w-full mb-4 text-left">
                        <label class="label">
                            <span class="label-text">Username</span>
                        </label>
                        <input
                            type="text"
                            wire:model="username"
                            class="input input-bordered w-full"
                            placeholder="admin"
                            autofocus
                        />
                        @error('username') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-6 text-left">
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>
                        <input
                            type="password"
                            wire:model="password"
                            class="input input-bordered w-full"
                            placeholder="••••••••"
                        />
                        @error('password') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mt-2">
                        <button type="submit" class="btn btn-primary w-full" style="padding: 1rem; height: auto;">
                            <span wire:loading.remove wire:target="login">Sign In</span>
                            <span wire:loading wire:target="login" class="loading loading-spinner"></span>
                        </button>
                    </div>
                </form>

                {{-- Footer Security Notice (Tahap 5) --}}
                <div class="mt-8 text-xs text-center text-base-content/60">
                    Authorized Access Only. All activities are logged.
                </div>

            </div>
        </div>
    </div>
</div>
