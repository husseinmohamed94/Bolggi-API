<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/chart/comments_chart','Backend\Api\ApiController@comments_chart');
Route::get('/chart/users_chart','Backend\Api\ApiController@users_chart');



/******** API  **************************/

Route::get('/all_posts',        ['as' =>'' ,'uses'=>'Api\General\GeneralController@get_posts']);
Route::get('/post/{slug}',      ['as' =>'' ,'uses'=>'Api\General\GeneralController@show_post']);
Route::post('/post/{slug}',    ['as'      => '','uses' => 'Api\General\GeneralController@store_comment']);

Route::get('/search',           ['as'      => '',  'uses' =>'Api\General\GeneralController@search']);
Route::get('/category/{category_slug}', ['as'       => '','uses' => 'Api\General\GeneralController@catgory']);
Route::get('/tag/{tag_slug}',    ['as'  => '',  'uses' => 'Api\General\GeneralController@tag']);
Route::get('/archive/{date}',    ['as'=> '', 'uses' => 'Api\General\GeneralController@archive']);
Route::get('/author/{username}',   ['as'  => '', 'uses'  => 'Api\General\GeneralController@author']);


Route::post('/Contact-us', ['as'      => '', 'uses' => 'Api\General\GeneralController@do_Contact']);




Route::post('register',         ['as' =>'' ,     'uses' => 'Api\AuthController@register']);
Route::post('login',            ['as' =>'' ,     'uses' => 'Api\AuthController@login']);
Route::post('refresh_token',    ['as' =>'' ,     'uses' => 'Api\AuthController@refresh_token']);

Route::group(['middleware' =>['auth:api']],function(){

    Route::any('/notification/get',['as'=>'' , 'uses' => 'Api\Users\UsersController@getnotification']);
    Route::any('/notification/read',['as'=>'' , 'uses' => 'Api\Users\UsersController@markAsRead']);

    Route::get('/user_information',['as'=>'','uses'=>'Api\Users\UsersController@user_information']);
    Route::patch('/edit_user_information',['as'=>'','uses'=>'Api\Users\UsersController@update_user_information']);
    Route::patch('/edit_user_password',['as'=>'','uses'=>'Api\Users\UsersController@update_user_password']);




    Route::get('my_posts',      ['as'=>'','uses'=>'Api\Users\UsersController@my_posts']);
    Route::get('my_posts/create',      ['as'=>'','uses'=>'Api\Users\UsersController@create_post']);
    Route::post('my_posts/create',      ['as'=>'','uses'=>'Api\Users\UsersController@store_post']);
    Route::get('my_posts/{post}/edit',      ['as'=>'','uses'=>'Api\Users\UsersController@edit_post']);
    Route::patch('my_posts/{post}/edit',      ['as'=>'','uses'=>'Api\Users\UsersController@update_post']);
    Route::delete('my_posts/{post}',      ['as'=>'','uses'=>'Api\Users\UsersController@delete_post']);
    Route::post('delete_post_media/{media_id}',      ['as'=>'','uses'=>'Api\Users\UsersController@delete_post_media']);

    Route::get('/all_comments',      ['as'=>'','uses'=>'Api\Users\UsersController@all_comments']);

    Route::get('/comments/{id}/edit',      ['as'=>'','uses'=>'Api\Users\UsersController@edit_comment']);
    Route::patch('/comments/{id}/edit',      ['as'=>'','uses'=>'Api\Users\UsersController@update_comment']);
    Route::delete('/comments/{id}',      ['as'=>'','uses'=>'Api\Users\UsersController@delete_comment']);



    Route::post('logout',       ['as'=>'','uses'=>'Api\Users\UsersController@logout']);

    Route::get('details',      ['as'=>'','uses'=>'Api\Users\UsersController@details']);
});


