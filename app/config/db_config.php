<?php
// app/config/db_config.php

function connectDB() {
    $servername = "localhost";
    $username = "root";     // ¡Tu usuario de MySQL!
    $password = "";         // ¡Tu contraseña de MySQL!
    $dbname = "iluminara_db"; // El nombre de tu base de datos

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        return null;
    }

    // Establecer el conjunto de caracteres a UTF-8 (¡CRUCIAL para caracteres especiales!)
    $conn->set_charset("utf8mb4");

    return $conn;
}
?>

