<?php
include '../php/config.php';

session_start();

if (!isset($_SESSION['loggedin'])) {
		header('Location: login.php');
		exit;
}

$currentPage = 'profile';

$range = 100;

$stmt = $conn->prepare("SELECT password, email, address, rank FROM users WHERE id = ?");
$stmt->bind_param("s", $_SESSION["id"]);
$stmt->execute();
$stmt->bind_result($password, $email, $address, $rank);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_users'])) {
	$randomUser = "user" . rand(1, $range);
	$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE username = '$randomUser'");
	$row = $result->fetch_assoc();

	if ($row['count'] == 0) {
		for ($i = 1; $i <= $range; $i++) {
			$randomUsername = "user$i";
			$randomName = "User $i";
			$randomEmail = "user$i@example.com";
			$randomAddress = "Address $i";
			$randomPassword = password_hash("password$i", PASSWORD_DEFAULT);

			$sql = "INSERT INTO users (username, name, email, address, password) VALUES ('$randomUsername', '$randomName', '$randomEmail', '$randomAddress', '$randomPassword')";
			if ($conn->query($sql) === TRUE) {
				console_log("User $i created successfully");
			} else {
				console_log("Error creating user $i: " . $conn->error);
			}
		}
	} else {
		console_log("Users already exist in the database");
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../fonts/css/all.min.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/profile.css">
	<link rel="icon" href="../imgs/logo.svg" type="image/svg">
	<title>Profile</title>
</head>
<body>
	<?php include 'nav.php'; ?>

<div class="content">
    <h2>Profile Page</h2>
    
    <!-- Card for user details table -->
    <div class="card">
        <p>Your account details are below:</p>
        <table>
            <tr>
                <td>Username:</td>
                <td><?=htmlspecialchars($_SESSION['username'], ENT_QUOTES)?></td>
            </tr>
            <tr>
                <td>Name:</td>
                <td><?=htmlspecialchars($_SESSION['name'], ENT_QUOTES)?></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><?=htmlspecialchars($password, ENT_QUOTES)?></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><?=htmlspecialchars($email, ENT_QUOTES)?></td>
            </tr>
            <tr>
                <td>Address:</td>
                <td><?=htmlspecialchars($address, ENT_QUOTES)?></td>
            </tr>
            <tr>
                <td>Rank:</td>
                <td><?=htmlspecialchars($rank, ENT_QUOTES)?></td>
            </tr>
        </table>
    </div>

    <?php if (in_array($rank, ['Supervisor', 'Manager', 'Director'])): ?>
        <!-- Card for Change Rank form -->
        <div class="card">
            <form method="post" action="../php/change_rank.php">
                <h3>Change User Rank</h3>
                <label for="username">Select User:</label>
                <select name="username" id="username" required>
                    <?php
                        $sql = "SELECT username FROM users WHERE username != ? ORDER BY id ASC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('s', $_SESSION['username']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['username'], ENT_QUOTES) . '">' 
                                . htmlspecialchars($row['username'], ENT_QUOTES) . '</option>';
                        }
                        $stmt->close();
                    ?>
                </select>

                <label for="rank">Select Rank:</label>
                <select name="rank" id="rank" required>
                    <?php
                        foreach ($rankHierarchy as $rankOption => $rankValue) {
                            if ($rankHierarchy[$rank] >= $rankValue) {
                                echo '<option value="' . $rankOption . '">' . $rankOption . '</option>';
                            }
                        }
                    ?>
                </select>

                <button type="submit">Change Rank</button>
            </form>

            <?php if (isset($_SESSION['message'])): ?>
                <?php console_log($_SESSION['message']); ?>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Card for Generate Users button -->
    <div class="card">
        <form method="post">
            <button type="submit" name="generate_users" class="links link-2-color button-2">Generate <?php echo $range ?> Users</button>
        </form>
    </div>
</div>

	<script src="../js/main.js"></script>
</body>
</html>

<?php
	$conn->close();
?>