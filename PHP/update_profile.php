<?php
require __DIR__ . "/../../vendor/autoload.php";
header("Content-Type: application/json");

// ---------- REDIS ----------
$redis = new Redis();
$redis->connect("127.0.0.1", 6379);

$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    exit;
}

$token = str_replace("Bearer ", "", $headers['Authorization']);
$session = $redis->get("session:$token");

if (!$session) {
    http_response_code(401);
    exit;
}

$sessionData = json_decode($session, true);
$userId = (int)$sessionData['user_id'];
// ---------- INPUT ----------
$data = json_decode(file_get_contents("php://input"), true);
$dob = $data['dob'];
$age = (int)$data['age'];
$contact = $data['contact'];

// ---------- MONGODB ----------
$client = new MongoDB\Client("mongodb://127.0.0.1:27017");
$db = $client->guvi_profiles;
$collection = $db->profiles;

$result=$collection->updateOne(
    ["user_id" => $userId],
    ['$set' => [
        "dob" => $dob,
        "age" => $age,
        "contact" => $contact
    ]
    ]
);

echo json_encode(["status"=>"success"]);
exit;