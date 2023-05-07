<?php
require 'config.php';
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = 'INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)';
    $stmt = $conn->prepare($sql);
    $stmt->execute(['username' => $username, 'password_hash' => $password]);

    $user_id = $conn->lastInsertId();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['admin'] = false;  
    echo "You have successfully registered. You may now <a href='landing.php'>proceed to the landing page</a>.";
    exit;
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
    <input type="submit" value="Register">
</form>
</body>
</html>
