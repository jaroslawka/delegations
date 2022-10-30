<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $hidden = ['created_at','updated_at'];

    public function delegations()
    {
        return $this->hasMany(Delegation::class);
    }
}
