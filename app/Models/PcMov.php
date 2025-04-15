<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PcMov extends Model
{
    protected $connection = 'oracle';
    protected $table = 'pcmov';
}
