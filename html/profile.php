<?php
session_start();

include '../php/config.php';

if (!isset($_SESSION['loggedin'])) {
	header('Location: login.php');
	exit;
}

$currentPage = 'profile';

$range = 100;
$maxResultsPerRequest = 100;

$stmt = $conn->prepare("SELECT password, email, address, rank FROM users WHERE id = ?");
$stmt->bind_param("s", $_SESSION["id"]);
$stmt->execute();
$stmt->bind_result($password, $email, $address, $rank);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_users'])) {
	$numRequests = ceil($range / $maxResultsPerRequest);
	$allUsers = [];

	for ($j = 0; $j < $numRequests; $j++) {
		$results = min($maxResultsPerRequest, $range - ($j * $maxResultsPerRequest));
		$response = file_get_contents('https://randomuser.me/api/?results=' . $results . '&nat=GB');
		$userData = json_decode($response, true);

		if (!file_exists(__DIR__ . '/../logs')) {
			mkdir(__DIR__ . '/../logs', 0777, true);
		}

		$timestamp = date('Ymd-His');
		file_put_contents(__DIR__ . "/../logs/random_users_{$timestamp}_$j.json", $response);

		if ($userData && isset($userData['results'])) {
			$allUsers = array_merge($allUsers, $userData['results']);
		} else {
			console_log("Error fetching user data from API");
		}
	}

	foreach ($allUsers as $i => $user) {
		$randomUsername = $user['login']['username'];
		$randomName = $user['name']['first'] . ' ' . $user['name']['last'];
		$randomEmail = $user['email'];
		$randomAddress = $user['location']['street']['number'] . ' ' . $user['location']['street']['name'];
		$randomPassword = password_hash($user['login']['password'], PASSWORD_DEFAULT);

		$stmt = $conn->prepare("INSERT INTO users (username, name, email, address, password) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param('sssss', $randomUsername, $randomName, $randomEmail, $randomAddress, $randomPassword);

		if ($stmt->execute()) {
			console_log("User " . ($i + 1) . " created successfully");
		} else {
			console_log("Error creating user " . ($i + 1) . ": " . $stmt->error);
		}

		$stmt->close();
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
			<button id="edit-profile-button" type="submit" name="edit_profile" class="links link-2-color button-2">Edit Profile</button>
		</div>

		<div id="password-confirmation-form" class="login" style="display: none;">
			<h1>Confirm Password</h1>
			<form id="confirm-password-form" method="post" action="../php/confirm_password.php">
				<label for="confirm-password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="confirm-password" id="confirm-password" required>
				<input type="submit" value="Confirm Password">
			</form>
			<?php if (isset($_SESSION['message']) && ($_SESSION['message'] === "Password confirmation failed" || $_SESSION['message'] === "Invalid request method")): ?>
				<style>
					.login form input[type="submit"] {
						border-radius: 0;
					}
				</style>
				<div class="dialog-warning"><?php echo $_SESSION['message']; ?></div>
				<script>
					document.addEventListener('DOMContentLoaded', function() {
						document.getElementById('password-confirmation-form').style.display = 'block';
						document.querySelector('.content h2').style.display = 'none';
						document.querySelectorAll('.card').forEach(function(card) {
							card.style.display = 'none';
						});
					});
				</script>
				<?php unset($_SESSION['message']); ?>
			<?php endif; ?>
		</div>

		<?php if (in_array($rank, ['Supervisor', 'Manager', 'Director'])): ?>
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

				<?php if (isset($_SESSION['message']) && isset($_SESSION['message_type'])): ?>
					<?php if ($_SESSION['message_type'] === "success"): ?>
						<div class="dialog-success"><?php echo $_SESSION['message'] ?></div>
					<?php else: ?>
						<div class="dialog-warning"><?php echo $_SESSION['message'] ?></div>
					<?php endif; ?>
					<?php console_log($_SESSION['message']); ?>
					<?php unset($_SESSION['message']); ?>
				<?php endif; ?>
			</div>

			<div class="card">
				<form method="post">
					<button type="submit" name="generate_users" class="links link-2-color button-2">Generate <?php echo $range ?> Users</button>
				</form>
			</div>
		<?php endif; ?>
	</div>

	<script src="../js/confirm_password.js"></script>
	<script src="../js/main.js"></script>
</body>
</html>

<?php
	$conn->close();
?>