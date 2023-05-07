<?php
session_start();

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $watched_episodes = isset($_POST['watched']) ? $_POST['watched'] : [];
    $liked_episodes = isset($_POST['liked']) ? $_POST['liked'] : [];

    foreach ($_POST['episode_id'] as $episode_id) {
        $watched = isset($watched_episodes[$episode_id]) ? 1 : 0;
        $liked = isset($liked_episodes[$episode_id]) ? 1 : 0;

        $sql = "SELECT * FROM user_episodes WHERE user_id=? AND episode_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $episode_id]);
        $row = $stmt->fetch();

        if ($row) { 
            if ($watched) {
                $sql = "UPDATE user_episodes SET watched=?, liked=? WHERE user_id=? AND episode_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$watched, $liked, $user_id, $episode_id]);
            } else {
                $sql = "DELETE FROM user_episodes WHERE user_id=? AND episode_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$user_id, $episode_id]);
            }
        } else { 
            if ($watched) {
                $sql = "INSERT INTO user_episodes (user_id, episode_id, watched, liked) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$user_id, $episode_id, $watched, $liked]);
            }
        }
    }
}

header('Location: manage_user_episodes.php?tv_show_id=' . $_POST['tv_show_id']);
exit();

?>
