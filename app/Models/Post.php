<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;


    protected $guarded=[];

    protected $casts =[
      'publish_at'  =>'datetime:Y-m-d H:00',
      'publish_until'  =>'datetime:Y-m-d H:00',
      'is_published'  =>'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
