<?php


use Core\Config;
use ReallySimpleJWT\Token;

/**
 * @return array
 */
function requestBody(): array | null
{
    $data = [];
    $requestBody = file_get_contents("php://input");

    if (!empty($requestBody)) {
        $data = json_decode($requestBody, true);
    }

    return $data;
}

/**
 * @param Exception $exception
 * @return void
 */
function error_response(Exception $exception):void
{
    die(json_response(422, [
        'data' => [
            'message' => $exception->getMessage()
        ],
        'err' => $exception->getTrace()
    ]));
}

function json_response($code = 200, array $data = []): string
{
    // clear the old headers
    header_remove();
    // set the actual code
    http_response_code($code);
    // set the header to make sure cache is forced
    header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
    // treat this as json
    header('Content-Type: application/json');

    $status = array(
        200 => '200 OK',
        400 => '400 Bad Request',
        422 => 'Unprocessable Entity',
        500 => '500 Internal Server Error',
        403 => 'Forbidden'
    );

    // ok, validation error, or failure
    header('Status: ' . $status[$code]);

    // return the encoded json
    return json_encode(array(
        'code' => $code,
        'status' => $status[$code], // success or not?
        ...$data
    ));
}

function getToken():string
{
    $headers = apache_request_headers();

    if(empty($headers['Authorization'])){
        throw new \Exception('Don`t have a token', 422);
    }

    return str_replace('Bearer ','', $headers['Authorization']);
}

function authId():int
{

    $tokenData = Token::getPayload(getToken());

    if(empty($tokenData['user_id'])){
        throw new \Exception('Token don`t have a user_id', 422);
    }

    return $tokenData['user_id'];
}

function config(string $name): string | null
{
    return Config::get($name);
}

function db(): PDO
{
    return \Core\Db::connect();
}
