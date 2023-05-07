<?php

session_start();

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $liked_tv_shows = isset($_POST['liked']) ? $_POST['liked'] : [];
    $status_tv_shows = isset($_POST['status']) ? $_POST['status'] : [];

    foreach ($status_tv_shows as $tv_show_id => $status) {
        $liked = isset($liked_tv_shows[$tv_show_id]) ? 1 : 0;

        $sql = "SELECT * FROM user_tv_shows WHERE user_id=? AND tv_show_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $tv_show_id]);
        $row = $stmt->fetch();

        if ($row) { 
            if (!empty($status)) {
                $sql = "UPDATE user_tv_shows SET liked=?, status=? WHERE user_id=? AND tv_show_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$liked, $status, $user_id, $tv_show_id]);
            } else {
                $sql = "DELETE FROM user_tv_shows WHERE user_id=? AND tv_show_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$user_id, $tv_show_id]);
            }
        } else if (!empty($status)) { 
            $sql = "INSERT INTO user_tv_shows (user_id, tv_show_id, liked, status) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id, $tv_show_id, $liked, $status]);
        }
    }
}

header('Location: manage_user_tv_shows.php');
exit();
?>
