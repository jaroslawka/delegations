<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delegation extends Model
{
    use HasFactory;

    protected $fillable = ['worker_id','start','end','country'];

    protected $hidden = ['created_at','updated_at'];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function allowance()
    {
        return $this->hasOne(Allowance::class, 'country', 'country');
    }
}
