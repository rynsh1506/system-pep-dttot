<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CekReksaloan extends Model
{
    protected $connection = 'dtot';
    protected $table = 'cekreksaloan';
    protected $guarded = ['id'];
    const UPDATED_AT = null;
}
