<?php
require __DIR__ . '/../../vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://127.0.0.1:27017");
    $db = $client->guvi_profiles;
    $collection = $db->profiles;
} catch (Exception $e) {
    echo json_encode(["status" => "mongo_error", "message" => $e->getMessage()]);
    exit;
}
