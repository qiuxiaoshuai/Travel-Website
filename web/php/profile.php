<?php
session_start(); // 开启 session



// 检查用户是否已登录
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // 从 session 获取用户 ID
$username = $_SESSION['username']; // 从 session 获取用户名

// 处理文件上传
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    // 获取临时上传文件的二进制数据
    $imageData = file_get_contents($_FILES["profile_picture"]["tmp_name"]);

    // 检查文件是否是图片
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check === false) {
        echo "上传的文件不是图片。";
        exit();
    }

    // 限制文件类型
    $imageFileType = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "只允许上传 JPG, JPEG, PNG, GIF 格式的图片。";
        exit();
    }

    // 创建数据库连接
    $conn = new mysqli('localhost', 'root', '123456', 'data');
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 更新数据库中的图片字段，存储二进制数据
    $stmt = $conn->prepare("UPDATE user SET profile_picture = ? WHERE user_id = ?");
    $stmt->bind_param("bi", $null, $user_id);  // 直接绑定二进制数据和 user_id
    $null = NULL; // 用于处理 LONG BLOB 类型
    $stmt->send_long_data(0, $imageData);  // 将二进制数据绑定到参数位置

    if ($stmt->execute()) {
        // echo "图片上传成功，数据库已更新。";
    } else {
        echo "数据库更新失败: " . $stmt->error;
    }

    // $stmt->close();
    // $conn->close();
}

// 获取用户信息
$conn = new mysqli('localhost', 'root', '123456', 'data');
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 查询用户信息
$stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "无法获取用户信息。";
    exit();
}

// 处理用户资料更新表单
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单数据并去除空格
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_gender = trim($_POST['gender']);
    $new_age = trim($_POST['age']);
    $new_cl = trim($_POST['cl']);
    $new_occupation = trim($_POST['occupation']);
    $new_hobbies = trim($_POST['hobbies']);
    $new_marital = trim($_POST['maritalStatus']);

    // 检查字段是否为空
    if (empty($new_username) || empty($new_email) || empty($new_gender) || empty($new_age) || empty($new_cl) || empty($new_occupation) || empty($new_hobbies) || empty($new_marital)) {
        echo "请确保所有字段都已填写。";
        exit();
    }

    // 更新用户信息到数据库
    $update_stmt = $conn->prepare("UPDATE user SET 
        username = ?, 
        email = ?, 
        gender = ?, 
        age = ?, 
        cl = ?, 
        Occupation = ?, 
        Hobbies = ?, 
        Marital = ? 
        WHERE user_id = ?");
    
    if ($update_stmt === false) {
        die("SQL 错误: " . $conn->error);
    }

    // 绑定参数
    $update_stmt->bind_param(
        "ssssssssi",  // 8 个字符串类型 + 1 个整数类型（user_id）
        $new_username, $new_email, $new_gender, $new_age, $new_cl, 
        $new_occupation, $new_hobbies, $new_marital, $user_id
    );

    if ($update_stmt->execute()) {
        echo "<script>alert('资料已更新！');</script>";
        // 更新后的数据重新获取
        $user['username'] = $new_username;
        $user['email'] = $new_email;
        $user['gender'] = $new_gender;
        $user['age'] = $new_age;
        $user['cl'] = $new_cl;
        $user['Occupation'] = $new_occupation;
        $user['Hobbies'] = $new_hobbies;
        $user['Marital'] = $new_marital;
    } else {
        echo "<script>alert('资料更新失败，请重试！');</script>";
    }

    $update_stmt->close();
}



$stmt->close();
$conn->close();

// 获取头像路径
// 如果头像是 BLOB 类型，可以从数据库获取二进制数据并在页面显示图片
if ($user['profile_picture']) {
    $profile_picture = 'data:image/jpeg;base64,' . base64_encode($user['profile_picture']);
} else {
    $profile_picture = 'img/默认头像.png'; // 默认图片路径
}

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人主页</title>
	<link rel="stylesheet" href="../css/3-daohanlan.css" />
	<link rel="stylesheet" href="../css/5-gereng.css" />
</head>
<body>
	<div class="navbar">
	    <div class="navbar-container">
	        <div class="logo">
	            <a href="#">丘小帅的旅游网站</a>
	        </div>
	        <div class="nav-links">
	            <a href="../index.html">首页</a>
	            <a href="#">攻略群</a>
	            <div class="dropdown">
	                <a href="#" class="dropbtn">去旅行</a>
	                <div class="dropdown-content">
	                    <a href="#">跟团</a>
	                    <a href="#">飞机</a>
	                    <a href="#">高铁</a>
	                </div>
	            </div>
	            <a href="#">旅游攻略</a>
	            <a href="#">定酒店</a>
	            <a href="#">关于我们</a>
	        </div>
		<div class="user-info" id="user-info">
			<a href="profile.php">
				<img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="用户头像" class="user-avatar">
				<span class="username"><?php echo htmlspecialchars($username); ?></span>
			</a>
		</div>
	    </div>
	</div>
	<div class="ger-img">
		<img src="../img/夏威夷to.jpg" alt="" />
		<div class="wave-container">
			<svg class="waves" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
				<defs>
					<!-- 定义波浪路径 -->
					<path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"/>
				</defs>
				<g class="parallax">
					<use xlink:href="#gentle-wave" x="48" y="10" fill="rgba(255,255,255,0.7)" />
					<use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
					<use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.3)" />
					<use xlink:href="#gentle-wave" x="48" y="7" fill="#fff" />
				</g>
			</svg>
		</div>
	</div>
	
    <div class="welcome-message">
        <h1>欢迎您，<?php echo htmlspecialchars($user['username']); ?>!</h1>
    </div>


	<!-- 显示头像 -->
	<div class="profile-image-container">
		<img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="头像" id="avatar" class="avatar">
		<!-- 隐藏的文件输入框 -->
		<form action="profile.php" method="POST" enctype="multipart/form-data">
			<input type="file" name="profile_picture" class="file-input" id="file-input" onchange="this.form.submit()" accept="image/*">
		</form>
	</div>
	
    <div class="main-layout-container">
        <!-- 左边的div -->
        <div class="left-side-content-box">
		    <p class="left-side-content-p"><?php echo htmlspecialchars($user['username']); ?></p>
			<div class="left-side-content-p-div">
				<p>UID: <?php echo htmlspecialchars($user['user_id']); ?></p>
				<p>性别: <?php echo htmlspecialchars($user['gender']); ?></p>
			</div>
			<div class="left-side-content-p-div">
				<p>邮箱: <?php echo htmlspecialchars($user['email']); ?></p>
				<p>年龄: <?php echo htmlspecialchars($user['age']); ?></p>
			</div >
			<div class="left-side-content-p-div">
				<p>国家: <?php echo htmlspecialchars($user['cl']); ?></p>
				<p>职业: <?php echo htmlspecialchars($user['Occupation']); ?></p>
			</div >
			<div class="left-side-content-p-div">
				<p>兴趣爱好: <?php echo htmlspecialchars($user['Hobbies']); ?></p>
				<p>感情状况: <?php echo htmlspecialchars($user['Marital']); ?></p>
			</div >
			<a href="../denglv.html" class="logout-button">注销</a>
        </div>

        <!-- 右边的div -->
        <div class="right-side-content-box">
			<div class="right-side-content-container">
			    <div class="four-column-item"><img src="../img/书写.png" alt="" />写游记</div>
			    <div class="four-column-item"><img src="../img/对话.png" alt="" />问达人</div>
			    <div class="four-column-item"><img src="../img/地图.png" alt="" />添加足迹</div>
			    <div class="four-column-item"><img src="../img/寻找校牌.png" alt="" />找伙伴</div>
			</div>
			
			<div class="right-side-content-bottom">
			    <div class="right-side-content-bottom-text">
			        <p class="right-side-content-bottom-text-p">
			            <span class="username-text"><?php echo htmlspecialchars($user['username']); ?></span>, 这里是你的家
			        </p>
			        <p class="two-line-text">是记录你的旅行记忆，结交各路豪杰的地盘儿。现在开启一场说走就走旅程！</p>
			    </div>
			
			    <!-- 三个水平排列的 div -->
			<div class="horizontal-divs">
				<div class="horizontal-item">
					<img src="../img/061_书写.png" alt="图片1" class="item-image" />
					<p class="item-text">做一个有“脸面”的人，上传头像，完善资料！</p>
					<button id="editProfileBtn" class="user-profile-edit-btn">完善资料</button>
					<div id="profileModal" class="user-profile-modal">
					    <div class="user-profile-modal-content">
					        <span class="user-profile-close-btn">&times;</span>
					        <h2 class="user-profile-modal-title">修改个人资料</h2>
					
					        <!-- 提交到当前页面 -->
					       <form method="POST" class="user-profile-form">
					           <div class="user-profile-form-group">
					               <label for="username" class="user-profile-form-label">用户名</label>
					               <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required class="user-profile-form-input">
					           </div>
					       
					           <div class="user-profile-form-group">
					               <label for="email" class="user-profile-form-label">邮箱</label>
					               <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="user-profile-form-input">
					           </div>
					       
					           <div class="user-profile-form-group">
					               <label for="gender" class="user-profile-form-label">性别</label>
					               <select id="gender" name="gender" required class="user-profile-form-select">
					                   <option value="男" <?php echo ($user['gender'] == '男') ? 'selected' : ''; ?>>男</option>
					                   <option value="女" <?php echo ($user['gender'] == '女') ? 'selected' : ''; ?>>女</option>
					                   <option value="其他" <?php echo ($user['gender'] == '其他') ? 'selected' : ''; ?>>其他</option>
					               </select>
					           </div>
					       
					           <div class="user-profile-form-group">
					               <label for="age" class="user-profile-form-label">年龄</label>
					               <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required class="user-profile-form-input">
					           </div>
					       
					           <div class="user-profile-form-group">
					               <label for="cl" class="user-profile-form-label">国家</label>
					               <input type="text" id="cl" name="cl" value="<?php echo htmlspecialchars($user['cl']); ?>" required class="user-profile-form-input">
					           </div>
					       
					           <div class="user-profile-form-group">
					               <label for="occupation" class="user-profile-form-label">职业</label>
					               <input type="text" id="occupation" name="occupation" value="<?php echo htmlspecialchars($user['Occupation']); ?>" required class="user-profile-form-input">
					           </div>
					       
					           <div class="user-profile-form-group">
					               <label for="hobbies" class="user-profile-form-label">兴趣爱好</label>
					               <input type="text" id="hobbies" name="hobbies" value="<?php echo htmlspecialchars($user['Hobbies']); ?>" required class="user-profile-form-input">
					           </div>
					       
					           <div class="user-profile-form-group">
					               <label for="maritalStatus" class="user-profile-form-label">感情状况</label>
					               <input type="text" id="maritalStatus" name="maritalStatus" value="<?php echo htmlspecialchars($user['Marital']); ?>" required class="user-profile-form-input">
					           </div>
					       
					           <button type="submit" class="user-profile-submit-btn">保存修改</button>
					       </form>
					    </div>
					</div>
				</div>
				<div class="horizontal-item">
					<img src="../img/旅行箱.png" alt="图片2" class="item-image" />
					<p class="item-text">这儿潜伏着哪些旅行大神？他们都怎么玩儿？</p>
					<button class="item-button">逛逛达人</button>
				</div>
				<div class="horizontal-item">
					<img src="../img/会员.png" alt="图片3" class="item-image" />
					<p class="item-text">快来升级你的等级吧,第一步，从打卡开始。</p>
					<button class="item-button">点击我</button>
				</div>
			</div>

			</div>

        </div>

    </div>


</body>
	<script>
		// 点击头像图片时触发文件上传
		document.getElementById('avatar').addEventListener('click', function() {
			document.getElementById('file-input').click();
		});
		
		// 获取按钮和弹出框元素
		const editProfileBtn = document.querySelector('.user-profile-edit-btn');
		const profileModal = document.querySelector('.user-profile-modal');
		const closeBtn = document.querySelector('.user-profile-close-btn');
		
		// 当点击 "完善资料" 按钮时，显示弹出表单
		editProfileBtn.addEventListener('click', () => {
		    profileModal.classList.add('show'); // 显示 modal
		});
		
		// 当点击关闭按钮时，隐藏弹出表单
		closeBtn.addEventListener('click', () => {
		    profileModal.classList.remove('show'); // 隐藏 modal
		});
		
		// 当点击模态框外部时，隐藏弹出表单
		window.addEventListener('click', (e) => {
		    if (e.target === profileModal) {
		        profileModal.classList.remove('show'); // 隐藏 modal
		    }
		});
	</script>
</html>
