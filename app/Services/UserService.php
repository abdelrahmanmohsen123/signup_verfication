<?php
namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;



class UserService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function signUp(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepo->createUser($data);
        $user->generateOtp();
        $user->sendVerificationEmail();
        return $user;
    }

    public function verifyEmail($email, $otp)
    {
        $user = $this->userRepo->getUserByEmail($email);
        if ($user && $user->verifyEmail($otp)) {
            return true;
        }
        return false;
    }
}







?>