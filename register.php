<?php
session_start();
$errors = [];
$success = false;

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'] ?? '';
    $uname = $_POST['uname'] ?? '';
    $upassword = $_POST['upassword'] ?? '';
    $ic = $_POST['ic'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    
    // Validate name
    if(empty($name)) {
        $errors['name'] = "Please enter your name";
    } elseif(!preg_match("/^[a-zA-Z ]*$/", $name)) {
        $errors['name'] = "Only letters and spaces allowed";
    }
    
    // Validate username
    if(empty($uname)) {
        $errors['uname'] = "Please enter a username";
    }
    
    // Validate password
    if(empty($upassword)) {
        $errors['upassword'] = "Please enter a password";
    } elseif(strlen($upassword) < 6) {
        $errors['upassword'] = "Password must be at least 6 characters";
    }
    
    // Validate IC
    if(empty($ic)) {
        $errors['ic'] = "Please enter your IC number";
    }
    
    // Validate email
    if(empty($email)) {
        $errors['email'] = "Please enter your email";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }
    
    // Validate contact
    if(empty($contact)) {
        $errors['contact'] = "Please enter your phone number";
    }
    
    // Validate gender
    if(empty($gender)) {
        $errors['gender'] = "Please select your gender";
    }
    
    // Validate address
    if(empty($address)) {
        $errors['address'] = "Please enter your address";
    }
    
    // If no errors, save to database
    if(empty($errors)) {
        $conn = new mysqli("localhost", "root", "", "bookstore");
        
        // Check if username exists
        $checkSql = "SELECT * FROM Users WHERE UserName = '$uname'";
        $checkResult = $conn->query($checkSql);
        
        if($checkResult->num_rows > 0) {
            $errors['uname'] = "Username already exists";
        } else {
            // Insert user
            $sql = "INSERT INTO Users (UserName, Password) VALUES ('$uname', '$upassword')";
            if($conn->query($sql)) {
                $user_id = $conn->insert_id;
                
                // Insert customer
                $customerSql = "INSERT INTO customer (CustomerName, CustomerPhone, CustomerIC, CustomerEmail, CustomerAddress, CustomerGender, UserID) 
                               VALUES ('$name', '$contact', '$ic', '$email', '$address', '$gender', $user_id)";
                
                if($conn->query($customerSql)) {
                    $success = true;
                    echo "<script>
                            alert('Registration successful! Please login.');
                            window.location.href = 'login.php';
                          </script>";
                }
            }
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BookStore</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        .register-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header h1 {
            color: #003366;
            margin-bottom: 10px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #003366;
            outline: none;
        }
        
        .gender-group {
            display: flex;
            gap: 20px;
            margin-top: 5px;
        }
        
        .gender-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .error {
            color: #e63946;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn-submit {
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
        
        .btn-submit:hover {
            background: #00509e;
        }
        
        .btn-cancel:hover {
            background: #555;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .login-link a {
            color: #003366;
            text-decoration: none;
            font-weight: bold;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Create Account</h1>
            <p>Join our bookstore community</p>
        </div>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" placeholder="Full Name">
                    <?php if(isset($errors['name'])): ?>
                        <span class="error"><?php echo $errors['name']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="uname">Username:</label>
                    <input type="text" id="uname" name="uname" value="<?php echo isset($_POST['uname']) ? htmlspecialchars($_POST['uname']) : ''; ?>" placeholder="User Name">
                    <?php if(isset($errors['uname'])): ?>
                        <span class="error"><?php echo $errors['uname']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="upassword">Password:</label>
                <input type="password" id="upassword" name="upassword" placeholder="Password (min. 6 characters)">
                <?php if(isset($errors['upassword'])): ?>
                    <span class="error"><?php echo $errors['upassword']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="ic">IC Number:</label>
                    <input type="text" id="ic" name="ic" value="<?php echo isset($_POST['ic']) ? htmlspecialchars($_POST['ic']) : ''; ?>" placeholder="xxxxxx-xx-xxxx">
                    <?php if(isset($errors['ic'])): ?>
                        <span class="error"><?php echo $errors['ic']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" placeholder="example@email.com">
                    <?php if(isset($errors['email'])): ?>
                        <span class="error"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="contact">Mobile Number:</label>
                    <input type="text" id="contact" name="contact" value="<?php echo isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>" placeholder="012-3456789">
                    <?php if(isset($errors['contact'])): ?>
                        <span class="error"><?php echo $errors['contact']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label>Gender:</label>
                    <div class="gender-group">
                        <div class="gender-option">
                            <input type="radio" id="male" name="gender" value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'checked' : ''; ?>>
                            <label for="male">Male</label>
                        </div>
                        <div class="gender-option">
                            <input type="radio" id="female" name="gender" value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'checked' : ''; ?>>
                            <label for="female">Female</label>
                        </div>
                    </div>
                    <?php if(isset($errors['gender'])): ?>
                        <span class="error"><?php echo $errors['gender']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" rows="3" placeholder="Your complete address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                <?php if(isset($errors['address'])): ?>
                    <span class="error"><?php echo $errors['address']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-submit">Create Account</button>
                <button type="button" class="btn-cancel" onclick="window.location.href='index.php'">Cancel</button>
            </div>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>