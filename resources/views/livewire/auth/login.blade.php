<div class="min-h-screen bg-base-200 flex items-center justify-center">
    <div class="card w-96 bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title justify-center text-2xl font-bold mb-4">Login System</h2>
            
            <form wire:submit.prevent="login">
                <div class="form-control w-full mb-4">
                    <label class="label">
                        <span class="label-text">Username</span>
                    </label>
                    <input type="text" wire:model="username" class="input input-bordered w-full" placeholder="Masukkan username" autofocus />
                    @error('username') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="form-control w-full mb-6">
                    <label class="label">
                        <span class="label-text">Password</span>
                    </label>
                    <input type="password" wire:model="password" class="input input-bordered w-full" placeholder="••••••••" />
                    @error('password') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="form-control w-full mt-2">
                    <button type="submit" class="btn btn-primary w-full">
                        <span wire:loading.remove wire:target="login">Login</span>
                        <span wire:loading wire:target="login" class="loading loading-spinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
