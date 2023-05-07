<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require 'config.php';

$sql = "SELECT tv_shows.*, user_tv_shows_view.status, user_tv_shows_view.liked, 
               watched_episodes_count(:user_id, tv_shows.tv_show_id) as watched_episodes
        FROM tv_shows
        LEFT JOIN user_tv_shows_view ON tv_shows.tv_show_id = user_tv_shows_view.tv_show_id
        AND user_tv_shows_view.user_id = :user_id
        ORDER BY tv_shows.tv_show_id";

$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['user_id']]);

$user_tv_shows = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $user_tv_shows[$row['tv_show_id']] = $row;
}

$status_options = ['watching', 'completed', 'want_to_watch'];
?>
<!DOCTYPE html>
<html>
<head>
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
        <a href="landing.php">Home</a>
        <div class="navbar-center"><h2>My TV Shows</h2></div>
        <div style="float: right;"><a href="logout.php">Logout</a></div>
    </div>
<?php
echo "<form method='post' action='process_user_tv_shows.php'>";
echo "<table>";

echo "<tr><th>Poster</th><th>Title</th><th>Description</th><th>Release Date</th><th>Seasons</th><th>Episodes Watched</th><th>Total Episodes</th><th>Liked</th><th>Status</th></tr>";

if (!empty($user_tv_shows)) {
foreach ($user_tv_shows as $tv_show) {
    $liked = isset($user_tv_shows[$tv_show['tv_show_id']]['liked']) ? $user_tv_shows[$tv_show['tv_show_id']]['liked'] : 0;
    $status = isset($user_tv_shows[$tv_show['tv_show_id']]['status']) ? $user_tv_shows[$tv_show['tv_show_id']]['status'] : '';
    
    echo "<tr>";
    echo "<td><img src='{$tv_show['poster_image_url']}' height='100'></td>";
    echo "<td>{$tv_show['title']}</td>";
    echo "<td>{$tv_show['description']}</td>";
    echo "<td>{$tv_show['release_date']}</td>";
    echo "<td>{$tv_show['seasons']}</td>";
    echo "<td>" . htmlspecialchars($tv_show['watched_episodes']) . "</td>";
    echo "<td>{$tv_show['episodes']}</td>";
    echo "<td>". ($status == 'completed' ? "<input type='checkbox' name='liked[{$tv_show['tv_show_id']}]' value='1' " . ($liked ? 'checked' : '') . ">" : '') . "</td>";
    echo "<td>";
    echo "<select name='status[{$tv_show['tv_show_id']}]'>";
    echo "<option value=''></option>";
    foreach ($status_options as $option) {
        $selected = ($status == $option) ? 'selected' : '';
        echo "<option value='$option' $selected>$option</option>";
    }
    echo "</select>";
    echo "</td>";
    echo "<input type='hidden' name='tv_show_id[]' value='{$tv_show['tv_show_id']}'>";
    echo "<td><a href='manage_user_episodes.php?tv_show_id={$tv_show['tv_show_id']}' class='btn btn-primary'>Manage Episodes</a></td>";
    echo "</tr>";
}
}

echo "</table>
      <button type='submit'>
      Save</button>
      </form>";
      ?>
      