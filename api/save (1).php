<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$db_file = '../data/visitors.db';
if (!is_dir('../data')) mkdir('../data', 0755, true);

$db = new SQLite3($db_file);
$db->exec("CREATE TABLE IF NOT EXISTS visitors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    birth_date TEXT,
    birth_time TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$name = trim($input['name'] ?? '');
$birthDate = trim($input['birthDate'] ?? '');
$birthTime = trim($input['birthTime'] ?? '');

if ($name && $birthDate && $birthTime) {
    $stmt = $db->prepare("INSERT INTO visitors (name, birth_date, birth_time) VALUES (:name, :birthDate, :birthTime)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':birthDate', $birthDate, SQLITE3_TEXT);
    $stmt->bindValue(':birthTime', $birthTime, SQLITE3_TEXT);
    $stmt->execute();
    
    $count = $db->querySingle("SELECT COUNT(*) FROM visitors");
    echo json_encode(['success' => true, 'count' => $count]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'همه فیلدها اجباریه!']);
}

$db->close();
?>
