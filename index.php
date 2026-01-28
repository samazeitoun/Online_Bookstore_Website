<?php
// index.php
session_start();

// Handle adding items to cart
if(isset($_POST['add_to_cart'])){
    $conn = new mysqli("localhost", "root", "", "bookstore");
    
    $bookID = $_POST['book_id'];
    $quantity = $_POST['quantity'];
    
    $sql = "SELECT Price FROM book WHERE BookID = '$bookID'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $price = $row['Price'];
    $totalPrice = $price * $quantity;
    
    // Check if item already in cart
    $checkSql = "SELECT * FROM cart WHERE BookID = '$bookID'";
    $checkResult = $conn->query($checkSql);
    
    if($checkResult->num_rows > 0) {
        $updateSql = "UPDATE cart SET Quantity = Quantity + $quantity WHERE BookID = '$bookID'";
        $conn->query($updateSql);
    } else {
        $insertSql = "INSERT INTO cart(BookID, Quantity, Price) VALUES('$bookID', $quantity, $price)";
        $conn->query($insertSql);
    }
    $conn->close();
}

// Handle emptying cart
if(isset($_GET['empty_cart'])){
    $conn = new mysqli("localhost", "root", "", "bookstore");
    $conn->query("DELETE FROM cart");
    $conn->close();
    header("Location: index.php");
}

// Database connection
$conn = new mysqli("localhost", "root", "", "bookstore");

// Search functionality
$search = "";
if(isset($_GET['search'])){
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM book WHERE BookTitle LIKE '%$search%' OR Author LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM book";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>BookStore</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
        }
        
        /* Header */
        .header {
            background: #003366;
            padding: 15px 0;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            color: white;
            font-size: 24px;
            text-decoration: none;
            font-weight: bold;
        }
        
        .user-menu {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            color: white;
            background: #00509e;
        }
        
        .btn:hover {
            background: #003366;
        }
        
        /* Search */
        .search-box {
            background: #002244;
            padding: 15px 0;
        }
        
        .search-form {
            text-align: center;
        }
        
        .search-input {
            padding: 10px;
            width: 300px;
            border: none;
            border-radius: 4px;
        }
        
        /* Main Layout */
        .main-layout {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        /* Books */
        .books-section {
            flex: 3;
        }
        
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .book-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .book-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .book-info {
            margin-top: 10px;
        }
        
        .book-title {
            font-weight: bold;
            color: #003366;
            margin: 5px 0;
        }
        
        .book-price {
            color: #e63946;
            font-weight: bold;
            font-size: 18px;
            margin: 10px 0;
        }
        
        .quantity-input {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .add-btn {
            background: #003366;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        
        .add-btn:hover {
            background: #00509e;
        }
        
        /* Cart */
        .cart-section {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-height: 500px;
            overflow-y: auto;
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .cart-title {
            color: #003366;
            font-size: 20px;
        }
        
        .empty-btn {
            background: #e63946;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #003366;
            text-align: right;
            font-weight: bold;
            font-size: 18px;
        }
        
        .checkout-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        
        .empty-cart {
            text-align: center;
            color: #666;
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .main-layout {
                flex-direction: column;
            }
            
            .search-input {
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">📚 BookStore</a>
                <div class="user-menu">
                    <?php if(isset($_SESSION['id'])): ?>
                        <a href="edituser.php" class="btn">Profile</a>
                        <a href="logout.php" class="btn">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn">Login</a>
                        <a href="register.php" class="btn">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search -->
    <div class="search-box">
        <div class="container">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="search-input" 
                       placeholder="Search books..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn">Search</button>
                <?php if($search): ?>
                    <a href="index.php" class="btn">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container">
        <div class="main-layout">
            <!-- Books Section -->
            <div class="books-section">
                <h2 style="color: #003366; margin-bottom: 20px;">Books</h2>
                
                <div class="books-grid">
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <div class="book-card">
                                <img src="<?php echo $row['Image']; ?>" alt="<?php echo $row['BookTitle']; ?>">
                                <div class="book-info">
                                    <div class="book-title"><?php echo $row['BookTitle']; ?></div>
                                    <div>Author: <?php echo $row['Author']; ?></div>
                                    <div>Type: <?php echo $row['Type']; ?></div>
                                    <div class="book-price">EGP <?php echo $row['Price']; ?></div>
                                    
                                    <form method="POST" style="margin-top: 10px;">
                                        <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                                        <input type="hidden" name="book_id" value="<?php echo $row['BookID']; ?>">
                                        <input type="hidden" name="add_to_cart" value="1">
                                        <button type="submit" class="add-btn">Add to Cart</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                            <h3>No books found</h3>
                            <p><?php echo $search ? 'Try a different search.' : 'No books available.'; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Cart Section -->
            <div class="cart-section">
                <div class="cart-header">
                    <div class="cart-title">🛒 Shopping Cart</div>
                    <a href="?empty_cart=1" class="empty-btn" 
                       onclick="return confirm('Empty cart?')">Empty</a>
                </div>
                
                <?php
                // Get cart items
                $cartSql = "SELECT book.*, cart.Quantity 
                           FROM book 
                           JOIN cart ON book.BookID = cart.BookID";
                $cartResult = $conn->query($cartSql);
                $total = 0;
                ?>
                
                <?php if($cartResult->num_rows > 0): ?>
                    <?php while($cartRow = $cartResult->fetch_assoc()): ?>
                        <?php 
                        $itemTotal = $cartRow['Price'] * $cartRow['Quantity'];
                        $total += $itemTotal;
                        ?>
                        <div class="cart-item">
                            <div style="font-weight: bold;"><?php echo $cartRow['BookTitle']; ?></div>
                            <div style="font-size: 14px; color: #666;">
                                EGP <?php echo $cartRow['Price']; ?> × <?php echo $cartRow['Quantity']; ?> 
                                = <strong>EGP <?php echo number_format($itemTotal, 2); ?></strong>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    
                    <div class="cart-total">
                        Total: EGP <?php echo number_format($total, 2); ?>
                    </div>
                    
                    <?php if(isset($_SESSION['id'])): ?>
                        <a href="checkout.php">
                            <button class="checkout-btn">Checkout →</button>
                        </a>
                    <?php else: ?>
                        <div style="text-align: center; margin-top: 10px;">
                            <a href="login.php" style="color: #003366;">Login to checkout</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-cart">
                        <div style="font-size: 48px;">🛒</div>
                        <p>Your cart is empty</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php $conn->close(); ?>
</body>
</html>