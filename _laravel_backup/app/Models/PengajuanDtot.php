<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanDtot extends Model
{
    protected $connection = 'dtot';
    protected $table = 'pengajuan_dtot';
    protected $guarded = ['id'];

    public function userPemeriksa()
    {
        return $this->belongsTo(User::class, 'checked_by', 'id');
    }
}
