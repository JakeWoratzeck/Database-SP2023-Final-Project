<?php
    session_start();
    require 'config.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['admin'] !== '1') {
        header('Location: index.php');
        exit();
    }

    if (!isset($_POST['tv_show_id'])) {
        header('Location: manage_tv_shows.php');
        exit();
    }

    $tv_show_id = $_POST['tv_show_id'];
    $action = $_POST['action'];

    if ($action === 'delete' && isset($_POST['selected_episodes'])) {
        $selected_episodes = $_POST['selected_episodes'];

        foreach ($selected_episodes as $episode_id) {
            $sql = "DELETE FROM user_episodes WHERE episode_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $episode_id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    if ($action === 'add') {
        $title = $_POST['title'];
        $season_number = $_POST['season_number'];
        $episode_number = $_POST['episode_number'];

        $sql = "INSERT INTO episodes (tv_show_id, title, season_number, episode_number) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tv_show_id, $title, $season_number, $episode_number]);
    } elseif ($action === 'delete' && isset($_POST['selected_episodes'])) {
        if (!empty($selected_episodes)) {
            $placeholders = rtrim(str_repeat('?,', count($selected_episodes)), ',');
            $sql = "DELETE FROM episodes WHERE episode_id IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $stmt->execute($selected_episodes);
        }
    } elseif ($action === 'update_confirm') {
        $episode_id = $_POST['episode_id'];
        $title = $_POST['title'];
        $season_number = $_POST['season_number'];
        $episode_number = $_POST['episode_number'];

        $sql = "UPDATE episodes SET title = ?, season_number = ?, episode_number = ? WHERE episode_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$title, $season_number, $episode_number, $episode_id]);
    }

    header('Location: manage_episodes.php?tv_show_id=' . $tv_show_id);
    exit();
?>
