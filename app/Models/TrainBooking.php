<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',        
        'from',            
        'to',             
        'travel_date', 
        'bill_to',
        'matter_code',
        'type',
        'traveller',
        'sequence_no',
        'order_no',
        'trip_type',
        "return_date",
         'seat_preference',
        'purpose_description',
        'food_preference'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
