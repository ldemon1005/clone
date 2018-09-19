<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadroomImage extends Model
{
    protected $table = 'bedroom_images';
    protected $primaryKey = 'bedroom_image_id';
    public $timestamps = true;
    protected $guarded = [];
}
