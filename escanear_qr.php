<!DOCTYPE html>
<html lang="es">
<?php
session_start();
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: inicio.php');
    exit;
}
?>
<head>
    <meta charset="UTF-8">
    <title>Escanear QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body { background: #174a6c; color: #fff; font-family: 'Montserrat', Arial, sans-serif; }
        .container { background: rgba(255,255,255,0.07); border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.12); padding: 40px 32px; max-width: 400px; margin: 60px auto; text-align: center; }
        #qr-reader { margin: 0 auto; }
        #result { margin-top: 20px; font-size: 1.2em; background: #fff; color: #174a6c; padding: 12px 18px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header mb-4" style="display:flex; justify-content:center; align-items:center; padding:10px 30px; background:#4876a5ff; margin-bottom:30px; border-radius:10px;">
            <div class="nav-btns w-100 d-flex justify-content-center gap-3" style="gap:24px;">
                <a href="inicio.php" class="nav-btn" style="background:#174a6c; color:#fff; border:none; border-radius:7px; font-size:1em; font-weight:500; padding:10px 32px; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.08); transition:background 0.2s; text-decoration:none;">Inicio</a>
                <a href="qr.php" class="nav-btn" style="background:#174a6c; color:#fff; border:none; border-radius:7px; font-size:1em; font-weight:500; padding:10px 32px; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.08); transition:background 0.2s; text-decoration:none;">QR</a>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <a href="Tabla.php" class="nav-btn" style="background:#174a6c; color:#fff; border:none; border-radius:7px; font-size:1em; font-weight:500; padding:10px 32px; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.08); transition:background 0.2s; text-decoration:none;">Tabla</a>
                <?php endif; ?>
            </div>
        </div>
        <h1>Escanear código QR</h1>
        <div id="qr-reader" style="width:320px;"></div>
        <div id="result">Esperando escaneo...</div>
    </div>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Acepta cualquier texto y lo envía a tabla.php
            window.location.href = 'tabla.php?qr=' + encodeURIComponent(decodedText);
        }
        function onScanError(errorMessage) {
            // Puedes mostrar errores si lo deseas
        }
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 10, qrbox: 250 }, false);
        html5QrcodeScanner.render(onScanSuccess, onScanError);
    </script>
</body>
</html>
