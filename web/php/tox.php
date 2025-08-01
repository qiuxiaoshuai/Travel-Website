<?php
session_start();

// 开启错误显示，方便调试
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 确保用户已登录
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => '用户未登录']);
    exit();
}

// 假设已经连接到数据库并获取用户数据
// 假设 $user 是你从数据库查询的用户数据数组
// 查询用户的头像（LONG BLOB）
$user_id = $_SESSION['user_id'];  // 用户的 ID，假设存储在 session 中
$query = "SELECT profile_picture FROM users WHERE user_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 默认头像路径
$profile_picture = 'uploads/default.png';  // 默认头像路径

// 如果数据库中有头像数据，转换为 Base64
if (isset($user['profile_picture']) && !empty($user['profile_picture'])) {
    $profile_picture = 'data:image/jpeg;base64,' . base64_encode($user['profile_picture']);
}

// 获取用户名
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// 返回 JSON 数据
echo json_encode([
    'profile_picture' => $profile_picture,
    'username' => $username
]);
?>
