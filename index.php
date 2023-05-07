<?php
require 'config.php';
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = 'SELECT * FROM users WHERE username = :username';
    $stmt = $conn->prepare($sql);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);
    
    if ($user && password_verify($password, $user->password_hash)) {
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['admin'] = $user->admin;
        header('Location: landing.php');
        exit;
    } else {
        echo "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<body>
<form method="POST" action="">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username"><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br>
    <input type="submit" value="Login">
</form>
<p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>
</html>
