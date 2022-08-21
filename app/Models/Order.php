<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_firstname',
        'billing_lastname',
        'billing_email',
        'billing_address',
        'billing_postalcode'
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listings()
    {
        return $this->belongsToMany(Listing::class)->withPivot('quantity');
    }
}
