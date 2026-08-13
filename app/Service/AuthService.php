<?php namespace App\Service;

use Illuminate\Support\Facades\Auth;

class AuthService{
    static function login(string $email, string $password, bool $remember = false){
        if(Auth::attempt([
            'email' => $email,
            'password' => $password
        ],$remember)){
            return true;
        }
        return false;

    }
}