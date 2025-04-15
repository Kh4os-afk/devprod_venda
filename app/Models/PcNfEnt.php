<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PcNfEnt extends Model
{
    protected $connection = 'oracle';
    protected $table = 'pcnfent';

    protected $casts = [
        'dtemissao' => 'datetime',
    ];
}
