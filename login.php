<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BookStore</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #003366;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group input:focus {
            border-color: #003366;
            outline: none;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn-login {
            flex: 2;
            background: #003366;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        
        .btn-cancel {
            flex: 1;
            background: #666;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-login:hover {
            background: #00509e;
        }
        
        .btn-cancel:hover {
            background: #555;
        }
        
        .error-message {
            color: #e63946;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .register-link a {
            color: #003366;
            text-decoration: none;
            font-weight: bold;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Login</h1>
            <p>Enter your credentials to access your account</p>
        </div>
        
        <form action="checklogin.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="pwd">Password:</label>
                <input type="password" id="pwd" name="pwd" required>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-login">Login</button>
                <button type="button" class="btn-cancel" onclick="window.location.href='index.php'">Cancel</button>
            </div>
        </form>
        
        <?php if(isset($_GET['errcode'])): ?>
            <?php
            $errorMessages = [
                1 => 'Invalid username or password. Please try again.',
                2 => 'Please login first.'
            ];
            
            $errcode = intval($_GET['errcode']);
            if(array_key_exists($errcode, $errorMessages)):
            ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($errorMessages[$errcode]); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>