<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require 'config.php';

$tv_show_id = isset($_GET['tv_show_id']) ? intval($_GET['tv_show_id']) : 0;

$sql = "SELECT v.*, ue.watched, ue.liked, tv_shows.seasons, tv_shows.episodes, tv_shows.description

        FROM tv_show_episodes v
        JOIN tv_shows ON v.tv_show_id = tv_shows.tv_show_id
        LEFT JOIN user_episodes ue ON v.episode_id = ue.episode_id AND ue.user_id = ? 
        WHERE v.tv_show_id = ? 
        ORDER BY v.season_number, v.episode_number";


$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindParam(2, $tv_show_id, PDO::PARAM_INT);
$stmt->execute();

$user_episodes = [];
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$tv_show = null;
foreach ($result as $row) {
    if (!$tv_show) {
        $tv_show = [
            'title' => $row['tv_show_title'],
            'seasons' => $row['seasons'],
            'episodes' => $row['episodes'],
            'description' => $row['description'],
            'poster_image_url' => $row['poster_image_url'],
        ];
    }

    $user_episodes[$row['episode_id']] = $row;
}

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
echo "<div>";
echo "<img src='{$tv_show['poster_image_url']}' height='200'>";
echo "<h2>{$tv_show['title']}</h2>";
echo "<p>{$tv_show['description']}</p>";
echo "</div>";

echo "<form method='post' action='process_user_episodes.php'>";
echo "<table>";

echo "<tr>";
echo "<th>Season</th>";
echo "<th>Episode</th>";
echo "<th>Title</th>";
echo "<th>Watched</th>";
echo "<th>Liked</th>";
echo "</tr>";

foreach ($user_episodes as $episode) {
    $watched = isset($user_episodes[$episode['episode_id']]['watched']) ? $user_episodes[$episode['episode_id']]['watched'] : 0;
    $liked = isset($user_episodes[$episode['episode_id']]['liked']) ? $user_episodes[$episode['episode_id']]['liked'] : 0; // Get the liked status
    
    echo "<tr>";
    echo "<td>Season {$episode['season_number']}</td>";
    echo "<td>Episode {$episode['episode_number']}</td>";
    echo "<td>{$episode['episode_title']}</td>";
    echo "<td><input type='checkbox' name='watched[{$episode['episode_id']}]' value='1' " . ($watched ? 'checked' : '') . "></td>";
    if ($watched) {
        $liked = isset($user_episodes[$episode['episode_id']]['liked']) ? $user_episodes[$episode['episode_id']]['liked'] : 0;
        echo "<td><input type='checkbox' name='liked[{$episode['episode_id']}]' value='1' " . ($liked ? 'checked' : '') . "></td>";
    } else {
    echo "<td></td>";
    }

    echo "</td>"; 
    echo "<input type='hidden' name='episode_id[]' value='{$episode['episode_id']}'>";
    echo "<input type='hidden' name='tv_show_id' value='{$_GET['tv_show_id']}'>";
    echo "</tr>";
}

echo "</table>
      <button type='submit'>Save</button>
    </form>";
?>
