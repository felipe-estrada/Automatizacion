<?php
session_start(); // Iniciar sesión al principio

// Configuración de la conexión a la base de datos
$servername = "db"; // Nombre del servicio de la base de datos en docker-compose
$username = "myuser"; // Usuario de MySQL
$password = "mypassword"; // Contraseña de MySQL
$dbname = "mydatabase"; // Nombre de la base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password']; // No aplicar cifrado aquí

    // Consulta SQL para verificar el usuario
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_password);
        $stmt->fetch();
        
        // Verificar la contraseña
        if ($password === $stored_password) {
            // Inicio de sesión exitoso
            $_SESSION['email'] = $email; // Guardar el correo en la sesión
            
            // Redirigir a la página principal
            header("Location: /University/HTML/Index.html"); // Cambia esto a la ruta de tu página principal
            exit();
        } else {
            echo "Correo electrónico o contraseña incorrectos.";
        }
    } else {
        echo "Correo electrónico o contraseña incorrectos.";
    }
}

// Cerrar la conexión
$conn->close();
?>
