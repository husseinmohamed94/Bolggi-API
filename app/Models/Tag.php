<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Tag extends Model
{
    use Sluggable,SearchableTrait;

    protected  $guarded = [];

    public function sluggable(){
        return [
            'slug' =>[
                'source' => 'name'
            ]
        ];
    }
    protected $searchable = [

        'columns' => [
            'tags.name'                => 10,
            'tags.slug'                => 10,

        ],

    ];
    public function  posts(){
        return $this->belongsToMany(Tag::class, 'posts_tags');
    }
}
