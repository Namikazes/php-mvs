<?php

namespace App\Controllers\Api;

use Core\Controller;
use App\Models\User;
use ReallySimpleJWT\Token;

class BaseApiController extends Controller
{
    public function before(string $action, array $params = []): bool
    {
        $headers = apache_request_headers();

        if(empty($headers['Authorization'])){
            throw new \Exception('Don`t have a token', 422);
        }

        $requvestToken = str_replace('Bearer ','', $headers['Authorization']);

        $tokenData = Token::getPayload($requvestToken);

        if(empty($tokenData['user_id'])){
            throw new \Exception('Token don`t have a user_id', 422);
        }

        $user = User::find($tokenData['user_id']);

        if(!Token::validate($requvestToken, $user->password)){
            throw new \Exception('Token is invalid', 422);
        }


        return true;
    }
}