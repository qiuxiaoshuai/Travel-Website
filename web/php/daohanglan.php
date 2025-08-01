<?php
session_start();  // 开启 session

header('Content-Type: application/json');  // 设置响应头为 JSON 格式

// 禁用显示错误信息
error_reporting(0);

// 检查用户是否已登录
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => '未登录']);  // 如果没有登录，返回错误信息
    exit();
}

$user_id = $_SESSION['user_id'];  // 从 session 获取用户 ID
$username = $_SESSION['username'];  // 从 session 获取用户名

// 创建数据库连接
$conn = new mysqli('localhost', 'root', '123456', 'data');
if ($conn->connect_error) {
    echo json_encode(['error' => '数据库连接失败']);  // 如果连接失败，返回错误信息
    exit();
}

// 查询用户信息
$stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo json_encode(['error' => '用户信息获取失败']);  // 如果查询失败，返回错误信息
    exit();
}

$stmt->close();
$conn->close();

// 获取头像路径
if ($user['profile_picture']) {
    $profile_picture = 'data:image/jpeg;base64,' . base64_encode($user['profile_picture']);
} else {
    $profile_picture = 'uploads/default.png';  // 默认头像
}

// 返回 JSON 格式的用户数据
echo json_encode([
    'username' => $user['username'],
    'profile_picture' => $profile_picture,
    'user_id' => $user['user_id'],
    'email' => $user['email'],
    'gender' => $user['gender'],
    'age' => $user['age']
]);
?>
