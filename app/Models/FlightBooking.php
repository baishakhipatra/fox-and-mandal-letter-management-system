<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',        
        'trip_type',      
        'from',            
        'to',             
        'departure_date', 
        'return_date',     
        'traveler_number',  
        'bill_to',  
        'arrival_time',  
      
        'traveller',
        'matter_code',
        'sequence_no',
        'order_no',
        'seat_preference',
        'food_preference',
        'purpose_description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
