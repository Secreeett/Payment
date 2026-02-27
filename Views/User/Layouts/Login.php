<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: url('<?php echo BASE_URL; ?>Assets/Images/1.jpg') center center / cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            padding: 50px 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .logo-emblem {
            width: 140px;
            height: 140px;
            margin: 0 auto 20px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-emblem::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border: 8px solid #FFD700;
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(0,0,0,0.1);
        }
        
        .logo-emblem img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            position: relative;
            z-index: 1;
        }
        
        .logo-emblem .emblem-text {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #333;
            text-align: center;
            line-height: 1.2;
            padding: 20px;
            z-index: 2;
        }
        
        .logo-emblem .emblem-text-top {
            position: absolute;
            top: 5px;
            font-weight: bold;
            font-size: 7px;
        }
        
        .logo-emblem .emblem-text-bottom {
            position: absolute;
            bottom: 5px;
            font-weight: bold;
            font-size: 7px;
        }
        
        .system-title {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
            outline: none;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .default-credentials {
            margin-top: 25px;
            text-align: center;
            color: #666;
            font-size: 13px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
            border: none;
        }
        
        .mb-3 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <div class="logo-emblem">
                <img src="<?php echo BASE_URL; ?>Assets/Images/logo_aip.png" alt="Logo" onerror="this.style.display='none'">
            </div>
            <h2 class="system-title">Payment System</h2>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo BASE_URL; ?>?page=login">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus placeholder="Enter your username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
            </div>
            <button type="submit" class="btn btn-login">Login</button>
        </form>
        
        <div class="default-credentials">
            <small>Default: admin/admin123 or mpdc/admin123</small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

