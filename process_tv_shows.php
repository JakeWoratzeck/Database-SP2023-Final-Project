<?php
require 'config.php';

$action = $_POST['action'];

if ($action === 'create') {
    $title = $_POST['title'];
    $release_date = $_POST['release_date'];
    $description = $_POST['description'];
    $poster_image_url = $_POST['poster_image_url'];
    $seasons = $_POST['seasons'];
    $episodes = $_POST['episodes'];

    $sql = "INSERT INTO tv_shows (title, release_date, description, poster_image_url, seasons) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->errorInfo()[2]);
    }

    $stmt->execute([$title, $release_date, $description, $poster_image_url, $seasons]);

} elseif ($action === 'delete') {
    $selected_tv_shows = $_POST['selected_tv_shows'];

    foreach ($selected_tv_shows as $tv_show_id) {
        $sql = "SELECT episode_id FROM episodes WHERE tv_show_id=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->errorInfo()[2]);
        }

        $stmt->execute([$tv_show_id]);
        $episode_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($episode_ids as $episode_id) {
            $sql = "DELETE FROM user_episodes WHERE episode_id=?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Prepare failed: " . $conn->errorInfo()[2]);
            }

            $stmt->execute([$episode_id]);
        }

        $sql = "DELETE FROM episodes WHERE tv_show_id=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->errorInfo()[2]);
        }

        $stmt->execute([$tv_show_id]);

        $sql = "DELETE FROM user_tv_shows WHERE tv_show_id=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->errorInfo()[2]);
        }

        $stmt->execute([$tv_show_id]);

        $sql = "DELETE FROM tv_shows WHERE tv_show_id=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->errorInfo()[2]);
        }

        $stmt->execute([$tv_show_id]);
    }
}
 elseif ($action === 'update_confirm') {
    $tv_show_id = $_POST['tv_show_id'];
    $title = $_POST['title'];
    $release_date = $_POST['release_date'];
    $description = $_POST['description'];
    $poster_image_url = $_POST['poster_image_url'];
    $seasons = $_POST['seasons'];

    $sql = "UPDATE tv_shows SET title=?, release_date=?, description=?, poster_image_url=?, seasons=? WHERE tv_show_id=?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->errorInfo()[2]);
    }

    $stmt->execute([$title, $release_date, $description, $poster_image_url, $seasons, $tv_show_id]);
}

$conn = null;

header("Location: manage_tv_shows.php");
?>
