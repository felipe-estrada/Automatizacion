<?php
// Configuración de la conexión a la base de datos
$servername = "db";  // Cambiado a 'db', que es el nombre del servicio en docker-compose
$username = "myuser"; // Usuario de MySQL definido en docker-compose
$password = "mypassword"; // Contraseña de MySQL definida en docker-compose
$dbname = "mydatabase";  // Nombre de la base de datos definida en docker-compose

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validar que las contraseñas coinciden
    if ($password !== $confirm_password) {
        echo "Las contraseñas no coinciden.";
        exit;
    }

    // Verificar si el correo electrónico ya está registrado
    $sql = "SELECT * FROM usuarios WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "El correo electrónico ya está registrado.";
    } else {
        // Insertar el nuevo usuario en la base de datos sin cifrar la contraseña
        $sql = "INSERT INTO usuarios (usuario, email, password) VALUES ('$nombre', '$email', '$password')";

        if ($conn->query($sql) === TRUE) {
            // Redirigir al login
            header("Location: /University/Login/Login.html");
            exit();
        } else {
            echo "Error al registrar el usuario: " . $conn->error;
        }
    }
}

// Cerrar la conexión
$conn->close();
?>
