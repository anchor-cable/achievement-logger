<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Users;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class AuthController extends Controller
{
    private function jwt(User $user)
    {
        $signer = new Sha256();
        $token = (new Builder())->setIssuer('http://achievement-logger')
            ->setAudience('http://achievement-logger')
            ->setId(uniqid(), true)
            ->setIssuedAt(time())
            ->setNotBefore(time() + 60)
            ->setExpiration(time() + 3600)
            ->set('uid', $user->id)
            ->sign($signer, env('JWT_SECRET'))
            ->getToken();

        return $token;
    }

    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = Users::where('email', $request->input('email'))->first();

        if (!$user) {
            return response()->json(['error' => 'login denied!'], 400);
        }

        if (Hash::check($request->input('password'), $user->password)) {
            return response()->json($this->jwt($user)->__toString(), 200);
        }

        return response()->json(['error' => 'login disaccepted!'], 400);
    }

    public function authTest(Request $request)
    {
        return 'fugafuga';
    }
}