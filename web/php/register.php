<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单提交的数据
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];  // 获取年龄字段

    // 检查密码和确认密码是否一致
    if ($password !== $confirm_password) {
        echo "密码和确认密码不一致，请重新输入。";
        exit;
    }

    // 创建数据库连接
    $conn = new mysqli('localhost', 'root', '123456', 'data');
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 检查用户名是否已存在
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "用户名已存在，请选择其他用户名。";
        exit;
    }
    $stmt->close();

    // 检查邮箱是否已存在
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "该邮箱已被注册，请选择其他邮箱。";
        exit;
    }
    $stmt->close();

    // 生成以 1 开头的随机 ID（长度为 10 位）
    $user_id = "1" . rand(100000000, 999999999);  // 生成 10 位的 ID，确保以 1 开头

    // 插入新用户数据，包括随机生成的用户ID
    $stmt = $conn->prepare("INSERT INTO user (user_id, username, email, password, gender, age) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        echo "准备语句失败: " . $conn->error;
        exit;
    }

    // 绑定参数并执行
    $stmt->bind_param("sssssi", $user_id, $username, $email, $password, $gender, $age);
	if ($stmt->execute()) {
		// 注册成功后跳转到登录页面
		header("Location: ../denglv.html"); 
		exit; // 确保脚本停止执行后再跳转
	} else {
		echo "注册失败: " . $stmt->error;
	}

    // 关闭连接
    $stmt->close();
    $conn->close();
}
?>
