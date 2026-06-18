<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanDtotModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pengajuan_dtot';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
