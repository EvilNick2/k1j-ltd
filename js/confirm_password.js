document.addEventListener('DOMContentLoaded', function() {
	var editProfileButton = document.getElementById('edit-profile-button');
	if (editProfileButton) {
			editProfileButton.addEventListener('click', function() {
					document.querySelectorAll('.card').forEach(function(card) {
							card.style.display = 'none';
					});
					document.querySelector('.content h2').style.display = 'none';
					document.getElementById('password-confirmation-form').style.display = 'block';
			});
	}
});