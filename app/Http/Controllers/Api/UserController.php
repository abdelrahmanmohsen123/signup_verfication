<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\loginUserRequest;
use App\Http\Requests\Auth\SignupUserRequest;
use App\Http\Requests\Auth\VerifyEmailUserRequest;
use App\Http\Resources\Users\UserResource;
use App\Traits\ApiResponder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\MockObject\Api;

class UserController extends Controller
{
    use ApiResponder;

    protected $userService;



    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function signup(SignupUserRequest $request)
    {


        $user = $this->userService->signUp($request->validated());

        return $this->respondResource(new UserResource($user),[
            'message' => 'Signup Success!',


        ]);
    }

    public function verifyEmail(VerifyEmailUserRequest $request)
    {

        $is_verified = $this->userService->verifyEmail($request->input('email'), $request->input('otp'));

        if (!$is_verified) {
            return $this->respondWithError('Email verification failed',401);
        }

        return $this->respondWithSuccess('Email verification success');

    }

    public function login(loginUserRequest $request)
    {
        try{

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                // dd($user);
                if ($user->is_verified) {
                    $token = $user->createToken('auth_token')->plainTextToken;
                    return $this->respondResource(new UserResource($user), [
                        'message' => 'Login Success!',
                        'token' => $token,

                    ]);
                }
                return $this->respondWithError('Email not verified',401);
            }


        } catch (\Exception $ex) {
            return $this->setStatusCode(422)->respondWithError($ex->getMessage());
        }
    }

}