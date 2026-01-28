<?php
session_start();

if(isset($_POST['username']) && isset($_POST['pwd'])){
    $username = $_POST['username'];
    $pwd = $_POST['pwd'];

    include "connectDB.php";
    
    // Fix the typo: Spob should be $pdo
    $sql = "SELECT * FROM Users WHERE UserName = :username AND Password = :pwd";
   $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':username' => $username,
        ':pwd' => $pwd
    ));
    
    if($stmt->rowCount() > 0){
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $_SESSION['id'] = $row['UserID'];
            $_SESSION['username'] = $row['UserName'];
        }
        header("Location: index.php");
        exit();
    } else {
        // Debug: Check what's in the database
        error_log("Login failed for username: $username");
        header("Location: login.php?errcode=1");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>