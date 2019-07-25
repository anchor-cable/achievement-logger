<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use App\Users;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = $request->get('token');

        if(!$token){
            return response()->json([
                'error' => 'Token not provided'
            ],401);
        }

        $signer = new Sha256();
        $data = new ValidationData();
        $data->setIssuer('http://achievement-logger');
        $data->setAudience('http://achievement-logger');
        $data->setCurrentTime(time()+60);

        try {
            $token=(new Parser())->parse((string)$token);

            if(!$token->validate($data)){
                throw new Exception('バリデーションエラーです');
            }
            if(!$token->verify($signer,config('jwt_secret'))){
                throw new Exception('署名のエラーです');
            }

            $user = Users::findOrFail($token->getClaim(('uid')));


        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()],400);
        }

        $request->user = $user;

        return $next($request);
    }
}
