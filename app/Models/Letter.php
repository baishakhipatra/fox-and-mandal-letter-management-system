<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use HasFactory;
    protected $table = 'letters';
    protected $fillable = [
        'created_by',
        'letter_id', 
        'received_from', 
        'handed_over_by', 
        'send_to', 
        'subject', 
        'document_reference_no', 
        'document_date',
        'document_image', 
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime', // Good to include this too
    ];

    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'letter_id', 'id'); 
    }

    public function handedOverByUser()
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }


}
