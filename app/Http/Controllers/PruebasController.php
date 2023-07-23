<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category; 
use App\Producto; 


class PruebasController extends Controller
{
  public function index(){
    $titulo  = 'animales';
    $animales = ['perro','gato','tigre'];

    return view ('pruebas.index', array(
        'titulo' => $titulo,
        'animales' => $animales

    ));
  }

  public function testOrm(){
/*
    $posts = Post::all();
    foreach($posts as $post){
      echo "<h1>".$post->title."</h1>";
      echo "<span style='color:gray;'>{$post->user->name} - {$post->category->name} </span>";
      echo "<p>".$post->content."</p>";
      echo "<hr>";
    }*/
    $productos = Producto::all();
    foreach($productos as $producto)
    echo "<h1>".$producto->NOMBRE."</h1>";


    $categories = Category::all();
    print($categories);
    foreach($categories as $category){

      echo "<h1>{$category->name}</h1>";

      foreach($category->posts as $post){
        echo "<h1>".$post->title."</h1>";
        echo "<span style='color:gray;'>{$post->user->name} - {$post->category->name} </span>";
        echo "<p>".$post->content."</p>";
      }
      echo "<hr>";

      echo 'chupala matias';
    }

    die();
  }

}
