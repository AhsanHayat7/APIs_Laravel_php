<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($flag)
    {
        //
        // p('Get Apis is working');

        $query = User::select('email','name');
        if($flag == 1){
            $query->where('status', 1);
        }
        elseif($flag == 0){
            //empty
        }
        else{
            return response()->json([
                'message' => "Invalid parameter passed, either it can be 0 or 1",
                'status'=> 0,
            ],400);
        }
        $users = $query->get();
        if(count($users) > 0 )
        {
            $response = [
                "message" => count($users) . ' users found',
                "status"=> 1,
                'data'=> $users,
            ];
        }
        else{
            $response = [
                "message" => count($users) . ' users found',
                "status"=> 0,
            ];

        }
        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // p($request->all());

        $validator = Validator::make($request->all(),
        [
            'name' => ['required'],
            'email'=> ['required', 'email', 'unique:users,email'],
            'password'=> [ 'required', 'min:8','confirmed'],
            'password_confirmation' => ['required']
        ]);
        if($validator->fails()){
            return response()->json($validator->messages(), 400);
        }
        else{
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                // 'password' => Hash::make($request->password),
                'password'=> $request->password,
            ];
            // p($data);
            DB::beginTransaction();
            try{
                $user = User::create($data);
                DB::commit();
            }
            catch(\Exception $e){
                DB::rollBack();
                // p($e->getMessage());
                $user = null ;
            }
            if($user != null){
                return response()->json([
                    "message"=> "User Register Successfully"], 200);
            }else{
                return response()->json([
                    "message"=> "Internal Server Error"], 500);

            }

        }

         p($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $users = User::find($id);
        if(is_null($users)){
           $response = [ 'message' => "user not found",
           'status'=> 0,
           ];
        }else
            $response = [
                'message' => "User found",
                'status'=> 1,
                'data'=> $users

            ];

            return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = User::find($id);
        // p($request->all());
        // die;
        if(is_null($user)){
            return response()->json([
                'message'=> "User doesn't exits found",
                'status'=> 0,
            ], 404);
        }
        else{
            DB::beginTransaction();
            try{
            $user->name = $request->name;
            $user->email =  $request->email;
            $user->pincode = $request->pincode;
            $user->address = $request->address;
            $user->contact = $request->contact;
            $user->save();
            DB::commit();



        }catch(\Exception $err){
            DB::rollBack();
            $user = null;
        }
        if(is_null($user)){

            return response()->json(
            $response = [
                 "message" => "Internal Server Error",
                "status"=>  0,
                "error_msg"=> $err->getMessage()
            ],
            500);
         }
         else{
            return response()->json(
                $response = [
                     "message" => "Datat Updated Successfully",
                    "status"=>  1,
                ],
                200);

         }

         return response()->json($response);

    }
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $users = User::find($id);

        if(is_null($users)){
            $response = [
                'message'=> "User doesn't Exits",
                'status'=> 0,
            ];
            $respCode = 404;
        }
        else{
            DB::beginTransaction();
            try{
                $users->delete();
                DB::commit();
                $response = [
                    "message" => "User Deleted Successfully",
                    "status" => 1,


                ];
                $respCode = 200;
            }
            catch(\Exception $err){
                DB::rollBack();
                $response = [
                    "message" => "Internal Server Error",
                    "status" => 0,


                ];
                $respCode = 500;

            }
        }
        return response()->json($response,$respCode,);
    }

    public function  get($id =  null){
          //
          if(!is_null($id)){
            $user = User::find($id);
          }else{
             $user = User::get();
      } $usercount = count($user);
      if($usercount > 0 ){
            $response = [
                "message"=> $usercount . " User are found",
                "status" => 1,
                "data"=>$user

            ];

    }else{
        $response = [
            "message"=> "User doesnot exits",
            "status"=> 0,
            "data"=>$user
        ];
    }
    return response()->json($response);
    }




    public function changedpassword(Request $request, $id){

        $user = User::find($id);
        if(is_null($user)){

            return response()->json(
                [
                    'status'=> 0,
                    'message'=> "User Doesnt Exist",

                ], 404
            );
        }
        else{
            if($user->password == $request->old_password){
                //change
                if($request->new_password == $request->confirm_password){
                    DB::beginTransaction();
                    try{
                        $user->password = $request->new_password;
                        $user->save();
                        DB::commit();

                    }catch(\Exception $e){
                        $user =null;
                        DB::rollBack();
                    }
                    if(is_null($user)){

                        return response()->json(
                        $response = [
                             "message" => "Internal Server Error",
                            "status"=>  0,
                            "error_msg"=> $e->getMessage()
                        ],
                        500);
                     }
                     else{
                        return response()->json(
                            $response = [
                                 "message" => "Password Updated Successfully",
                                "status"=>  1,
                            ],
                            200);

                     }
                }else{
                    return response()->json(
                        [
                            'status'=> 0,
                            'message'=> "New password and Confirm password doesn't match",

                        ], 400
                    );
                }
            }else{
                return response()->json(
                    [
                        'status'=> 0,
                        'message'=> "Old Password Doesn't Match",

                    ], 400
                );

            }
        }
    }


    public function register(Request $request){
        $validatordata = $request->validate([
            'name'=> 'required',
            'email'=> 'required|email',
            'password'=> 'min:8|confirmed'
        ]);

        $user = User::create($validatordata);
        $token = $user->createToken('auth_token')->accessToken;

            return response()->json([
                'token'=> $token,
                'user'=> $user,
                'message'=> "User created  Successfully.",
                'status'=> 1,


            ], );
    }




    public function login(Request $request){
        $validatordata = $request->validate([
            'email'=> 'required|email',
            'password'=> 'required'
        ]);
        $user = User::where(['email'=> $validatordata['email'] ,'password'=> $validatordata['password']])->first();
        $token = $user->createToken('auth_token')->accessToken;
        return response()->json([
            'token'=> $token,
            'user'=> $user,
            'message'=> "User Logged in  Successfully.",
            'status'=> 1,


        ], );

    }

    public function getUser($id){
        $user  =  User::find($id);
        if(is_null($user)){
            return response()->json([
                'user'=> null,
                'message'=> "No User Found",
                'status'=> 0,


            ], );
        }else{
            return response()->json([
                'message'=> "User Found Successfully.",
                'status'=> 1,
                'user' => $user,


            ], );

        }
    }

}
