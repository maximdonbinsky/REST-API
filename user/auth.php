<?php

header("Access-Control-Allow-Origin: http://MySite.ru/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config/Database.php";
include_once "../objects/User.php";

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->login) && !empty($data->password)) {

    $user->login = $data->login;
    $user->password = $data->password;

    include_once "../config/core.php";
    include_once "../libs/JWT/BeforeValidException.php";
    include_once "../libs/JWT/ExpiredException.php";
    include_once "../libs/JWT/SignatureInvalidException.php";
    include_once "../libs/JWT/JWT.php";
    use \Firebase\JWT\JWT;

    if ($user->auth()) {

        $token = array(
            "iss" => $iss,
            "aud" => $aud,
            "iat" => $iat,
            "nbf" => $nbf,
            "data" => array(
                "id" => $user->id,
                "login" => $user->login,
                "name" => $user->name,
                "email" => $user->email
            )
         );

        http_response_code(200);

        $jwt = JWT::encode($token, $key, 'HS256');
        echo json_encode(array( "message" => "Пользователь авторизован", "jwt" => $jwt));
    }
    else {

        http_response_code(400);

        echo json_encode(array("message" => "Неверный логин или пароль!"), JSON_UNESCAPED_UNICODE);
    }
}
else {
http_response_code(400);

echo json_encode(array("message" => "Невозможно авторизовать пользователя. Данные неполные!"), JSON_UNESCAPED_UNICODE);
}