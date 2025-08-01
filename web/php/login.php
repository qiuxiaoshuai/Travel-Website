<?php
session_start(); // 开启 session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单提交的数据
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 创建数据库连接
    $conn = new mysqli('localhost', 'root', '123456', 'data');
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 检查用户名是否存在
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // 检查是否找到该用户
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc(); // 获取用户数据
        // 验证密码
        if ($password === $user['password']) {  // 如果密码匹配
            // 登录成功，保存用户信息到 session
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['user_id']; // 假设表中有 id 字段
            header("Location: profile.php");
            exit; // 确保脚本停止执行后再跳转
        } else {
            // 密码错误
            echo "密码错误，请重新输入。";
        }
    } else {
        // 用户名不存在
        echo "用户名不存在，请重新输入。";
    }

    // 关闭连接
    $stmt->close();
    $conn->close();
}
?>
