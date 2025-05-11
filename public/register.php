<?php
header('Content-Type: application/json; charset=utf-8'); // Встановлення правильного заголовка
mb_internal_encoding("UTF-8");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "unityaccess";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Помилка підключення: " . $conn->connect_error], JSON_UNESCAPED_UNICODE));
}

// Отримання JSON-даних із Unity
$data = json_decode(file_get_contents("php://input"));

if (!isset($data) || !isset($data->username) || !isset($data->password) || empty($data->username) || empty($data->password)) {
    echo json_encode(["success" => false, "message" => "Некоректні дані"], JSON_UNESCAPED_UNICODE);
    exit();
}

$username = $conn->real_escape_string($data->username);

// Генерація salt (можеш змінити довжину)
$salt = bin2hex(random_bytes(16));

// Хешування пароля разом із salt
$passwordHash = password_hash($data->password . $salt, PASSWORD_BCRYPT);

// Перевірка, чи існує користувач
$sql = "SELECT id FROM players WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Користувач уже існує"], JSON_UNESCAPED_UNICODE);
} else {
    // Додаємо нового користувача (включаючи salt)
    $sql = "INSERT INTO players (username, hash, salt, level) VALUES ('$username', '$passwordHash', '$salt', 1)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Реєстрація успішна"], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["success" => false, "message" => "Помилка: " . $conn->error], JSON_UNESCAPED_UNICODE);
    }
}

$conn->close();
?>
