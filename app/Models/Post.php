<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Fields\Searchable;

class Post extends Model
{
    use HasFactory , Searchable, Actionable;


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

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($post) {
            if ( !$post->user_id ) {
                $post->user_id = Auth::id();
            }
        });
    }
}
