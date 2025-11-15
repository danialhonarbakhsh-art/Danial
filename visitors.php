<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$db_file = '../data/visitors.db';
$db = new SQLite3($db_file);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($method) {
    case 'GET':
        // لیست همه کاربران
        $results = $db->query('SELECT * FROM visitors ORDER BY id DESC LIMIT 50');
        $users = [];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $users[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $users]);
        break;

    case 'POST':
        // ثبت کاربر جدید
        $name = trim($input['name'] ?? '');
        $month = (int)($input['month'] ?? 0);
        $day = (int)($input['day'] ?? 0);

        if ($name && $month >= 1 && $month <= 13 && $day >= 1 && $day <= 28) {
            $stmt = $db->prepare("INSERT INTO visitors (name, month, day) VALUES (:name, :month, :day)");
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':month', $month, SQLITE3_INTEGER);
            $stmt->bindValue(':day', $day, SQLITE3_INTEGER);
            $stmt->execute();
            echo json_encode(['success' => true, 'id' => $db->lastInsertRowID()]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'داده نامعتبر']);
        }
        break;

    case 'DELETE':
        // حذف کاربر
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM visitors WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'حذف شد']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID نامعتبر']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

$db->close();
?>
