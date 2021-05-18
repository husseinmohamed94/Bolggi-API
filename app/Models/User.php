<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Passport\HasApiTokens;
use Mindscms\Entrust\Traits\EntrustUserWithPermissionsTrait;
use App\Models\Post;
use App\Models\Comment;
use Nicolaslopezj\Searchable\SearchableTrait;

class User extends Authenticatable implements MustVerifyEmail
{
    use  HasApiTokens , Notifiable ,EntrustUserWithPermissionsTrait,SearchableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function receivesBroadcastNotificationsOn()
    {
        return 'App.User.'.$this->id;
    }

    protected $searchable = [

        'columns' => [
            'users.name'               => 10,
            'users.username'           => 10,
            'users.email'              => 10,
            'users.mobile'             => 10,
            'users.bio'                => 10,

        ],

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function status(){
        return $this->status == 1 ? 'Active' : 'Inactive';
    }

    public  function userImage(){
    return  $this->user_image != ''?  asset('assets/Users/'.$this->user_image) :asset('assets/users/defaultsmall.jpg');
    }
}
