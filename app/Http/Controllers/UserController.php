<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use App\Mail\resetPassword;
use App\Mail\VerifyEmail;
use Carbon\Carbon;
use App\User;
use App\Type;

class UserController extends Controller
{
    public function store(Request $request)
    {
		switch ($request->type_id) {
			case 1:
				$resp = $this->storeHospital($request);
				return response()->json([
		    		'resp'=>$resp['resp'],
		    		'data'=>$resp['data']
		    	], $resp['code']);
				break;
			case 3:
				$resp = $this->storePatient($request);
				return response()->json([
		    		'resp'=>$resp['resp'],
		    		'data'=>$resp['data']
		    	], $resp['code']);
				break;
			default:
				return response()->json([
		    		'resp'=>'Tipo de usuario no permitido',
		    		'data'=>''
		    	], 400);
				break;
		}
	}

    public function login(Request $request)
    {
    	$credentials = request(['email', 'password']);
		$token = null;

		//validar q el correo este  verificado
		$user = User::where('email',$request->email)->first();
		if ($user->email_verified_at == null) {
			return response()->json([
	    		'resp'=>'No autorizado,Debe Activar la Cuenta ',
	    		'data'=>''
	    	], 401);
		}

		 //validar que el medico ya cambio la contraseña
		if (($user->first_time == 0) && ($user->type_id == 2)) {
			$token = $random = Str::random(40);
	    	DB::table('password_resets')->insert([
	            'email' => $request->email,
	            'token' => $token,
	            'created_at' => Carbon::now(),
	        ]);

			$correo['url'] = request()->root().'/reset/password/'.$token;

			// Mail::to($request->email)->send(new resetPassword($correo));
			return response()->json([
	    		'resp'=>'No autorizado, Se le envio un correo para cambiar la contraseña ',
	    		'data'=>''
	    	], 401);
		}


        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
	    		'resp'=>'Usuario y contraseña no coinciden',
	    		'data'=>''
	    	], 401);
        }

        return response()->json([
			'resp'=>'Login Exitoso',
			'data'=>[
	            'access_token' => $token,
	            'expires_in' => JWTAuth::factory()->getTTL() * 60
	        ]
	    ], 200);
    }

    public function logout(Request $request)
    {
    	try {
    		if (!$request->token) {
	    		return response()->json([
					'resp'=>'error, Token obligatorio',
					'data'=>''
		    	], 400);
	    	}	

	    	JWTAuth::invalidate($request->token);

	    	return response()->json([
		    		'resp'=>'Hasta Luego',
		    		'data'=>''
		    ], 200);
    		
    	} catch (Exception $e) {
    		return response()->json([
		    		'resp'=>'Error inesperado inténtelo de nuevo',
		    		'data'=>$e
		    ], 500);
    	}
    	
    }

    /* registro de un hospital */
    public function storeHospital($request){
    	try {
    		DB::beginTransaction();
    		$validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'phone' => 'required|max:14',
                'password' => 'required|string|min:6',
                'address' => 'required',
                'services' => 'required|array',
            ]);
            if ($validator->fails()) {
                return[
                    'resp' => 'error',
                    'data' => $validator->errors(),
                    'code' => 400
                ];
            }

			$token_verify = $random = Str::random(40);
	    	$user = new User();
	    	$user->name = $request->name;
			$user->email = $request->email;
			$user->phone = $request->phone;
			$user->password = Hash::make($request->password);
			$user->address = $request->address;
			$user->remember_token = $token_verify;
			$user->type_id = 1;
			$user->save();

			$data = [];
			foreach ($request->services as $key => $value) {
				array_push($data, ['user_id'=>$user->id, 'service_id' => $value]);
			}
			DB::table('services_users')->insert($data);

			$correo['name'] = $request->name;
			$correo['url'] = request()->root().'/verify/'.$token_verify;

			// Mail::to($request->email)->send(new VerifyEmail($correo));
	        
	    	DB::commit();
	    	return [
	    		'resp'=>'Hospital Registrado con Exito, Verifique su correo Electronico',
	    		'data'=>$user,
	    		'code' =>200
	    	];
	    } catch (Exception $e) {
	    	DB::rollback();
	    	return [
	    		'resp'=>'Error inesperado inténtelo de nuevo',
	    		'data'=>$e,
	    		'code' =>200
	    	];
	    }
    }

    /* registro de un de un doctor */
	public function storeDoctor(Request $request){
		try {
			$validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'phone' => 'required|max:14',
                'password' => 'required|string|min:6',
                'address' => 'required',
                'specialty' => 'required|string',
                'parent_id' =>'required|integer',
                'token' => 'required',
            ]);
            if ($validator->fails()) {
                return [
                    'resp' => 'error',
                    'data' => $validator->errors(),
                    'code' =>400,
                ];
            }
            if(JWTAuth::user()->type_id != 1 ){
            	return [
	    		'resp'=>'No autorizado',
	    		'data'=>'',
	    		'code'=>401,
	    		];
            }

            $token_verify = $random = Str::random(40);
			$user = new User();
	    	$user->name = $request->name;
			$user->email = $request->email;
			$user->phone = $request->phone;
			$user->password = Hash::make($request->password);
			$user->address = $request->address;
			$user->specialty = $request->specialty;
			$user->type_id = 2;
			$user->remember_token = $token_verify;
			$user->parent_id = $request->user_id;
			$user->save();

			$correo['name'] = $request->name;
			$correo['url'] = request()->root().'/verify/'.$token_verify;

			// Mail::to($request->email)->send(new VerifyEmail($correo));

			return [
	    		'resp'=>'Medico Registrado con Exito, Verifique su correo Electronico',
	    		'data'=>$user,
	    		'code'=>200,
	    	];
			
		} catch (Exception $e) {
	    	return [
	    		'resp'=>'Error inesperado inténtelo de nuevo',
	    		'data'=>$e,
	    		'code'=>500,
	    	];
		}
    }

    /* registro de un paciente */
	public function storePatient($request){
		try {
			$validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'phone' => 'required|max:14',
                'password' => 'required|string|min:6',
                'address' => 'required',
                'birthdate' => 'required|date',
            ]);
            if ($validator->fails()) {
                return [
                    'resp' => 'error',
                    'data' => $validator->errors(),
                    'code'=>400,
                ];
            }
			$token_verify = $random = Str::random(40);
			$birthdate = new Carbon($request->birthdate);
	    	$user = new User();
	    	$user->name = $request->name;
			$user->email = $request->email;
			$user->phone = $request->phone;
			$user->password = Hash::make($request->password);
			$user->address = $request->address;
			$user->birthdate = $birthdate;
			$user->type_id = 3;
			$user->remember_token = $token_verify;
			$user->save();

			$correo['name'] = $request->name;
			$correo['url'] = request()->root().'/verify/'.$token_verify;

			// Mail::to($request->email)->send(new VerifyEmail($correo));
			return [
	    		'resp'=>'Usuario Registrado con Exito, Verifique su correo Electronico',
	    		'data'=>$user,
	    		'code'=>200,
	    	];
			
		} catch (Exception $e) {
			return [
	    		'resp'=>'Error inesperado inténtelo de nuevo',
	    		'data'=>$e,
	    		'code'=>500,
	    	];
		}
    }

    /*Listado de tipos  de usuarios*/
    public function getTypes(){
    	$type = Type::all();
    	return response()->json([
    		'resp'=>'Tipos de usuarios',
    		'data'=>$type
    	], 200);
    }

    public function getHospitals(){
    	$users = User::where('type_id', 1)->get();
    	return response()->json([
    		'resp'=>'Hospitales',
    		'data'=>$users
    	], 200);
    }

    public function getPatients(){
     	if (JWTAuth::user()->type_id == 3) {
     		return response()->json([
	    		'resp'=>'No autorizado',
	    		'data'=>''
	    	], 401);
     	}

    	$users = User::where('type_id', 3)->get();
    	return response()->json([
    		'resp'=>'Pasientes',
    		'data'=>$users
    	], 200);
    }

    public function verify(Request $request){
    	try {
    		$now  = Carbon::now();
    		$user = User::where('remember_token', $request->token)->first();
	    	if (!$user) {
	    		return response()->json([
		    		'resp'=>'Error, El token no es valido',
		    		'data'=>$type
		    	], 400);
	    	}
			$user->email_verified_at = $now;
			$user->save();

			return response()->json([
	    		'resp'=>'Cuenta Activa',
	    		'data'=>''
	    	], 200);

    	} catch (Exception $e) {
    		return [
	    		'resp'=>'Error inesperado inténtelo de nuevo',
	    		'data'=>$e,
	    		'code'=>500,
	    	];	
    	}
    	
    }

    // cambiar contraseña
    public function changePassword(Request $request){
    	try {
    		$data = DB::select('select email from password_resets where token = ?', [$request->token]);
    		if($data){
	    		$user = User::where('email',$data[0]->email)->first();
	    		if (!$user) {
	    			return response()->json([
			    		'resp'=>'Correo no existe',
			    		'data'=>$data[0]->email
			    	], 400);
	    		}
	    		$user->password = Hash::make($request->password);
	    		$user->first_time =1;
	    		$user->save();
	    		DB::delete('delete from password_resets where email = ?', [$data[0]->email]);

	    		return response()->json([
		    		'resp'=>'Contraseña Modificada con extito',
		    		'data'=>''
		    	], 200);
	    	}else {
	    		return response()->json([
		    		'resp'=>'Error, El token no es valido',
		    		'data'=>''
		    	], 400);
	    	}	
    	} catch (Exception $e) {
    		return [
	    		'resp'=>'Error inesperado inténtelo de nuevo',
	    		'data'=>$e,
	    		'code'=>500,
	    	];		
    	}
    }

	public function resetPassword(Request $request){
		$token = $random = Str::random(40);
    	DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

		$correo['url'] = request()->root().'/reset/password/'.$token;

		// Mail::to($request->email)->send(new resetPassword($correo));
        return response()->json([
    		'resp'=>'Correo enviado',
    		'data'=>''
    	], 200);
    }


}
