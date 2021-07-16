<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductsList extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    use HasFactory;
}
