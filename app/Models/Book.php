<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','bookshelves_id', 'office_id', 'uid', 'qrcode', 'category_id',
        'title', 'author', 'publisher', 'edition', 'page', 'quantity',
    ];

    

    public function bookshelve()
    {
        return $this->belongsTo(Bookshelve::class, 'bookshelves_id');
    }
     public function user()
     {
         return $this->belongsTo(User::class);
     }
     
     public function office()
    {
        return $this->belongsTo(Office::class);
    }
     
      public function bookshelves()
     {
         return $this->belongsTo(Bookshelve::class);
     }
     
      public function category()
     {
         return $this->belongsTo(BookCategory::class);
     }
}
