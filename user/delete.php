<?php

header("Access-Control-Allow-Origin: http://MySite.ru/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config/core.php";
include_once "../libs/JWT/BeforeValidException.php";
include_once "../libs/JWT/ExpiredException.php";
include_once "../libs/JWT/SignatureInvalidException.php";
include_once "../libs/JWT/JWT.php";
include_once "../libs/JWT/Key.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

include_once "../config/Database.php";
include_once "../objects/User.php";

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

$jwt = isset($data->jwt) ? $data->jwt : "";

if($jwt) {
    try {

        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        $user->id = $decoded->$data->id;

        if ($user->delete()) {

            http_response_code(200);
        
            echo json_encode(array("message" => "Пользователь был удалён"), JSON_UNESCAPED_UNICODE);
        }
        else {
        
            http_response_code(409);
        
            echo json_encode(array("message" => "Не удалось удалить пользователя!", JSON_UNESCAPED_UNICODE));
        }
    }
    catch (Exception $e) {

        http_response_code(403);

        echo json_encode(array(
            "message" => "Доступ закрыт",
            "error" => $e->getMessage()
        ));
    }
}

else {

http_response_code(403);

echo json_encode(array("message" => "Доступ закрыт"));
}