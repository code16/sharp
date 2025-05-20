<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestTag extends Model
{
    /** @use HasFactory<\Database\Factories\TestTagFactory> */
    use HasFactory;

    protected $guarded = [];
}
