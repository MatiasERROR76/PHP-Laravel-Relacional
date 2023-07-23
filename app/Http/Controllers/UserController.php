<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\User;

class UserController extends Controller
{
    public function pruebas (Request $request){
    return "Accion de pruebas de USER-CONTROLLER";
  }

  public function register(Request $request){

    //RECOGER LOS DATOS DEL USUARIO POR POST
    $json = $request->input('json', null);
    $params = json_decode($json); //objeto
    $params_array = json_decode($json, true); //aray


    if(!empty($params) && !empty($params_array)){

    // limpiar los datos
    $params_array = array_map('trim', $params_array);


    //VALIDAR DATOS
    $validate = \Validator::make($params_array, [
      'name'      => 'required|alpha',
      'surname'   => 'required|alpha',
      'email'     => 'required|email|unique:users',
      'password'  => 'required'


    ]);
    if($validate->fails()){
      // la validacion ha fallado
        $data = array (
        'status' => 'error',
        'code' => 404,
        'message' => 'El usuario no se ha creado',
        'errors' => $validate->errors()
      );
    }else{

      //validacion pasada correctamente 
    //CIFRAR CONTRASEÑA
    $pwd = hash('sha256',$params->password);
    //COMPROBAR SI EL USUARIO EXISTE YA (DUPLICADO)
    
    //CREAR EL USUARIO
      $user = new User();
      $user->name = $params_array['name'];
      $user->surname = $params_array['surname'];
      $user->email = $params_array['email'];
      $user->password = $pwd;
      $user->role = 'ROLE_USER';


// guardar el usuario
 $user->save(); 

      
      $data = array(
        'status' => 'success',
        'code' => 200,
        'message' => 'El usuario se ha creado correctamente',
        'user' => $user
        );

        
    }
 
  }else{
    $data = array (
      'status' => 'error',
      'code' => 404,
      'message' => 'Los datos enviados no son correctos',
    );

  }

      
      return response()->json($data, $data['code']);
  }




  public function login(Request $request){
    
    $jwtAuth = new \JwtAuth();

    // recibir datos por post
    $json = $request->input('json', null);
    $params = json_decode($json);
    $params_array = json_decode($json, true);
    
    // validar esos datos
    $validate = \Validator::make($params_array, [

      'email'     => 'required|email',
      'password'  => 'required'


    ]);
    if($validate->fails()){
      // la validacion ha fallado
        $signup = array (
        'status' => 'error',
        'code' => 404,
        'message' => 'El usuario no se ha podido identificar',
        'errors' => $validate->errors()
      );
    }else{

    // cifrar pw

    $pwd = hash('sha256', $params->password);

    // devolver token o datos
    $signup = $jwtAuth->signup($params->email, $pwd);

    if(!empty($params->gettoken)){
      $signup = $jwtAuth->signup($params->email, $pwd, true);

    }
  }
  
  

    return response()->json($signup, 200);

  }

  public function update(Request $request){
    // comprobar si el usuario esta identificado

    $token = $request->header('Authorization');
    $jwtAuth = new \JwtAuth();
    $checkToken = $jwtAuth->checkToken($token);

      // recoger los datos por post 
      $json = $request->input('json', null);
      $params_array = json_decode($json, true);


    if($checkToken && !empty($params_array)){


      //sacar usuario identificado
      $user = $jwtAuth->checkToken($token, true);

      // validar los datos
      $validate = \Validator::make($params_array, [
        'name'      => 'required|alpha',
        'surname'   => 'required|alpha',
        'email'     => 'required|email|unique:users,'.$user->sub
      ]);

      // quitar los campos que no quiero actualizar 
      unset($params_array['id']);
      unset($params_array['role']);
      unset($params_array['password']);
      unset($params_array['created_at']);
      unset($params_array['remember_token']);

      // actualizar el usuario en bbdd
      $user_update = User::where('id', $user->sub)->update($params_array);

      // devolver array con el resultado
      $data = array(
        'code' => 200,
        'status' => 'success',
        'user' => $user,
        'change' => $params_array

      );


    }else{
      $data = array(
        'code' => 400,
        'status' => 'error',
        'message' => 'el usuario no esta identificado.'
      );
    }
    return response()->json($data, $data['code']);
  }



  
  public function upload(Request $request){

    // recoger la imagen
    $image = $request->file('file0');
    
    // validacion de imagen

    
  $validate = \Validator::make($request->all(), [
      'file0' => ['required','mimes:jpg,jpeg,png,gif']
    ]);



    // guarda imagen
      if(!$image || $validate->fails()){
             $data = array(
        'code' => 400,
        'status' => 'error',
        'message' => 'error al subir imagen'      
      );
      
 
    }else{

      $image_name = time().$image->getClientOriginalName();
      \Storage::disk('users')->put($image_name, \File::get($image));

      $data = array(
        'code' => 200,
        'status' => 'success',
        'image' => $image_name
      );

    }

    return response()->json($data, $data['code']);
  }

  public function getImage($filename){
    $isset = \Storage::disk('users')->exists($filename);
    if($isset){
    $file = \Storage::disk('users')->get($filename);
    return new Response($file, 200);
    }else{
      $data = array(
        'code' => 400,
        'status' => 'error',
        'message' => ' la imagen no existe' 
           );
    return response()->json($data, $data['code']);

    } 
  }


  public function detail($id){
    $user = User::find($id); 
    
    if(is_object($user)){
      $data = array(
        'code' => 200,
        'status' => 'success',
        'user' => $user
      );
    }else{
      $data = array(
        'code' => 404,
        'status' => 'error',
        'message' => 'el usuario no existe.'
      );
    }
    return response()->json($data, $data['code']);


  }





}