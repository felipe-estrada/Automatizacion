<?php
session_start(); // Iniciar sesión al principio

// Configuración de la conexión a la base de datos
$servername = "db";
$username = "myuser";
$password = "mypassword";
$dbname = "mydatabase";

// Incluir PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Asegúrate de tener instalado PHPMailer vía composer

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
            // Inicio de sesión exitoso, ahora generamos el código MFA
            $_SESSION['email'] = $email; // Guardar el correo en la sesión

            // Generar código MFA
            $mfa_code = random_int(100000, 999999);

            // Guardar el código en la base de datos (o en la sesión si prefieres)
            $stmt = $conn->prepare("UPDATE usuarios SET mfa_code = ? WHERE email = ?");
            $stmt->bind_param("is", $mfa_code, $email);
            $stmt->execute();

            // Enviar el código por correo electrónico usando PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Configuración del servidor de correos (SMTP)
                $mail->isSMTP();
                $mail->Host = 'smtp.tu-dominio.com'; // Cambia esto por el servidor SMTP de tu proveedor
                $mail->SMTPAuth = true;
                $mail->Username = 'tu-email@tu-dominio.com'; // Cambia esto por tu correo
                $mail->Password = 'tu-contraseña'; // Cambia esto por tu contraseña
                $mail->SMTPSecure = 'tls'; 
                $mail->Port = 587; // El puerto SMTP

                // Configuración del email
                $mail->setFrom('no-reply@tu-dominio.com', 'Tu Aplicación');
                $mail->addAddress($email); // Dirección del usuario

                // Contenido del correo
                $mail->isHTML(true); 
                $mail->Subject = 'Tu código de autenticación';
                $mail->Body    = "Tu código de autenticación es: <b>$mfa_code</b>";

                $mail->send();
                // Redirigir al formulario para ingresar el código MFA
                header("Location: /App/Login/MFA.php");
                exit();

            } catch (Exception $e) {
                echo "No se pudo enviar el código de autenticación. Error: {$mail->ErrorInfo}";
            }

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
