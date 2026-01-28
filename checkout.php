<?php
session_start();

// Initialize variables
$nameErr = $emailErr = $genderErr = $addressErr = $icErr = $contactErr = "";
$name = $email = $gender = $address = $ic = $contact = "";
$cID = 0;

// Handle form submission for non-logged in users
if(isset($_POST['submitButton'])){
    // Validate form data
    $name = $_POST['name'] ?? '';
    $ic = $_POST['ic'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    
    $errors = false;
    
    // Validate name
    if (empty($name)) {
        $nameErr = "Please enter your name";
        $errors = true;
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $name)){
        $nameErr = "Only letters and white space allowed";
        $errors = true;
    }
    
    // Validate IC
    if (empty($ic)){
        $icErr = "Please enter your IC number";
        $errors = true;
    } elseif(!preg_match("/^[0-9 -]*$/", $ic)){
        $icErr = "Please enter a valid IC number";
        $errors = true;
    }
    
    // Validate email
    if (empty($email)){
        $emailErr = "Please enter your email address";
        $errors = true;
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $emailErr = "Invalid email format";
        $errors = true;
    }
    
    // Validate contact
    if (empty($contact)){
        $contactErr = "Please enter your phone number";
        $errors = true;
    } elseif(!preg_match("/^[0-9 -]*$/", $contact)){
        $contactErr = "Please enter a valid phone number";
        $errors = true;
    }
    
    // Validate gender
    if (empty($gender)){
        $genderErr = "Gender is required!";
        $errors = true;
    }
    
    // Validate address
    if (empty($address)){
        $addressErr = "Please enter your address";
        $errors = true;
    }
    
    // If no errors, process the order
    if(!$errors){
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "bookstore";

        $conn = new mysqli($servername, $username, $password, $dbname); 

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Insert customer using prepared statement
        $stmt = $conn->prepare("INSERT INTO customer(CustomerName, CustomerPhone, CustomerIC, CustomerEmail, CustomerAddress, CustomerGender) 
                                VALUES(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $contact, $ic, $email, $address, $gender);
        $stmt->execute();
        $cID = $stmt->insert_id;
        $stmt->close();
        
        // Update cart with customer ID
        $stmt = $conn->prepare("UPDATE cart SET CustomerID = ?");
        $stmt->bind_param("i", $cID);
        $stmt->execute();
        $stmt->close();
        
        // Move cart items to orders
        $sql = "SELECT * FROM cart WHERE CustomerID = $cID";
        $result = $conn->query($sql);
        
        while($row = $result->fetch_assoc()){
            $stmt = $conn->prepare("INSERT INTO `order`(CustomerID, BookID, DatePurchase, Quantity, TotalPrice, Status) 
                                   VALUES(?, ?, CURRENT_TIME, ?, ?, 'N')");
            $stmt->bind_param("isid", $row['CustomerID'], $row['BookID'], $row['Quantity'], $row['TotalPrice']);
            $stmt->execute();
            $stmt->close();
        }
        
        // Clear cart
        $conn->query("DELETE FROM cart WHERE CustomerID = $cID");
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Checkout - BookStore</title>
    <style> 
        body {
            font-family: Arial; 
            margin: 0 auto; 
            background-color: #f2f2f2;
        }
        
        header {
            background-color: rgb(0,51,102);
            width: 100%;
        }
        
        header img {
            margin: 1%;
        }
        
        header .hi {
            background-color: #fff;
            border: none;
            border-radius: 20px;
            text-align: center;
            transition-duration: 0.5s; 
            padding: 8px 30px;
            cursor: pointer;
            color: #000;
            margin-top: 15%;
            float: right;
            margin: 2%;
        }
        
        header .hi:hover {
            background-color: #ccc;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .checkout-form {
            margin-top: 20px;
            width: 100%;
            color: #000;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        input[type=text], textarea, select {
            width: 100%;
            padding: 10px;
            border-radius: 3px;
            box-sizing: border-box;
            border: 2px solid #ccc;
            transition: 0.5s;
            outline: none;
        }
        
        input[type=text]:focus, textarea:focus, select:focus {
            border: 2px solid rgb(0,51,102);
        }
        
        textarea {
            outline: none;
            border: 2px solid #ccc;
            min-height: 100px;
        }
        
        textarea:focus {
            border: 2px solid rgb(0,51,102);
        }
        
        .button {
            background-color: rgb(0,51,102);
            border: none;
            border-radius: 20px;
            text-align: center;
            transition-duration: 0.5s; 
            padding: 10px 30px;
            cursor: pointer;
            color: #fff;
            font-size: 16px;
            margin: 10px 5px;
        }
        
        .button:hover {
            background-color: rgb(102,255,255);
            color: #000;
        }
        
        .button-group {
            text-align: center;
            margin-top: 30px;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        tr {
            background-color: #fff;
        }
        
        th {
            background-color: rgb(0,51,102);
            color: white;
        }
        
        .error {
            color: red;
            font-size: 0.8em;
            display: block;
            margin-top: 5px;
        }
        
        .gender-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .gender-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .order-summary {
            margin-top: 40px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
<header>
    <blockquote>
        <img src="image/logo.png" alt="BookStore Logo">
        <input class="hi" type="button" name="cancel" value="Home" onClick="window.location='index.php';" />
    </blockquote>
</header>

<div class="container">
    <?php
    // Process checkout for logged-in users
    if(isset($_SESSION['id'])){
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "bookstore";

        $conn = new mysqli($servername, $username, $password, $dbname); 

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get customer ID
        $stmt = $conn->prepare("SELECT CustomerID FROM customer WHERE UserID = ?");
        $stmt->bind_param("i", $_SESSION['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $cID = $row['CustomerID'];
        $stmt->close();

        // Update cart with customer ID
        $stmt = $conn->prepare("UPDATE cart SET CustomerID = ? WHERE CustomerID IS NULL");
        $stmt->bind_param("i", $cID);
        $stmt->execute();
        $stmt->close();

        // Move cart items to orders
        $sql = "SELECT * FROM cart WHERE CustomerID = $cID";
        $result = $conn->query($sql);
        
        while($row = $result->fetch_assoc()){
            $stmt = $conn->prepare("INSERT INTO `order`(CustomerID, BookID, DatePurchase, Quantity, TotalPrice, Status) 
                                   VALUES(?, ?, NOW(), ?, ?, 'N')");
            $stmt->bind_param("isid", $row['CustomerID'], $row['BookID'], $row['Quantity'], $row['TotalPrice']);
            $stmt->execute();
            $stmt->close();
        }
        
        // Clear cart
        $conn->query("DELETE FROM cart WHERE CustomerID = $cID");
        
        // Get order details
        $sql = "SELECT customer.CustomerName, customer.CustomerIC, customer.CustomerGender, 
                       customer.CustomerAddress, customer.CustomerEmail, customer.CustomerPhone, 
                       book.BookTitle, book.Price, book.Image, 
                       `order`.`DatePurchase`, `order`.`Quantity`, `order`.`TotalPrice`
                FROM customer, book, `order`
                WHERE `order`.`CustomerID` = customer.CustomerID 
                AND `order`.`BookID` = book.BookID 
                AND `order`.`Status` = 'N' 
                AND `order`.`CustomerID` = $cID";
        
        $result = $conn->query($sql);
        
        if($result->num_rows > 0) {
            echo '<div class="button-group">';
            echo '<input class="button" type="button" value="Continue Shopping" onClick="window.location=\'index.php\'" />';
            echo '</div>';
            
            echo '<h2 style="color: #003366;">Order Successful!</h2>';
            
            // Display order details
            $row = $result->fetch_assoc();
            echo '<div class="order-summary">';
            echo '<h3>Order Summary</h3>';
            echo '<p><strong>Name:</strong> ' . htmlspecialchars($row['CustomerName']) . '</p>';
            echo '<p><strong>IC Number:</strong> ' . htmlspecialchars($row['CustomerIC']) . '</p>';
            echo '<p><strong>Email:</strong> ' . htmlspecialchars($row['CustomerEmail']) . '</p>';
            echo '<p><strong>Mobile:</strong> ' . htmlspecialchars($row['CustomerPhone']) . '</p>';
            echo '<p><strong>Gender:</strong> ' . htmlspecialchars($row['CustomerGender']) . '</p>';
            echo '<p><strong>Address:</strong> ' . htmlspecialchars($row['CustomerAddress']) . '</p>';
            echo '<p><strong>Order Date:</strong> ' . $row['DatePurchase'] . '</p>';
            echo '</div>';
            
            // Display ordered items
            echo '<h3>Ordered Items</h3>';
            echo '<table>';
            echo '<tr><th>Item</th><th>Details</th><th>Total</th></tr>';
            
            $total = 0;
            mysqli_data_seek($result, 0); // Reset result pointer
            while($row = $result->fetch_assoc()){
                $itemTotal = $row['Price'] * $row['Quantity'];
                $total += $itemTotal;
                
                echo '<tr>';
                echo '<td><img src="' . htmlspecialchars($row['Image']) . '" width="80" alt="' . htmlspecialchars($row['BookTitle']) . '"></td>';
                echo '<td>';
                echo '<strong>' . htmlspecialchars($row['BookTitle']) . '</strong><br>';
                echo 'Price: EGP ' . number_format($row['Price'], 2) . '<br>';
                echo 'Quantity: ' . $row['Quantity'];
                echo '</td>';
                echo '<td>EGP ' . number_format($itemTotal, 2) . '</td>';
                echo '</tr>';
            }
            
            echo '<tr style="background-color: #f0f0f0;">';
            echo '<td colspan="2" style="text-align: right;"><strong>Total Price:</strong></td>';
            echo '<td><strong>EGP ' . number_format($total, 2) . '</strong></td>';
            echo '</tr>';
            echo '</table>';
            
            // Update order status
            $conn->query("UPDATE `order` SET Status = 'Y' WHERE CustomerID = $cID AND Status = 'N'");
        }
        
        $conn->close();
        
    } else {
        // Show checkout form for non-logged in users
        if(isset($_POST['submitButton']) && empty($nameErr) && empty($icErr) && empty($emailErr) && empty($contactErr) && empty($genderErr) && empty($addressErr)) {
            // Display order confirmation after successful submission
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "bookstore";

            $conn = new mysqli($servername, $username, $password, $dbname);
            
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            // Get order details
            $sql = "SELECT customer.CustomerName, customer.CustomerIC, customer.CustomerGender, 
                           customer.CustomerAddress, customer.CustomerEmail, customer.CustomerPhone, 
                           book.BookTitle, book.Price, book.Image, 
                           `order`.`DatePurchase`, `order`.`Quantity`, `order`.`TotalPrice`
                    FROM customer, book, `order`
                    WHERE `order`.`CustomerID` = customer.CustomerID 
                    AND `order`.`BookID` = book.BookID 
                    AND `order`.`Status` = 'N' 
                    AND `order`.`CustomerID` = $cID";
            
            $result = $conn->query($sql);
            
            echo '<div class="button-group">';
            echo '<input class="button" type="button" value="Continue Shopping" onClick="window.location=\'index.php\'" />';
            echo '</div>';
            
            echo '<h2 style="color: #003366;">Order Successful!</h2>';
            
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo '<div class="order-summary">';
                echo '<h3>Order Summary</h3>';
                echo '<p><strong>Name:</strong> ' . htmlspecialchars($row['CustomerName']) . '</p>';
                echo '<p><strong>IC Number:</strong> ' . htmlspecialchars($row['CustomerIC']) . '</p>';
                echo '<p><strong>Email:</strong> ' . htmlspecialchars($row['CustomerEmail']) . '</p>';
                echo '<p><strong>Mobile:</strong> ' . htmlspecialchars($row['CustomerPhone']) . '</p>';
                echo '<p><strong>Gender:</strong> ' . htmlspecialchars($row['CustomerGender']) . '</p>';
                echo '<p><strong>Address:</strong> ' . htmlspecialchars($row['CustomerAddress']) . '</p>';
                echo '<p><strong>Order Date:</strong> ' . $row['DatePurchase'] . '</p>';
                echo '</div>';
                
                // Display ordered items
                echo '<h3>Ordered Items</h3>';
                echo '<table>';
                echo '<tr><th>Item</th><th>Details</th><th>Total</th></tr>';
                
                $total = 0;
                mysqli_data_seek($result, 0);
                while($row = $result->fetch_assoc()){
                    $itemTotal = $row['Price'] * $row['Quantity'];
                    $total += $itemTotal;
                    
                    echo '<tr>';
                    echo '<td><img src="' . htmlspecialchars($row['Image']) . '" width="80" alt="' . htmlspecialchars($row['BookTitle']) . '"></td>';
                    echo '<td>';
                    echo '<strong>' . htmlspecialchars($row['BookTitle']) . '</strong><br>';
                    echo 'Price: EGP ' . number_format($row['Price'], 2) . '<br>';
                    echo 'Quantity: ' . $row['Quantity'];
                    echo '</td>';
                    echo '<td>EGP ' . number_format($itemTotal, 2) . '</td>';
                    echo '</tr>';
                }
                
                echo '<tr style="background-color: #f0f0f0;">';
                echo '<td colspan="2" style="text-align: right;"><strong>Total Price:</strong></td>';
                echo '<td><strong>EGP ' . number_format($total, 2) . '</strong></td>';
                echo '</tr>';
                echo '</table>';
                
                // Update order status
                $conn->query("UPDATE `order` SET Status = 'Y' WHERE CustomerID = $cID AND Status = 'N'");
            }
            
            $conn->close();
            
        } else {
            // Show the checkout form
            ?>
            <h2 style="color: #003366;">Checkout</h2>
            <p>Please fill in your details to complete your order.</p>
            
            <form method="post" action="" class="checkout-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Full Name">
                        <span class="error"><?php echo $nameErr; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="ic">IC Number:</label>
                        <input type="text" id="ic" name="ic" value="<?php echo htmlspecialchars($ic); ?>" placeholder="xxxxxx-xx-xxxx">
                        <span class="error"><?php echo $icErr; ?></span>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="example@email.com">
                        <span class="error"><?php echo $emailErr; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact">Mobile Number:</label>
                        <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>" placeholder="012-3456789">
                        <span class="error"><?php echo $contactErr; ?></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Gender:</label>
                    <div class="gender-group">
                        <div class="gender-option">
                            <input type="radio" id="male" name="gender" value="Male" <?php echo ($gender == "Male") ? "checked" : ""; ?>>
                            <label for="male">Male</label>
                        </div>
                        <div class="gender-option">
                            <input type="radio" id="female" name="gender" value="Female" <?php echo ($gender == "Female") ? "checked" : ""; ?>>
                            <label for="female">Female</label>
                        </div>
                    </div>
                    <span class="error"><?php echo $genderErr; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" placeholder="Your complete address"><?php echo htmlspecialchars($address); ?></textarea>
                    <span class="error"><?php echo $addressErr; ?></span>
                </div>
                
                <div class="button-group">
                    <input class="button" type="button" name="cancel" value="Cancel" onClick="window.location='index.php';" />
                    <input class="button" type="submit" name="submitButton" value="CHECKOUT">
                </div>
            </form>
            <?php
        }
    }
    ?>
</div>
</body>
</html>