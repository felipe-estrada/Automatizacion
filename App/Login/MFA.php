<?php
session_start();

// Verificar si el usuario ya ha iniciado sesión y tiene el correo almacenado
if (!isset($_SESSION['email'])) {
    header("Location: /App/Login/login.php");
    exit();
}

// Configuración de la conexión a la base de datos
$servername = "db";
$username = "myuser";
$password = "mypassword";
$dbname = "mydatabase";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = $_POST['mfa_code'];
    $email = $_SESSION['email'];

    // Obtener el código MFA almacenado para este usuario
    $stmt = $conn->prepare("SELECT mfa_code FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($stored_code);
    $stmt->fetch();

    // Verificar si el código ingresado es correcto
    if ($entered_code == $stored_code) {
        // Código MFA correcto, autenticar al usuario
        $_SESSION['authenticated'] = true; // Marcar al usuario como autenticado
        header("Location: /App/HTML/Index.html"); // Redirigir a la página principal
        exit();
    } else {
        echo "Código de autenticación incorrecto.";
    }

    // Cerrar el statement
    $stmt->close();
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación MFA</title>
</head>
<body>
    <h1>Verificación de Autenticación Multifactor</h1>
    <form method="post" action="">
        <label for="mfa_code">Introduce el código que recibiste por correo:</label>
        <input type="text" id="mfa_code" name="mfa_code" required>
        <button type="submit">Verificar</button>
    </form>
</body>
</html>
