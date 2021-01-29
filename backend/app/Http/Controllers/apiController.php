<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;

class apiController extends Controller
{
    public function SignUpUser(Request $req){
        //get the data from the Request
        $email = $req->get("email");
        $nom = $req->get("name");
        $password = $req->get("password");

        //conditions
        if($email==null || $password==null ||
         $email=="" || $password=="" ||
         $nom==null || $nom == ""){
            return response()->json([
                'message' => 'LES CHAMPS EMAIL ,PASSWORD ET NAME DOIVENT ETRE RENSEIGNES',
                'success' => false
            ], 200);
        }else{
            // create user
            $user = User::saveUser($req);

            //generate token
            $token = FacadesJWTAuth::fromUser($user);

            return response()->json([
                'token' => $token
            ], 200);
        }
        
    }


    public function signin(Request $req){
        $email = $req->get("email");
        $password = $req->get("password");

        if($email==null || $password==null ||
        $email=="" || $password==""){
            return response()->json([
                'message' => 'LES CHAMPS EMAIL ,PASSWORD ET 
                NAME DOIVENT ETRE RENSEIGNES',
                'success' => false
            ], 200);
        }else{
            //find the user
            $user_find = User::getUserByParams($email, $password);

            if($user_find!=null){

                //generate token
                $token = FacadesJWTAuth::fromUser($user_find);

                //send the response
                return response()->json([
                    'user' => $user_find,
                    'token' => $token,
                    'success' => true
                ], 200);

            }else{

                //send the response
                return response()->json([
                    'message' => 'CES IDENTIFIANTS N\'EXISTENT PAS',
                    'success' => false
                ], 200);

            }

            
        }
    }

    public function getResources(Request $req){
        return response()->json([
            'success' => true
        ],200);
    }

    public function getAuthenticatedUser()
            {
                    try {

                            if (! $user = FacadesJWTAuth::parseToken()->authenticate()) {
                                    return response()->json(['user_not_found'], 404);
                            }

                    // } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                    //         return response()->json(['token_expired'], $e->getStatusCode());

                    // } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    //         return response()->json(['token_invalid'], $e->getStatusCode());

                    } catch (\Exception $e) {

                            return response()->json(['token_absent'], $e->getMessage());

                    }

                    return response()->json(compact('user'));
            }



}
