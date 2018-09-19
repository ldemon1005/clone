<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomestayImage extends Model
{
    protected $table = 'homestay_images';
    protected $primaryKey = 'homestay_image_id';
    public $timestamps = true;
    protected $guarded = [];
}
