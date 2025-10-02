 <?php
// Archivo de conexión a la base de datos "congreso"

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'congreso';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

?>