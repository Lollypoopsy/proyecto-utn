<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Super QR - Iniciar Sesión</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Estilos trasladados a global.css -->
    <style>
        .shine-title {
            display: inline-block;
            position: relative;
            color: #fff;
            text-align: center;
            background: linear-gradient(90deg, #6bb6ff 0%, #fff 40%, #6bb6ff 60%, #fff 100%);
            background-size: 200% auto;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 2.5s linear infinite;
        }
        @keyframes shine {
            0% {
                background-position: 200% center;
            }
            100% {
                background-position: -200% center;
            }
        }
    </style>
</head>
<body>
    <div class="container d-flex flex-column align-items-center justify-content-center min-vh-100 p-3" style="max-width: 100vw;">
        <h1 class="text-center mb-2 shine-title">Congreso</h1>
        <br>
        <br>
        <div class="w-100 d-flex flex-column justify-content-start align-items-start gap-3" style="margin-left: 60px;">
            <form action="login.php" method="get" class="w-100" style="max-width: 200px; min-width: 0;">
                <button type="submit" class="form-btn w-100 py-3 fs-4">Iniciar Sesión</button>
            </form>
            <form action="registro.php" method="get" class="w-100" style="max-width: 200px; min-width: 0;">
                <button type="submit" class="form-btn w-100 py-3 fs-4">Registrarse</button>
            </form>
        </div>
    </div>
    <div class="utn-bar d-none d-md-flex align-items-center" style="gap:18px; min-height:70px;">
    <img src="img/utn-logo.png" alt="UTN Logo" style="height:200px; width:1000px; margin-top: -30px; object-fit:contain; margin-right:20px; margin-left:1px; transform: rotate(90deg);">
    <div class="circle d-none d-md-block"></div>
        <script>
            document.getElementById('userIcon').onclick = function(event) {
                event.stopPropagation();
                var menu = document.getElementById('userMenu');
                menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
            };
            document.addEventListener('click', function(e) {
                var menu = document.getElementById('userMenu');
                var icon = document.getElementById('userIcon');
                if (menu.style.display === 'block' && !menu.contains(e.target) && e.target !== icon && !icon.contains(e.target)) {
                    menu.style.display = 'none';
                }
            });
        </script>
    </body>
</html>
