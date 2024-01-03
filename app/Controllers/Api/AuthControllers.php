<?php


namespace App\Controllers\Api;

use App\Models\User;
use App\Validators\Auth\AuthValidator;
use App\Validators\Auth\RegisterValidators;
use Core\Controller;
use ReallySimpleJWT\Token;

class AuthControllers extends Controller
{
    public function singup():array
    {
        $data = requestBody();
        $validate = new RegisterValidators();

        if($validate->validate($data)){
           $user = User::create([
               ...$data,
               'password' => password_hash($data['password'], PASSWORD_BCRYPT)
           ]);

           return $this->response(200, $user->toArray());
        }

        return $this->response(200, [], $validate->getErr());
    }

    public function singin(): array
    {
        $data = requestBody();
        $validator = new AuthValidator();

        if($validator->validate($data)) {
            $user = User::findBy('email', $data['email']);
            if(password_verify($data['password'], $user->password)) {
                $expiration = time() + 3600;
                $token = Token::create($user->id, $user->password, $expiration, 'localhost');

                return $this->response(200, compact('token'));
            }
        }
        return $this->response(200, [], $validator->getErr());
    }
}