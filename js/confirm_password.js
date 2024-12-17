document.addEventListener('DOMContentLoaded', function() {
	var editProfileButton = document.getElementById('edit-profile-button');
	if (editProfileButton) {
			editProfileButton.addEventListener('click', function() {
					document.querySelectorAll('.card').forEach(function(card) {
							card.style.display = 'none';
					});
					document.getElementById('password-confirmation-form').style.display = 'block';
			});
	}

	var confirmPasswordForm = document.getElementById('confirm-password-form');
	if (confirmPasswordForm) {
		confirmPasswordForm.addEventListener('submit', function(event) {
			event.preventDefault();
			var formData = new FormData(this);
			
			fetch('../php/confirm_password.php', {
				method: 'POST',
				body: formData
			})
			.then(response => response.text())
			.then(text => {
				try {
					const data = JSON.parse(text);
					if (data.success) {
						document.getElementById('password-confirmation-form').style.display = 'none';
						document.getElementById('edit-profile-form').style.display = 'block';
					} else {
						alert(data.message || 'Password confirmation failed. Please try again.');
					}
				} catch (error) {
					console.error('Error parsing JSON:', error);
					console.error('Raw response:', text);
				}
			})
			.catch(error => console.error('Error:', error));
		});
	}
});