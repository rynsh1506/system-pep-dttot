<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{
    protected $connection = 'dtot';
    protected $table = 'change_requests';
    protected $guarded = ['id'];

    public function targetTerduga()
    {
        return $this->belongsTo(Terduga::class, 'target_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
}
