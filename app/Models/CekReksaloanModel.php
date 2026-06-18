<?php

namespace App\Models;

use CodeIgniter\Model;

class CekReksaloanModel extends Model
{
    protected $table            = 'cekreksaloan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'no_kontrak',
        'nama_debitur',
        'nik',
        'hasil_dtot',
        'hasil_pep',
        'keterangan',
        'bukti_ss',
        'checked_by',
        'checked_at'
    ];

    // Dates
    protected $useTimestamps = false;
}
