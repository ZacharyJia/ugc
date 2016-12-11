<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UGC extends Model
{
    use SoftDeletes;

    protected $hidden = ['deleted_at'];

    protected $table = 'ugc';
}
