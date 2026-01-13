<?php
header("Content-Type: application/json");

$conn = new mysqli("127.0.0.1","root","1234","guvi_auth");
$redis = new Redis();
$redis->connect("127.0.0.1",6379);

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$password = $data['password'];

$stmt = $conn->prepare("SELECT id,password FROM users WHERE email=?");
$stmt->bind_param("s",$email);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows===0){
    echo json_encode(["status"=>"invalid"]);
    exit;
}

$user = $res->fetch_assoc();
if(!password_verify($password,$user['password'])){
    echo json_encode(["status"=>"invalid"]);
    exit;
}

$token = bin2hex(random_bytes(32));

$payload = [
    "user_id" => (int)$user['id'],
    "email" => $email,
    "login_time" => time()
];

$redis->setex(
    "session:$token",
    3600,
    json_encode($payload)
);

echo json_encode([
    "status"=>"success",
    "token"=>$token
]);