<?php
session_start();


$alerta = isset($_SESSION["alerta"]) ? $_SESSION["alerta"] : null;
unset($_SESSION["alerta"]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoReport - Recuperar Contraseña</title>
    <link rel="stylesheet" href="../estilos/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-wrapper">
        <!-- Panel izquierdo -->
        <div class="login-image-panel">
            <div class="image-overlay">
                <div class="brand-content">
                    <div class="brand-logo">
                        <i class="bi bi-key-fill"></i>
                        <h1>Recuperación</h1>
                    </div>
                    <p class="brand-tagline">Restablece tu acceso al sistema</p>
                    <div class="features">
                        <div class="feature-item">
                            <i class="bi bi-envelope-fill"></i>
                            <span>Envío por Correo</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-shield-lock-fill"></i>
                            <span>Proceso Seguro</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-clock-fill"></i>
                            <span>Validez Temporal</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel derecho -->
        <div class="login-form-panel">
            <div class="login-container">
                <div class="login-header">
                    <div class="avatar-container">
                        <div class="avatar-circle">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <div class="avatar-glow"></div>
                    </div>
                    <h2>Recuperar Contraseña</h2>
                    <p>Ingresa tu correo electrónico y te enviaremos una contraseña temporal</p>
                    <div class="security-badge">
                        <i class="bi bi-shield-check"></i>
                        <span>Proceso Seguro</span>
                    </div>
                </div>

                <form action="../controladores/RecuperarPasswordControlador/RecuperarPasswordController.php" method="POST" class="login-form">
                    <div class="input-group">
                        <label class="input-label">Correo Electrónico</label>
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <input type="email" name="correo" placeholder="tu-correo@ejemplo.com" required class="form-input" autocomplete="email">
                            <div class="input-border"></div>
                            <div class="input-glow"></div>
                        </div>
                    </div>

                    <button type="submit" class="login-btn">
                        <div class="btn-background"></div>
                        <div class="btn-content">
                            <span class="btn-text">Enviar Contraseña Temporal</span>
                            <div class="btn-icon">
                                <i class="bi bi-send-fill"></i>
                            </div>
                        </div>
                        <div class="btn-ripple"></div>
                    </button>

                    <div class="divider">
                        <div class="divider-line"></div>
                        <span class="divider-text">Recuperación Segura</span>
                        <div class="divider-line"></div>
                    </div>

                    <div class="back-to-login">
                        <a href="login.php" class="back-link">
                            <i class="bi bi-arrow-left-circle-fill"></i>
                            Volver al Login
                        </a>
                    </div>
                </form>

                <div class="login-footer">
                    <div class="footer-content">
                        <div class="security-info">
                            <i class="bi bi-info-circle-fill"></i>
                            <span>La contraseña temporal será válida por 24 horas</span>
                        </div>
                        <p class="copyright">&copy; 2025 EcoReport. Cuidando el planeta juntos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($alerta)): ?>
    <script>
        Swal.fire({
            title: "<?php echo $alerta['titulo']; ?>",
            text: "<?php echo $alerta['mensaje']; ?>",
            icon: "<?php echo $alerta['icono']; ?>",
            confirmButtonColor: "#16a34a",
            confirmButtonText: "Entendido"
        }).then((result) => {
            if (result.isConfirmed && "<?php echo $alerta['icono']; ?>" === "success") {
                window.location.href = "login.php";
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>