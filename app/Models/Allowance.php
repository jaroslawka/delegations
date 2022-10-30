<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    use HasFactory;

    protected $fillable = ['country', 'ammount'];

    protected $hidden = ['id', 'country', 'created_at', 'updated_at'];
}
