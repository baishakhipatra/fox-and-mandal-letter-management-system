<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    protected $table = 'deliveries';

    protected $fillable = [
        'letter_id',
        'delivered_to_user_id',
        'signature_image_path',
        'delivered_at',
    ];

     public function letter()
    {
        return $this->belongsTo(Letter::class);
    }

    public function deliveredToUser()
    {
        return $this->belongsTo(User::class, 'delivered_to_user_id');
    }
}
