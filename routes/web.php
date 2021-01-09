<?php

use App\User;
use App\Address;
use App\Post;
use App\Role;

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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/admin/login', 'Auth\AdminLoginController@showLoginForm')->middleware('admin_guest');
Route::post('/admin/login', 'Auth\AdminLoginController@login');
Route::get('/admin/home', 'Auth\AdminLoginController@home')->middleware('admin_auth');
Route::get('/admin/logout', 'Auth\AdminLoginController@logout');


/////////////////////////////////////////////////// one To One \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


Route::group(['prefix' => 'onetoone'], function () {

    Route::get('create', function () {
        //Grabing a certain user from the database
        $user = User::findOrFail(2);

        /*
        | Here we will be creating a new record in the address table and linking it with the users id as a foreign key
        | we dont have to do this through the address model like the traditional way instead we can just
        | access the addreses table through the address() method made from the relationship.
        */
        $address = new address(['address' => 'Baqa']);
        $user->address()->save($address);

        // return $user->address;
    });
    Route::get('read', function () {
        //Grabing a certain user from the database
        $user = User::findOrFail(2);
        //We can access the user's address through the address() method made by the hasOne relationship.
        return $user->address->address;
        /*
        | In one to one relations there must be only one record contaning a certain foreign key
        | because it returns an object containing the record matching the foreign id.
        | In the other hand the one to many relation returns an array of objects which needs to be looped over
        */
    });
    Route::get('read-inverse', function () {
        //Grabing a certain address from the database
        $address = Address::findOrFail(1);
        //We can access the user from an address through the user() method made by the belongsTo relationship.
        return $address->user->email;
    });
});



/////////////////////////////////////////////////// One To Many \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


Route::group(['prefix' => 'onetomany'], function () {

    Route::get('create', function () {
        //Grabing a certain user from the database
        $user = User::findOrFail(2);

        /*
         | Here we will be creating a new record in the posts table and linking it with the users id as a foreign key
         | we dont have to do this through the posts model like the traditional way instead we can just
         | access the posts table through the posts() method made from the relationship.
         */
        $post = new Post(['title' => 'Second Post', 'body' => 'Ok maria']);

        $user->posts()->save($post);
    });
    Route::get('read', function () {
        //Grabing a certain user from the database
        $user = User::findOrFail(2);

        /*
        | We can access the user's posts through the posts() method made by the hasMany relationship.
        | Here in the many to many relationship we nedd to loop over the results
        | cause it returns a collection of objects which is basicaly an array that needs to be looped over to access its contents.
        */

        foreach ($user->posts as $post) {
            echo $post->body . "<br>";
        }

        /*
        | In thr other hand one to one relations there must be only one record contaning a certain foreign key
        | because it returns an object with containing the record matching the foreign id.
        */
    });
    Route::get('read-inverse', function () {
        //Grabing a certain user from the database
        $post = Post::findOrFail(2);
        //We can access the user from a post through the user() method made by the belongsTo relationship.

        return $post->user->name;
    });
    Route::get('test', function () {
        $posts = Post::all();

        return view('Auth.admin_home', compact('posts'));
    });
    Route::get('delete', function () {
        $user = User::find(2);
        foreach ($user->posts as $post) {
            $post->where('id', '3')->delete();
        }
        return $user->posts;
    });
});


/////////////////////////////////////////////////// Many To Many\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


Route::group(['prefix' => 'manytomany'], function () {

    Route::get('create', function () {
        $user = User::findOrFail(2);
        /*
        | Here we will be creating a new record in the roles table and insert it in a third table called a pivot table which will | hold the user_id and the role_id.
        | we dont have to do this through the role model like the traditional way instead we can just
        | access the roles table through the roles() method made from the relationship.
        */
        $role = new Role(['type' => 'admin']);
        $user->roles()->save($role);
    });
    Route::get('read', function () {
        $user = User::findOrFail(2);

        foreach ($user->roles as $role) {
            echo $role->type . "<br>";
        }
    });
    Route::get('read-inverse', function () {
        $role = Role::findOrFail(1);
        //We can access the users from a user through the users() method made by the belongsTo relationship.
        foreach ($role->users as $user) {
            echo $user->name . "<br>";
        }
    });


    Route::get('attach', function () {
        $user = User::findOrFail(2);

        //The attach method takes both the keys from the user_id and the role_id and inserts them in the pivot table
        $user->roles()->attach(1);
    });
    Route::get('detach', function () {
        $user = User::findOrFail(2);
        //The detach method takes both the keys from the user_id and the role_id and removes them from the pivot table
        $user->roles()->detach(1);
    });
    Route::get('sync', function () {
        $user = User::findOrFail(2);
        //The sync method overwrites all the records in the pivot table and replaces them with the given array of role id's.
        $user->roles()->sync([1, 3]);
    });
});
