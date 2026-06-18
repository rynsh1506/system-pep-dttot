<?php

namespace App\Models;

use CodeIgniter\Model;

class TerdugaModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'terduga';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';
}
