<?php

use App\Models\User;

define('BASE_DIR', dirname(__DIR__));

require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/vendor/autoload.php";
require_once BASE_DIR . "/core/Router.php";

try{
    $dotenv = \Dotenv\Dotenv::createUnsafeImmutable(BASE_DIR);
    $dotenv->load();

//   dd(User::create([
//       'email' => 'kristina0mmpdotaplayer@gmail.com',
//       'password' => '1234'
//   ]));
//    foreach($users as $user) {
//        d($user->getUserInfo());
//    }
//    dd();


    die(\Core\Router::dispatch($_SERVER['REQUEST_URI']));

} catch (Exception $exception) {
    error_response($exception);
}