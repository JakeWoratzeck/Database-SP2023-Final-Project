<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require 'config.php';

$tv_show_id = isset($_GET['tv_show_id']) ? intval($_GET['tv_show_id']) : 0;

$sql = "SELECT v.*, ue.watched, tv_shows.seasons, tv_shows.episodes, tv_shows.description
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
<script>
function handleUpdateButtonClick() {
    const checkboxes = document.getElementsByName("selected_episodes[]");
    let selectedEpisodeId;
    let selectedCount = 0;

    for (let i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            selectedEpisodeId = checkboxes[i].value;
            selectedCount++;
            if (selectedCount > 1) {
                alert("Please select only one episode to update.");
                return;
            }
        }
    }

    if (selectedEpisodeId) {
        window.location.href = "manage_episodes.php?tv_show_id=<?php echo $tv_show_id; ?>&action=update&update_id=" + selectedEpisodeId;
    } else {
        alert("Please select an episode to update.");
    }
}

</script>
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
<div>
<img src="<?php echo htmlspecialchars($tv_show['poster_image_url']); ?>" height="200">
    <h2><?php echo htmlspecialchars($tv_show['title']); ?></h2>
    <p><?php echo htmlspecialchars($tv_show['description']); ?></p>
</div>

<form action="process_episodes.php" method="post">
<table>
<tr>
<th>Select</th>
<th>Season</th>
<th>Episode</th>
<th>Title</th>
</tr>
<?php
         foreach ($user_episodes as $episode) {
             echo "<tr>
                 <td><input type='checkbox' name='selected_episodes[]' value='{$episode['episode_id']}'></td>
                 <td>{$episode['season_number']}</td>
                 <td>{$episode['episode_number']}</td>
                 <td>{$episode['episode_title']}</td>
             </tr>";
         }
         ?>
</table>
<button type="button" onclick="handleUpdateButtonClick()">Update Selected Episode</button>
<input type="hidden" name="tv_show_id" value="<?php echo $tv_show_id; ?>">
<button type="submit" name="action" value="delete">Delete Selected Episodes</button>
</form>
<?php
if (isset($_GET['update_id']) && isset($_GET['action']) && $_GET['action'] == 'update') {
$update_id = $_GET['update_id'];
$sql = "SELECT * FROM tv_show_episodes WHERE episode_id=?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $update_id, PDO::PARAM_INT);
$stmt->execute();

if ($episode = $stmt->fetch(PDO::FETCH_ASSOC)) {
?>
<h2>Update Episode</h2>
<form action="process_episodes.php" method="post">
    <input type="hidden" name="episode_id" value="<?php echo $episode['episode_id']; ?>">
    <label for="season_number">Season Number:</label>
    <input type="number" name="season_number" id="season_number" value="<?php echo $episode['season_number']; ?>" required>
    <br>
    <label for="episode_number">Episode Number:</label>
    <input type="number" name="episode_number" id="episode_number" value="<?php echo $episode['episode_number']; ?>" required>
    <br>
    <label for="title">Title:</label>
    <input type="text" name="title" id="title" value="<?php echo $episode['episode_title']; ?>" required>
    <br>
    <input type="hidden" name="tv_show_id" value="<?php echo $tv_show_id; ?>">
    <button type="submit" name="action" value="update_confirm">Update Episode</button>
</form>

<?php
}


$conn = null;
}
?>
<h2>Add New Episode</h2>
<form action="process_episodes.php" method="post">
    <input type="hidden" name="tv_show_id" value="<?php echo $tv_show_id; ?>">
    <label for="season_number">Season Number:</label>
    <input type="number" name="season_number" id="season_number" required>
    <br>
    <label for="episode_number">Episode Number:</label>
    <input type="number" name="episode_number" id="episode_number" required>
    <br>
    <label for="title">Title:</label>
    <input type="text" name="title" id="title" required>
    <br>
    <button type="submit" name="action" value="add">Add Episode</button>
</form>
</body>
</html>
