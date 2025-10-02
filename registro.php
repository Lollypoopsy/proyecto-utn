<?php
session_start();
require_once 'conexion.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = $_POST['nombre'] ?? '';
    $dni      = $_POST['dni'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($nombre && $dni && $password && $email) {
        // Validación local de DNI argentino
        if (!preg_match('/^[1-9][0-9]{6,7}$/', $dni) || preg_match('/^(\d)\1{6,7}$/', $dni)) {
            $error = 'El DNI debe tener 7 u 8 dígitos, no puede empezar con 0 ni ser todos los números iguales.';
        } else {
            // Validar que el DNI no exista
            $sql = "SELECT id FROM usuarios WHERE dni = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $dni);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = 'El DNI ya está registrado.';
            } else {
                // Validar que el nombre no exista
                $sql = "SELECT id FROM usuarios WHERE nombre = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $nombre);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error = 'El usuario ya existe.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO usuarios (nombre, dni, email, password) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssss", $nombre, $dni, $email, $hash);
                    if ($stmt->execute()) {
                        // Guardar usuario en sesión
                        $_SESSION['usuario_id'] = $stmt->insert_id;
                        $_SESSION['usuario_email'] = $email;
                        $_SESSION['usuario_nombre'] = $nombre;

                        // Generar código de verificación
                        $codigo = rand(100000, 999999);
                        $_SESSION['codigo_verificacion'] = $codigo;
                        $_SESSION['codigo_expira'] = time() + 300;

                        // Enviar correo (PHPMailer)
                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->Host       = 'smtp.gmail.com';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = 'elmateomoyano@gmail.com';
                            $mail->Password   = 'xqbdmofgikjtmeei'; // clave de app
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port       = 587;

                            $mail->setFrom('elmateomoyano@gmail.com', 'Congreso');
                            $mail->addAddress($email);

                            $mail->isHTML(true);
                            $mail->Subject = 'Código de verificación UTN';
                            $mail->CharSet = 'UTF-8';
                            $mail->Body    = '
                                <div style="font-family: Montserrat, Arial, sans-serif; background: #174a6c; padding: 0; margin: 0;">
                                    <div style="max-width: 480px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 16px #174a6c33; overflow: hidden;">
                                        <div style="background: #65bbf8; padding: 24px 0; text-align: center;">
                                            <span style="display: inline-block; background: #174a6c; color: #fff; font-size: 1.5em; font-weight: 800; letter-spacing: 2px; padding: 10px 28px; border-radius: 8px; box-shadow: 0 2px 8px #174a6c22;">UTN</span>
                                            <div style="font-size: 1.15em; color: #174a6c; font-weight: 600; margin-top: 12px; letter-spacing: 1px;">Verificación de registro</div>
                                        </div>
                                        <div style="padding: 32px 24px 24px 24px; text-align: center;">
                                            <p style="font-size: 1.1em; color: #174a6c; margin-bottom: 18px;">Hola <b>' . htmlspecialchars($nombre) . '</b>,<br>¡Gracias por registrarte!</p>
                                            <p style="font-size: 1.1em; color: #174a6c; margin-bottom: 18px; font-weight: bold;">Código de verificación:</p>
                                            <div style="font-size: 2.2em; font-weight: bold; color: #65bbf8; background: #174a6c; border-radius: 8px; padding: 16px 0; margin: 0 auto 24px auto; width: 220px; letter-spacing: 4px;">' . $codigo . '</div>
                                            <p style="color: #205b85; margin-bottom: 0;">Ingresa este código en la página para completar tu registro.<br>Si no solicitaste este correo, ignóralo.</p>
                                        </div>
                                        <div style="background: #174a6c; color: #fff; text-align: center; padding: 12px 0; font-size: 0.95em; border-radius: 0 0 12px 12px;">UTN &copy; 2025</div>
                                    </div>
                                </div>';
                            $mail->send();

                            header("Location: formulario.php");
                            exit;
                        } catch (Exception $e) {
                            $error = "No se pudo enviar el correo: " . $mail->ErrorInfo;
                        }
                    } else {
                        $error = 'Error al registrar usuario.';
                    }
                }
            }
            $stmt->close();
        }
    } else {
        $error = 'Completa todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Super QR - Registro</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <div class="container">
        <h1>Super QR</h1>
        <div class="form-box">
            <form action="registro.php" method="post" style="display:inline-block; width: 100%;">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="nombre" required>
                <label for="dni">DNI</label>
                <input type="text" id="dni" name="dni" required>
                <label for="email">Email</label>
                <input type="text" id="email" name="email" required>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                <div style="display: flex; gap: 10px; justify-content: flex-start;">
                    <button type="submit">Registrarse</button>
                    <button type="button" onclick="window.location.href='index.php'">Volver</button>
                </div>
            </form>
            <?php if (isset($error) && $error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success) && $success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>