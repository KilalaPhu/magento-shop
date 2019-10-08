<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [ 'localizationRedirect' ]
], function () {

    // Admin
    Route::group(['prefix' => 'admin',  'middleware' => 'auth'], function()
    {
        Route::get('/', function () {
            return view('admin.pages.dashboard');
        })->name('admin');

        //Users
        Route::resource('/users', 'UsersController');
        Route::get('/users/{user_id}/delete','UsersController@delete');

        //CATEGORY
        Route::resource('/categories', 'CategoriesController');
        Route::get('/categories/{id}/delete','CategoriesController@destroy')->name('categories.delete');

        //Sponsor
        Route::resource('/sponsor', 'SponsorController');
        Route::get('/sponsor/{id}/delete','SponsorController@destroy')->name('sponsor.delete');

        //Post
        Route::resource('/posts', 'PostsController');
        Route::get('/posts/{id}/delete','PostsController@destroy')->name('posts.delete');

        // Emails Template
        Route::resource('/emails-template', 'EmailsTemplateController');

        //Tags
        Route::resource('/tags', 'TagsController');
        Route::get('/tags/{id}/delete','TagsController@destroy')->name('tags.delete');

        //Languages
        Route::resource('/languages', 'LanguagesController');
        Route::get('/languages/{id}/delete','LanguagesController@destroy')->name('languages.delete');

        // MediaManager
        ctf0\MediaManager\MediaRoutes::routes();

        // Newsletter
        Route::get('/newsletter',[
            'uses'=>'NewsLetterController@index',
            'as'=>'newsletterAdmin'
        ]);
        Route::get('/newsletter/{id}/delete','NewsLetterController@destroy')->name('newsletterAdmin.delete');

        Route::group(['namespace' => 'Settings'], function () {
            Route::get('settings/{lang_slug}/edit', 'SettingsController@edit')->name('settings.edit');
            Route::post('settings/{lang_slug}', 'SettingsController@update')->name('settings.update');
            //Route::resource('settings', 'SettingsController', ['except' => ['show', 'create', 'save', 'index', 'destroy']]);
            Route::post('removeicon/{setting}', 'SettingsLogoController@destroy')->name('removeIcon');
        });

        includeRouteFiles(__DIR__.'/Backend/');

    });

    //---------Frontent--------//
    // Newsletter
    Route::get('/newsletter',[
        'uses'=>'NewsLetterController@create',
        'as'=>'newsletter'
    ]);
    Route::post('/newsletter/apply',[
        'uses'=>'NewsLetterController@store',
        'as'=>'newsletter.apply'
    ]);
    Route::get('/newsletter/unsubscribe',[
        'uses'=>'NewsLetterController@unsubscribe',
        'as'=>'newsletter.unsubscribe'
    ]);
    Route::post('/newsletter/delete','NewsLetterController@destroy')->name('newsletter.delete');

});

Route::group(['prefix' => 'admin'], function()
{
    Route::auth();
    Route::get('/logout', function () {
        Auth::logout();
        return redirect('admin/login');
    });
});


