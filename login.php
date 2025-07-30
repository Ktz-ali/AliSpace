<?php
session_start();

// 检查是否已登录
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: admin.php');
    exit;
}

$error = '';

// 处理登录请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // 这里应替换为您的验证逻辑
    $validUsername = 'admin';
    $validPassword = 'admin'; // 在实际应用中应使用哈希密码
    
    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        
        // 记录登录活动
        logLoginActivity($username);
        
        header('Location: admin.php');
        exit;
    } else {
        $error = '用户名或密码错误';
    }
}

// 记录登录活动
function logLoginActivity($username) {
    $logEntry = "login|{$username} 登录系统|" . date('Y-m-d H:i:s');
    file_put_contents('data/activity.log', $logEntry . PHP_EOL, FILE_APPEND);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台登录</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #4776E6 0%, #8E54E9 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        .login-body {
            padding: 25px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
        }
        .btn-login {
            background: linear-gradient(135deg, #4776E6 0%, #8E54E9 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            color: white;
            font-weight: bold;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(142, 84, 233, 0.4);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h3><i class="fas fa-lock me-2"></i>后台管理系统</h3>
            <p class="mb-0">请登录您的账户</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">用户名</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="请输入用户名" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">密码</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="请输入密码" required>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-login">登录</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>