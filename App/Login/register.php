<?php
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
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validar que las contraseñas coinciden
    if ($password !== $confirm_password) {
        echo "Las contraseñas no coinciden.";
        exit;
    }

    // Verificar si el correo electrónico ya está registrado utilizando sentencias preparadas
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "El correo electrónico ya está registrado.";
    } else {
        // Cifrar la contraseña antes de insertarla en la base de datos
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insertar el nuevo usuario en la base de datos usando sentencias preparadas
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $email, $password_hash);

        if ($stmt->execute()) {
            // Redirigir al login
            header("Location: /Login/Login.html");
            exit();
        } else {
            echo "Error al registrar el usuario: " . $conn->error;
        }
    }

    // Cerrar el statement
    $stmt->close();
}

// Cerrar la conexión
$conn->close();
?>
