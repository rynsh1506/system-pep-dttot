<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagement extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editId = null;

    public string $nama_lengkap = '';
    public string $username = '';
    public string $password = '';
    public int $level = 1;
    public string $search = '';

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|min:3|max:100',
            'username'     => 'required|string|min:3|max:50|unique:cadeb.users,username' . ($this->isEditing ? ',' . $this->editId : ''),
            'password'     => $this->isEditing ? 'nullable|min:6' : ['required', 'min:6'],
            'level'        => 'required|integer|in:1,2,3,4',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['nama_lengkap', 'username', 'password', 'level', 'editId', 'isEditing']);
        $this->level = 1;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editId       = $id;
        $this->isEditing    = true;
        $this->nama_lengkap = $user->nama_lengkap;
        $this->username     = $user->username;
        $this->level        = $user->level;
        $this->password     = '';
        $this->showModal    = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->isEditing) {
            $data = [
                'nama_lengkap' => $this->nama_lengkap,
                'username'     => $this->username,
                'level'        => $this->level,
            ];
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            User::findOrFail($this->editId)->update($data);
        } else {
            User::create([
                'nama_lengkap' => $this->nama_lengkap,
                'username'     => $this->username,
                'password'     => Hash::make($this->password),
                'level'        => $this->level,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Berhasil!',
            'text'  => $this->isEditing ? 'User berhasil diperbarui.' : 'User baru berhasil ditambahkan.',
        ]);
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) return;
        User::findOrFail($id)->delete();

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Dihapus!',
            'text'  => 'User berhasil dihapus.',
        ]);
    }

    public function render()
    {
        $roleLabels = [1 => 'Staff Input', 2 => 'Supervisor', 3 => 'Manager', 4 => 'Super Admin'];

        $query = User::query();
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.user-management', [
            'users'      => $query->orderByDesc('level')->orderBy('username')->get(),
            'roleLabels' => $roleLabels,
        ])->layout('components.layouts.app', ['title' => 'User Management']);
    }
}
