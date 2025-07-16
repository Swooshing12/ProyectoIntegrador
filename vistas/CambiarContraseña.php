<?php
require_once __DIR__ . "/../config/config.php";

session_start();

// Verificar si el usuario está autenticado y en estado "Pendiente" (id_estado = 3)
if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_estado"] != 3) {
    header("Location: " . BASE_URL . "/vistas/login.php");
    exit();
}

// Capturar alerta si existe
$alerta = isset($_SESSION["alerta"]) ? $_SESSION["alerta"] : null;
unset($_SESSION["alerta"]); // Eliminar alerta después de mostrarla
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoReport - Cambiar Contraseña</title>
    <link rel="stylesheet" href="../estilos/cambiarclave.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-container">
        <h2>Cambiar Contraseña</h2>
        <p class="subtitle">Establece una nueva contraseña segura para proteger tu cuenta</p>
        
        <form action="../controladores/CambiarContraseñaControlador/CambiarContraseñaController.php" method="POST">
            <div class="input-group">
                <label for="password" data-icon="🔒">Nueva Contraseña</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" required>
                    <div class="input-glow"></div>
                    <div class="input-icon">🔒</div>
                </div>
                <div class="password-strength">
                    <div class="strength-bar"></div>
                    <div class="strength-bar"></div>
                    <div class="strength-bar"></div>
                    <div class="strength-bar"></div>
                </div>
            </div>
            
            <div class="input-group">
                <label for="confirm_password" data-icon="🔐">Confirmar Contraseña</label>
                <div class="input-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password" required>
                    <div class="input-glow"></div>
                    <div class="input-icon">🔐</div>
                </div>
            </div>
            
            <button type="submit">Actualizar Contraseña</button>
            
            <div class="security-info">
                <p>Tu contraseña debe tener al menos 8 caracteres e incluir letras, números y símbolos especiales.</p>
            </div>
        </form>
    </div>

    <!-- Mostrar alerta si existe -->
    <?php if (!empty($alerta)): ?>
    <script>
        Swal.fire({
            title: "<?php echo $alerta['titulo']; ?>",
            text: "<?php echo $alerta['mensaje']; ?>",
            icon: "<?php echo $alerta['icono']; ?>",
            confirmButtonColor: "#16a34a",
            confirmButtonText: "Aceptar"
        });
    </script>
    <?php endif; ?>

</body>
<script src="<?= BASE_URL ?>/js/bloquear.js"></script>
</html>