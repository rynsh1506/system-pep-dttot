<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Terduga extends Model
{
    use SoftDeletes;

    protected $connection = 'dtot';
    protected $table = 'terduga';
    protected $guarded = ['id'];
    const UPDATED_AT = null;
}
