<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $fillable = ['name' , 'description' , 'lat', 'long', 'admin_id'];

    public function vendor()
    {
        return $this->belongsTo(User::class , 'admin_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class , 'store_id');
    }
}
