<?php namespace App\Service;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService{
    static function login(string $email, string $password, bool $remember = false){
        $user = User::where('email',$email)->first();
        if(!$user){
            return ["result" => false,"message" => "Akun belum terdaftar"];
        }
        if($user->status !== "active"){
            return ["result" => false,"message"=>"Akun belum aktif"];
        }
        if(Auth::attempt([
            'email' => $email,
            'password' => $password
        ],$remember)){
            session()->regenerate();
            return ["result"=>true,];
        }
        return ["result"=>false,"message"=>"email atau password salah"];
    }

    static function logout(){
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}