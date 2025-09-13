<?php
// Conexión a la base de datos
$server = "localhost";
$user   = "root";
$passdb = "";
$db     = "registroj";

$conexion = new mysqli($server, $user, $passdb, $db);

// Verificar conexión
if ($conexion->connect_errno) {
    die("Conexión fallida: " . $conexion->connect_errno);
}

// Procesar formulario
$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email      = isset($_POST['register_email']) ? trim($_POST['register_email']) : "";
    $password   = isset($_POST['register_password']) ? $_POST['register_password'] : "";
    $confirmado = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : "";

    // Validaciones básicas
    if (empty($email) || empty($password) || empty($confirmado)) {
        $mensaje = "Por favor completa todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Ingrese un correo electrónico válido.";
    } elseif ($password !== $confirmado) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        // Hash de la contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Insertar con prepared statement
        $stmt = $conexion->prepare("INSERT INTO usuarios (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hash);

        if ($stmt->execute()) {
            // REDIRECCIÓN A home.html DESPUÉS DEL REGISTRO EXITOSO
            header("Location: home.html");
            exit(); // Es crucial llamar a exit() después de la redirección
        } else {
            $mensaje = "Error al registrar: " . $conexion->error;
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Crear Cuenta</h1>
            <p class="text-gray-600">Regístrate con tu email</p>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class="mb-4 p-3 rounded bg-yellow-100 text-yellow-800"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <form class="space-y-6" name="registro" method="post" action="">
            <div>
                <label for="register-email" class="block text-sm font-medium text-gray-700 mb-2">Correo electrónico</label>
                <input 
                    type="email" 
                    name="register_email"
                    id="register-email" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    placeholder="tu@email.com"
                    required
                >
            </div>
            
            <div>
                <label for="register-password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                <input 
                    type="password" 
                    name="register_password"
                    id="register-password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    placeholder="••••••••"
                    required
                >
            </div>
            
            <div>
                <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-2">Confirmar contraseña</label>
                <input 
                    type="password" 
                    name="confirm_password"
                    id="confirm-password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    placeholder="••••••••"
                    required
                >
            </div>
            
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="terms"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    required
                >
                <label for="terms" class="ml-2 block text-sm text-gray-700">
                    Acepto los <a href="#" class="text-blue-600 hover:text-blue-500">términos y condiciones</a>
                </label>
            </div>
            
            <button type="submit"
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-semibold" 
                >
                Crear Cuenta
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                ¿Ya tienes una cuenta? 
                <a href="index.html" class="text-blue-600 hover:text-blue-500 font-semibold">Inicia sesión</a>
            </p>
        </div>
    </div>
</body>
</html>