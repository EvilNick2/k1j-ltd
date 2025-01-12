<?php
$prefix = '';
if (isset($currentPage) && $currentPage !== 'index') {
	$prefix = '../';
}
?>

<nav>
	<div class="logo">
		<img src="<?php echo $prefix; ?>imgs/logo.svg" alt="K1J LTD Logo">
	</div>

	<div class="links left">
		<a class="link-1-color" href="<?php echo $prefix; ?>index.php">Home</a>
		<a class="link-1-color" href="<?php echo $prefix; ?>html/dashboard.php">Analytics</a>
		<?php if (isset($_SESSION['rank']) && $rankHierarchy[$_SESSION['rank']] >= $rankHierarchy['Employee']): ?>
			<a class="link-1-color" href="<?php echo $prefix; ?>html/products.php">Products</a>
		<?php endif; ?>
	</div>

	<div class="links right">
		<a class="link-2-color button-1" id="light-mode-toggle">Toggle Dark Mode</a>
		
		<?php if (isset($currentPage) && $currentPage === 'login'): ?>
			<?php if (isset($_SESSION['loggedin'])): ?>
				<?php $firstName = explode(' ', $_SESSION['name'])[0]; ?>
				<a class="link-2-color button-2" href="<?php echo $prefix; ?>html/profile.php">
					<?php echo htmlspecialchars($firstName, ENT_QUOTES); ?>
				</a>
			<?php else: ?>
				<a class="link-2-color button-2" href="<?php echo $prefix; ?>html/register.php">Register</a>
			<?php endif; ?>
		<?php elseif (isset($currentPage) && $currentPage === 'profile'): ?>
			<?php if (isset($_SESSION['loggedin'])): ?>
				<a class="link-2-color button-2" href="<?php echo $prefix; ?>php/logout.php">Logout</a>
			<?php else: ?>
				<a class="link-2-color button-2" href="<?php echo $prefix; ?>html/login.php">Login</a>
			<?php endif; ?>
		<?php else: ?>
			<?php if (isset($_SESSION['loggedin'])): ?>
				<?php $firstName = explode(' ', $_SESSION['name'])[0]; ?>
				<a class="link-2-color button-2" href="<?php echo $prefix; ?>html/profile.php">
					<?php echo htmlspecialchars($firstName, ENT_QUOTES); ?>
				</a>
			<?php else: ?>
				<a class="link-2-color button-2" href="<?php echo $prefix; ?>html/login.php">Login</a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</nav>