<?php
session_start(); // Iniciar sesión al principio

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
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Contraseña ingresada por el usuario

    // Consulta SQL para verificar el usuario
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_password_hash);
        $stmt->fetch();

        // Verificar la contraseña cifrada
        if (password_verify($password, $stored_password_hash)) {
            // Inicio de sesión exitoso
            $_SESSION['email'] = $email; // Guardar el correo en la sesión

            // Redirigir a la página principal
            header("Location: /App/HTML/Index.html");
            exit();
        } else {
            echo "Correo electrónico o contraseña incorrectos.";
        }
    } else {
        echo "Correo electrónico o contraseña incorrectos.";
    }

    // Cerrar el statement
    $stmt->close();
}

// Cerrar la conexión
$conn->close();
?>
