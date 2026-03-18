<?php
// Turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config.php';

echo "Step 1: Config loaded successfully<br>";

$email = 'admin@example.com'; // Change to YOUR admin email
$password = 'admin123'; // Change to YOUR password

echo "Step 2: Trying to login with: $email<br>";

// Check admin table
$stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
$stmt->execute([$email]);
$admin = $stmt->fetch();

echo "Step 3: Admin query executed<br>";

if ($admin) {
    echo "Step 4: Admin found in database<br>";
    echo "Admin name: " . $admin['full_name'] . "<br>";
    
    if (password_verify($password, $admin['password'])) {
        echo "Step 5: Password is CORRECT!<br>";
        
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_name'] = $admin['full_name'];
        $_SESSION['user_email'] = $admin['email'];
        $_SESSION['user_type'] = 'admin';
        $_SESSION['is_admin'] = true;
        
        echo "Step 6: Session variables set<br>";
        echo '<pre>';
        print_r($_SESSION);
        echo '</pre>';
        
        echo 'Step 7: Redirecting to admin_dashboard.php...<br>';
        // header('Location: admin_dashboard.php');
        echo '<a href="admin_dashboard.php">Click here to go to admin dashboard</a>';
    } else {
        echo "Step 5: Password is WRONG!<br>";
        echo "Stored hash: " . $admin['password'] . "<br>";
    }
} else {
    echo "Step 4: Admin NOT found in database<br>";
    
    // Show all admins
    $admins = $pdo->query("SELECT id, full_name, email FROM admin")->fetchAll();
    echo "<h3>Admins in database:</h3>";
    echo "<pre>";
    print_r($admins);
    echo "</pre>";
}
?>