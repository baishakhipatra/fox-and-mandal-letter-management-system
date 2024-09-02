<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookTransfer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'book_id', 'from_user_id', 'to_user_id', 'status', 'requested_at', 'approved_at', 'declined_at'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
