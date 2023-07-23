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

// cargando clases

use App\Http\Middleware\ApiAuthMiddleware;


//RUTAS DE PRUEBA
Route::get('/', function () {
  return '<h1> Hola desde laravale</h1>';
});


Route::get('/welcome', function () {  
    return view('welcome');
});

Route::get('/pruebas/{nombre?}', function ($nombre = null) {
  $texto =  '<h2>Texto desde una ruta </h2> ';
  $texto .= 'Nombre: '.$nombre; 
  
  return view('pruebas', array(
    'texto' => $texto
  ));
});

Route::get('/animales', 'PruebasController@index');
Route::get('/test-orm', 'PruebasController@testOrm');



// RUTAS DEL API

//metodos http comunes GET,POST,PUT,DELETE APIRESTFULL


//rutas de prueba
Route::get('/usuario/pruebas', 'UserController@pruebas');
Route::get('/categoria/pruebas', 'CategoryController@pruebas');
Route::get('/entrada/pruebas', 'PostController@pruebas');

//rutas del controlador de usuarios

Route::post('api/register', 'Usercontroller@register');
Route::post('api/login', 'Usercontroller@login'); 
Route::put('api/user/update', 'Usercontroller@update'); 
Route::post('api/user/upload', 'Usercontroller@upload')->middleware(ApiAuthMiddleware::class); 
Route::get('api/user/avatar/{filename}', 'Usercontroller@getImage');
Route::get('api/user/detail/{id}', 'Usercontroller@detail');


// rutas categorias
Route::resource('/api/category', 'CategoryController');

// rutas post
Route::resource('/api/post', 'PostController');
Route::post('/api/post/upload', 'PostController@upload');
Route::get('/api/post/image/{filename}', 'PostController@getImage');
Route::get('/api/post/category/{id}', 'PostController@getPostsByCategory');
Route::get('/api/post/user/{id}', 'PostController@getPostsByUser');



