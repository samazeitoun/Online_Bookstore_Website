<?php
// connectDB.php
define('SERVER','localhost');
define('DBUSER','root');
define('DBPASS','');
define('DBNAME','bookstore');

try {
    $pdo = new PDO('mysql:host='.SERVER.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} 
catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>