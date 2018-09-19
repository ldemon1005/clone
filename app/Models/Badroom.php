<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badroom extends Model
{
    protected $table = 'bedrooms';
    protected $primaryKey = 'bedroom_id';
    protected $guarded = [];
}
