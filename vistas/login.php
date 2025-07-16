<?php  
session_start(); 
$error = isset($_SESSION["error"]) ? $_SESSION["error"] : ""; 
unset($_SESSION["error"]);  

$alerta = isset($_SESSION["alerta"]) ? $_SESSION["alerta"] : null; 
unset($_SESSION["alerta"]);
?>  

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoReport - Iniciar Sesión</title>
    <link rel="stylesheet" type="text/css" href="../estilos/login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-wrapper">
        <!-- Panel izquierdo con imagen ambiental -->
        <div class="login-image-panel">
            <div class="image-overlay">
                <div class="brand-content">
                    <div class="brand-logo">
                        <i class="bi bi-shield-fill-check"></i>
                        <h1>EcoReport</h1>
                    </div>
                    <p class="brand-tagline">Sistema de Denuncias Ambientales</p>
                    <div class="features">
                        <div class="feature-item">
                            <i class="bi bi-tree-fill"></i>
                            <span>Protección Ambiental</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>Denuncias Geolocalizadas</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-clipboard2-data-fill"></i>
                            <span>Seguimiento en Tiempo Real</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-people-fill"></i>
                            <span>Participación Ciudadana</span>
                        </div>
                    </div>
                    <div class="environmental-stats">
                        <div class="stat-item">
                            <div class="stat-number">+1,250</div>
                            <div class="stat-label">Denuncias Resueltas</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">98%</div>
                            <div class="stat-label">Satisfacción</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel derecho con formulario -->
        <div class="login-form-panel">
            <div class="login-container">
                <!-- Header elegante con avatar ambiental -->
                <div class="login-header">
                    <div class="avatar-container">
                        <div class="avatar-circle">
                            <i class="bi bi-shield-fill-check"></i>
                        </div>
                        <div class="avatar-glow"></div>
                    </div>
                    <h2>Bienvenido</h2>
                    <p>Accede al sistema de denuncias ambientales</p>
                    <div class="security-badge">
                        <i class="bi bi-lock-fill"></i>
                        <span>Acceso Seguro</span>
                    </div>
                </div>

                <form action="../controladores/LoginControlador/LoginController.php" method="POST" class="login-form">
                    <!-- Campo de username/email mejorado -->
                    <div class="input-group">
                        <label class="input-label">Usuario o Correo</label>
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <input type="text" name="username" placeholder="tu_usuario@ecoreport.gob.ec" required class="form-input">
                            <div class="input-border"></div>
                            <div class="input-glow"></div>
                        </div>
                    </div>

                    <!-- Campo de contraseña mejorado -->
                    <div class="input-group">
                        <label class="input-label">Contraseña</label>
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <i class="bi bi-key-fill"></i>
                            </div>
                            <input type="password" name="password" placeholder="••••••••" required id="password" class="form-input">
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="bi bi-eye-fill" id="toggleIcon"></i>
                            </button>
                            <div class="input-border"></div>
                            <div class="input-glow"></div>
                        </div>
                        <div class="password-strength">
                            <div class="strength-indicator">
                                <div class="strength-bar"></div>
                            </div>
                        </div>
                    </div>

                                        <!-- Opciones del formulario elegantes -->
                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span class="checkmark">
                                <i class="bi bi-check-lg"></i>
                            </span>
                            <span class="label-text">Recordar sesión</span>
                        </label>
                        <a href="recuperar-password.php" class="forgot-password">
                            <i class="bi bi-key-fill"></i>
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <!-- Botón de login premium -->
                    <button type="submit" class="login-btn">
                        <div class="btn-background"></div>
                        <div class="btn-content">
                            <span class="btn-text">Acceder al Sistema</span>
                            <div class="btn-icon">
                                <i class="bi bi-arrow-right-circle-fill"></i>
                            </div>
                        </div>
                        <div class="btn-ripple"></div>
                    </button>

                    <!-- Separador elegante -->
                    <div class="divider">
                        <div class="divider-line"></div>
                        <span class="divider-text">Protegiendo el Ambiente</span>
                        <div class="divider-line"></div>
                    </div>

                    <!-- Enlaces adicionales para ciudadanos -->
                    <div class="citizen-access">
                        <div class="citizen-option">
                            <i class="bi bi-plus-circle-fill"></i>
                            <span>¿Eres ciudadano? <a href="#">Regístrate aquí</a></span>
                        </div>
                        <div class="citizen-option">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>Denuncia anónima <a href="#">sin registro</a></span>
                        </div>
                    </div>
                </form>

                <!-- Footer elegante -->
                <div class="login-footer">
                    <div class="footer-content">
                        <div class="security-info">
                            <i class="bi bi-shield-check"></i>
                            <span>Certificado SSL</span>
                        </div>
                        <div class="environmental-badge">
                            <i class="bi bi-leaf-fill"></i>
                            <span>Plataforma Carbono Neutral</span>
                        </div>
                        <p class="copyright">&copy; 2025 EcoReport. Cuidando el planeta juntos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript para mostrar/ocultar contraseña -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye-fill');
                toggleIcon.classList.add('bi-eye-slash-fill');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash-fill');
                toggleIcon.classList.add('bi-eye-fill');
            }
        }

        // Animación del logo principal
        document.addEventListener('DOMContentLoaded', function() {
            const logo = document.querySelector('.brand-logo i');
            setInterval(() => {
                logo.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    logo.style.transform = 'scale(1)';
                }, 300);
            }, 3000);
        });
    </script>

    <!-- Alertas con tema ambiental -->
    <?php if (!empty($error)): ?>
    <script>
        Swal.fire({
            title: "Acceso Denegado",
            text: "<?php echo $error; ?>",
            icon: "error",
            confirmButtonColor: "#ef4444",
            confirmButtonText: "Intentar nuevamente",
            background: '#f8fafc',
            backdrop: `rgba(22, 163, 74, 0.1)`
        });
    </script>
    <?php endif; ?>

    <?php if (!empty($alerta)): ?>
    <script>
        Swal.fire({
            title: "<?php echo $alerta['titulo']; ?>",
            text: "<?php echo $alerta['mensaje']; ?>",
            icon: "<?php echo $alerta['icono']; ?>",
            confirmButtonColor: "#16a34a",
            confirmButtonText: "Continuar",
            background: '#f8fafc',
            backdrop: `rgba(22, 163, 74, 0.1)`
        });
    </script>
    <?php endif; ?>
</body>
</html>