<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    /** @use HasFactory<\Database\Factories\TestModelFactory> */
    use HasFactory;
}
