<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$isAdmin = $_SESSION['admin'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing</title>
    <style>
        body {
            padding-top: 60px;
        }

        .navbar {
            height: 50px;
            overflow: hidden;
            background-color: #333;
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            color: white;
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .navbar-center {
            text-align: center;
            margin: auto;
        }
    </style>
</head>
<body>
<div class="navbar">
    <?php if ($isAdmin): ?>
        <a href="manage_movies.php">Manage Movies</a>
        <a href="manage_tv_shows.php">Manage TV Shows</a>
        <a href="manage_user_movies.php">My Movies</a>
        <a href="manage_user_tv_shows.php">My TV Shows</a>
        <a href="user_management.php">User Management</a>
        <a href="media_popularity_report.php">Generate Media Popularity Report</a>
    <?php else: ?>
        <a href="manage_user_movies.php">My Movies</a>
        <a href="manage_user_tv_shows.php">My TV Shows</a>
    <?php endif; ?>

    <div style="float: right;"><a href="logout.php">Logout</a></div>
</div>

</body>
</html>
