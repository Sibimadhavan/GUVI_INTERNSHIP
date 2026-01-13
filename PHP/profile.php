<?php
header("Content-Type: application/json");

/* ================= REDIS AUTH ================= */
$headers = getallheaders();
$token = str_replace("Bearer ", "", $headers['Authorization'] ?? '');

$redis = new Redis();
$redis->connect("127.0.0.1", 6379);

$session = $redis->get("session:$token");

if (!$session) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$sessionData = json_decode($session, true);
$userId = $sessionData['user_id'];

/* ================= MYSQL (USERNAME + EMAIL) ================= */
$conn = new mysqli("127.0.0.1", "root", "1234", "guvi_auth");

$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "User not found"]);
    exit;
}

$user = $result->fetch_assoc();

/* ================= MONGODB (PROFILE) ================= */
require __DIR__ . "/../../vendor/autoload.php";

$mongo = new MongoDB\Client("mongodb://127.0.0.1:27017");
$collection = $mongo->guvi_profiles->profiles;

$profile = $collection->findOne(["user_id" => $userId]);

/* ================= RESPONSE ================= */
echo json_encode([
    "username" => $user['username'],
    "email"    => $user['email'],
    "age"      => $profile['age'] ?? "",
    "dob"      => $profile['dob'] ?? "",
    "contact"  => $profile['contact'] ?? ""
]);
