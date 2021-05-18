<?php

Route::get('/', ['as' => 'frontend.index','uses' => 'Frontend\IndexController@index']);






// Authentication Route


Route::get('/login',                     [ 'as' => 'frontend.show_login_form'  ,         'uses' =>  'Frontend\Auth\LoginController@showLoginForm' ]);
Route::post('login',                     [ 'as' => 'frontend.login'  ,                   'uses' =>  'Frontend\Auth\LoginController@login' ]);

Route::get('login/{provider}',          ['as'=>'frontend.social_login'  , 'uses' =>'Frontend\Auth\LoginController@redirectToProvider']);
Route::get('login/{provider}/callback', ['as'=>'frontend.social_login_callback'  , 'uses' => 'Frontend\Auth\LoginController@handleProviderCallback']);

Route::post('logout',                    [ 'as' => 'frontend.logout'  ,                  'uses' =>  'Frontend\Auth\LoginController@logout' ]);
Route::get('register',                   [ 'as' => 'frontend.show_register_form'  ,      'uses' =>  'Frontend\Auth\RegisterController@showRegistrationForm' ]);
Route::post('register',                  [ 'as' => 'frontend.register'  ,                'uses' =>  'Frontend\Auth\RegisterController@register' ]);
Route::get('password/reset',             [ 'as' => 'password.request'  ,                 'uses' => 'Frontend\Auth\ForgotPasswordController@showLinkRequestForm' ]);
Route::post('password/email',            [ 'as' => 'password.email'  ,                   'uses' => 'Frontend\Auth\ForgotPasswordController@sendResetLinkEmail' ]);
Route::get('password/reset/{token}',     [ 'as' => 'password.reset'  ,                   'uses' => 'Frontend\Auth\ResetPasswordController@showResetForm' ]);
Route::post('password/reset',            [ 'as' => 'password.update'  ,                  'uses' => 'Frontend\Auth\ResetPasswordController@reset' ]);
Route::get('email/verify',               [ 'as' => 'verification.notice' ,                'uses' => 'Frontend\Auth\VerificationController@show']);
Route::get('email/verify/{id}/{hash}',   [ 'as' => 'verification.verify' ,                'uses' => 'Frontend\Auth\VerificationController@verify']);
Route::post('email/resend',              [ 'as' => 'verification.resend' ,                'uses' => 'Frontend\Auth\VerificationController@resend']);


Route::group(['middleware' => 'verified'],function(){
    Route::get('/dashboard',                               ['as' => 'frontend.dashboard' , 'uses' => 'Frontend\UsersController@index']);

    Route::any('user/notification/get','Frontend\NotificationsController@getnotification');
    Route::any('user/notification/read','Frontend\NotificationsController@markAsRead');
    Route::any('user/notification/read/{id}','Frontend\NotificationsController@markAsReadAndReadirect');


    Route::get('/edit-info',                               ['as' => 'users.edit_info' , 'uses' => 'Frontend\UsersController@edit_info']);
    Route::post('/edit-info',                              ['as' => 'users.update_info' , 'uses' => 'Frontend\UsersController@update_info']);

    Route::post('/edit-password',                              ['as' => 'users.update_password' , 'uses' => 'Frontend\UsersController@update_pasword']);

    Route::get('/create-post',                             ['as' => 'users.post.create' , 'uses' => 'Frontend\UsersController@create_post']);
    Route::post('/create-post',                            ['as' => 'users.post.store' , 'uses' => 'Frontend\UsersController@store_post']);

    Route::get('/edit-post/{post_id}',                     ['as' => 'users.post.edit' , 'uses' => 'Frontend\UsersController@edit_post']);
    Route::put('/edit-post/{post_id}',                     ['as' => 'users.post.update' , 'uses' => 'Frontend\UsersController@update_post']);
    Route::delete('/delete-post/{post_id}',                ['as' => 'users.post.destroy' , 'uses' => 'Frontend\UsersController@destroy_post']);

    Route::post('/delete-post-media/{media_id}',            ['as' => 'users.post.media.destroy' , 'uses' => 'Frontend\UsersController@destroy_post_media']);
    Route::get('/comments',                                 ['as' => 'users.comments' , 'uses' => 'Frontend\UsersController@show_comments']);
    Route::get('/edit-comment/{comment_id}',                ['as' => 'users.comment.edit' , 'uses' => 'Frontend\UsersController@edit_comment']);
    Route::put('/edit-comment/{comment_id}',                ['as' => 'users.comment.update' , 'uses' => 'Frontend\UsersController@update_comment']);
    Route::delete('/delete-comment/{comment_id}',           ['as' => 'users.comment.destroy' , 'uses' => 'Frontend\UsersController@destroy_comment']);

});


Route::group(['prefix' => 'admin'],function(){

    Route::get('/login',                     [ 'as' => 'admin.show_login_form'  ,         'uses' =>  'Backend\Auth\LoginController@showLoginForm' ]);
    Route::post('login',                     [ 'as' => 'admin.login'  ,                   'uses' =>  'Backend\Auth\LoginController@login' ]);
    Route::post('logout',                    [ 'as' => 'admin.logout'  ,             'uses' =>  'Backend\Auth\LoginController@logout' ]);
    Route::get('password/reset',             [ 'as' => 'admin.password.request'  ,         'uses' => 'Backend\Auth\LoginController@showLinkRequestForm' ]);
    Route::post('password/email',            [ 'as' => 'admin.password.email'  ,            'uses' => 'Backend\Auth\LoginController@sendResetLinkEmail' ]);
    Route::get('password/reset/{token}',     [ 'as' => 'admin.password.reset'  ,           'uses' => 'Backend\Auth\LoginController@showResetForm' ]);
    Route::post('password/reset',            [ 'as' => 'admin.password.update'  ,          'uses' => 'Backend\Auth\LoginController@reset' ]);

    Route::group(['middleware' => ['roles','role:admin|editor']],function(){

        Route::any('/notification/get','Backend\NotificationsController@getnotification');
       Route::any('/notification/read','Backend\NotificationsController@markAsRead');
     Route::any('/notification/read/{id}','Backend\NotificationsController@markAsReadAndReadirect');




        Route::get('/',                          [ 'as' => 'admin.index_route'  ,         'uses' =>  'Backend\AdminController@index' ]);
        Route::get('/index',                     [ 'as' => 'admin.index'        ,         'uses' =>  'Backend\AdminController@index' ]);

        Route::post('/posts/removeImage/{media_id}' ,         ['as' =>'admin.posts.media.destroy','uses' => 'Backend\PostsController@removeImage']);
        Route::resource('posts',                      'Backend\PostsController',['as' => 'admin']);

        Route::post('/pages/removeImage/{media_id}' ,  ['as' =>'admin.pages.media.destroy','uses' => 'Backend\PagesController@removeImage']);
        Route::resource('pages',                      'Backend\PagesController',['as' => 'admin']);
        Route::resource('post_comments',              'Backend\PostCommentsController',['as' => 'admin']);
        Route::resource('post_categories',            'Backend\PostCategoriesController',['as' => 'admin']);
        Route::resource('post_tags',                   'Backend\PostTagsController',['as' => 'admin']);

        Route::post('/users/removeImage' ,  ['as' =>'admin.users.remove_image','uses' => 'Backend\UsersController@removeImage']);

        Route::resource('users',                      'Backend\UsersController',['as' => 'admin']);
        Route::resource('contact_us',                 'Backend\ContactUsController',['as' => 'admin']);
        Route::post('/supervisors/removeImage' ,  ['as' =>'admin.supervisors.remove_image','uses' => 'Backend\SupervisorsController@removeImage']);

        Route::resource('supervisors',                 'Backend\SupervisorsController',['as' => 'admin']);
        Route::resource('settings',                  'Backend\SettingsController',['as' => 'admin']);


    });


});




Route::get('/Contact-us',                       ['as'      => 'frontend.Contact',                          'uses' => 'Frontend\IndexController@Contact']);
Route::post('/Contact-us',                      ['as'      => 'frontend.do_Contact',                       'uses' => 'Frontend\IndexController@do_Contact']);
Route::get('/category/{category_slug}',         ['as'       => 'frontend.category.posts',                  'uses' => 'Frontend\IndexController@catgory']);
Route::get('/tag/{tag_slug}',                   ['as'       => 'frontend.tag.posts',                       'uses' => 'Frontend\IndexController@tag']);
Route::get('/archive/{date}',                   ['as'       => 'frontend.archive.posts',                   'uses' => 'Frontend\IndexController@archive']);
Route::get('/author/{username}',                ['as'       => 'frontend.author.posts',                   'uses'  => 'Frontend\IndexController@author']);
Route::get('/search',                           ['as'      => 'frontend.search',                           'uses' => 'Frontend\IndexController@search']);
Route::get('/{post}',                           ['as'      => 'posts.show',                                'uses' => 'Frontend\IndexController@post_show']);
Route::post('/{post}',                          ['as'      => 'posts.add_comment',                         'uses' => 'Frontend\IndexController@store_comment']);
