<?php
session_start();
require_once 'conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'src/Exception.php';
require_once 'src/PHPMailer.php';
require_once 'src/SMTP.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$reenviado = false;
$error = '';

// Reenviar código si se presionó el botón
if (isset($_POST['reenviar'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $sql = "SELECT nombre, email FROM usuarios WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $stmt->bind_result($nombre, $email);
    $stmt->fetch();
    $stmt->close();

    $codigo = rand(100000, 999999);
    $_SESSION['codigo_verificacion'] = $codigo;
    $_SESSION['codigo_expira'] = time() + 900; // 15 minutos

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'elmateomoyano@gmail.com';
        $mail->Password   = 'xqbdmofgikjtmeei'; // clave de app
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('elmateomoyano@gmail.com', 'SuperQR');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Código de verificación';
        $mail->Body    = "<h3>Hola {$nombre}, tu nuevo código es: <b>$codigo</b></h3>";

        $mail->send();
        $reenviado = true;
    } catch (Exception $e) {
        $error = "No se pudo reenviar el correo: " . $mail->ErrorInfo;
    }
}

// Verificar código
if (isset($_POST['codigo'])) {
    $codigoIngresado = $_POST['codigo'] ?? '';
    $expira = $_SESSION['codigo_expira'] ?? 0;
    if (time() > $expira) {
        $error = "El código ha expirado. Por favor, haz clic en 'Reenviar código' para obtener uno nuevo.";
    } else if ($codigoIngresado == $_SESSION['codigo_verificacion']) {
        unset($_SESSION['codigo_verificacion']);
        unset($_SESSION['codigo_expira']);
        header("Location: home.php");
        exit;
    } else {
        $error = "Código incorrecto. Intente de nuevo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <div class="container">
        <h1>Verificación de código</h1>
        <div class="form-box">
            <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
            <?php if ($reenviado) echo "<div class='success'>Se ha enviado un nuevo código a tu correo.</div>"; ?>
            <form action="verificar.php" method="post">
                <label for="codigo">Ingrese el código enviado a su correo</label>
                <input type="text" id="codigo" name="codigo" required>
                <button type="submit">Verificar</button>
            </form>
            <form action="verificar.php" method="post" style="margin-top:18px;">
                <button type="submit" name="reenviar">Reenviar código</button>
            </form>
        </div>
    </div>
</body>
</html>