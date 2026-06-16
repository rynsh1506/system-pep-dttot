<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChangeRequest;
use App\Models\Terduga;
use Illuminate\Support\Facades\DB;

class Approvals extends Component
{
    public function approve($id)
    {
        $role = session('role_level');
        $request = ChangeRequest::findOrFail($id);

        DB::transaction(function () use ($role, $request) {
            if ($role == 2) { // Supervisor
                $request->update([
                    'status' => 'PENDING_MANAGER',
                    'approver_id' => auth()->id()
                ]);
                session()->flash('success', 'Permintaan disetujui dan diteruskan ke Manager.');
            } elseif ($role == 3 || $role == 4) { // Manager / Admin
                $data = json_decode($request->data_json, true);

                if ($request->request_type === 'ADD') {
                    Terduga::create($data);
                } elseif ($request->request_type === 'EDIT') {
                    $terduga = Terduga::findOrFail($request->target_id);
                    $data['is_pending'] = 0;
                    $terduga->update($data);
                } elseif ($request->request_type === 'DELETE') {
                    $terduga = Terduga::findOrFail($request->target_id);
                    $terduga->update(['is_pending' => 0]);
                    $terduga->delete(); // Soft delete
                }

                $request->update([
                    'status' => 'APPROVED',
                    'approver_id' => auth()->id(),
                    'processed_at' => now()
                ]);
                session()->flash('success', 'Permintaan telah disetujui sepenuhnya.');
            }
        });
    }

    public function reject($id)
    {
        $request = ChangeRequest::findOrFail($id);
        
        DB::transaction(function () use ($request) {
            if ($request->target_id) {
                Terduga::where('id', $request->target_id)->update(['is_pending' => 0]);
            }
            $request->update([
                'status' => 'REJECTED',
                'approver_id' => auth()->id(),
                'processed_at' => now()
            ]);
            session()->flash('success', 'Permintaan ditolak.');
        });
    }

    public function render()
    {
        if (session('role_level') < 2) {
            abort(403, 'Akses ditolak.');
        }

        $query = ChangeRequest::with(['requester', 'targetTerduga']);
        
        if (session('role_level') == 2) {
            $query->where('status', 'PENDING_SPV');
        } elseif (session('role_level') == 3) {
            $query->where('status', 'PENDING_MANAGER');
        } else {
            $query->whereIn('status', ['PENDING_SPV', 'PENDING_MANAGER']);
        }

        $requests = $query->orderBy('created_at', 'asc')->get();

        return view('livewire.approvals', [
            'requests' => $requests
        ]);
    }
}
