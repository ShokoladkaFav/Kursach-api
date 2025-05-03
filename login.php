<?php
header('Content-Type: application/json; charset=utf-8');

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "unityaccess";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Помилка підключення: " . $conn->connect_error]));
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->username) || !isset($data->password)) {
    echo json_encode(["success" => false, "message" => "Некоректні дані"]);
    exit();
}

$username = $conn->real_escape_string($data->username);
$password = $data->password;

// Отримуємо дані користувача, включаючи race_id і skill_id
$sql = "SELECT id, username, hash, salt, level, race_id, skill_id FROM players WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Перевіряємо пароль
    if (password_verify($password . $row['salt'], $row['hash'])) {
        $hasRaceSkill = !is_null($row['race_id']) && !is_null($row['skill_id']);

        echo json_encode([
            "success" => true,
            "message" => "Вхід успішний!",
            "user_id" => $row['id'],
            "username" => $row['username'],
            "level" => $row['level'],
            "hasRaceSkill" => $hasRaceSkill // Перевірка раси та скіла
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Невірний пароль"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Користувача не знайдено"]);
}

$conn->close();
?>
