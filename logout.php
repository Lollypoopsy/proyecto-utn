<?php
// logout.php - Cierra la sesión
session_start();
session_destroy();
header('Location: index.php');
exit;
?>
<!-- Removed inline CSS -->
<!-- Adapted HTML structure to use classes from global.css and Bootstrap -->
<div class="container">
	<div class="header">
		<button class="btn btn-primary nav-btn">Logout</button>
	</div>
</div>
 		
<?php
// logout.php - Cierra la sesión
session_start();
session_destroy();
header('Location: index.php');
exit;
?>

<style scoped>
@media (max-width: 900px) {
			.header {
				flex-direction: column;
				gap: 10px;
				padding: 10px 10px;
			}
			.nav-btns {
				gap: 10px;
				flex-wrap: wrap;
			}
		}
		@media (max-width: 500px) {
			.nav-btn {
				font-size: 0.95em;
				padding: 8px 10px;
			}
			.user-icon {
				font-size: 2em;
			}
		}
</style>