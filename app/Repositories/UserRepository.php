<?php


namespace App\Repositories;
use App\Models\User;


class UserRepository{
    public function createUser($data){
        $user = User::create($data);
        return $user;

    }

    public function getUserByEmail($email){

        $user = User::where('email',$email)->get();
        return $user;

    }


}


?>