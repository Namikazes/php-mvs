<?php

namespace App\Controllers\Api;

use Core\Controller;
use App\Models\User;
use ReallySimpleJWT\Token;

class BaseApiController extends Controller
{
    public function before(string $action, array $params = []): bool
    {
        $userId = authId();
        $requvestToken = getToken();

        $user = User::find($userId);

        if(!Token::validate($requvestToken, $user->password)){
            throw new \Exception('Token is invalid', 422);
        }


        return true;
    }
}