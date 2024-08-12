<?

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

if (!empty($data->name) && !empty($data->password) && !empty($data->email) && !empty($data->login)) {
    $user->name = $data->name;
    $user->password = $data->password;
    $user->email = $data->email;
    $user->login = $data->login;
    $user->dateReg = date("Y-m-d H:i:s");

    if ($user->create()) {
        
        http_response_code(200);
        
        echo json_encode(array("message" => "Пользователь зарегистрирован!"), JSON_UNESCAPED_UNICODE);
    }
    else {
        http_response_code(409);

        echo json_encode(array("message" => "Пользователь, с таким логином или email, уже есть!"), JSON_UNESCAPED_UNICODE);
    }
}
else {
http_response_code(400);

echo json_encode(array("message" => "Невозможно создать пользователя. Данные неполные!"), JSON_UNESCAPED_UNICODE);
}