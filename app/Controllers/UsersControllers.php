<?php


namespace App\Controllers;

use App\Models\User;
use App\Validators\Auth\AuthValidator;
use App\Validators\Auth\RegisterValidators;
use Core\Controller;

class UsersControllers extends Controller
{
    public function store():array
    {
        $data = requestBody();
        $validate = new RegisterValidators();

        if($validate->validate($data)){
           $id = User::create([
               ...$data,
               'password' => password_hash($data['password'], PASSWORD_BCRYPT)
           ]);

           return $this->response(200, User::find($id)->toArray());
        }

        return $this->response(200, [], $validate->getErr());
    }

    public function singin(): array
    {
        $data = requestBody();
        $validate = new AuthValidator();

        if($validate->validate($data)) {
            $user = User::findBy('email', $data['email']);
            if(password_verify($data['password'], $user->password)) {
                $token = random_bytes(32);

                return $this->response(200, compact('token'));
            }
        }
        return $this->response(200, [], $validate->getErr());
    }
}