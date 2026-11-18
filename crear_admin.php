<?php

// Ejecutar únicamente desde la terminal
// php crear_admin.php "Nombre Apellido" "correo@ejemplo.com"

// Verificar que el archivo se ejecute desde la terminal
if (PHP_SAPI !== 'cli') {

    http_response_code(403);

    exit('Solo disponible en terminal.');
}

// Validar los argumentos recibidos
if (
    $argc !== 3
    || !filter_var($argv[2], FILTER_VALIDATE_EMAIL)
    || !preg_match('/^[\p{L} ]{2,100}$/u', $argv[1])
) {

    exit(
        "Uso: php crear_admin.php \"Nombre Apellido\" correo@ejemplo.com\n"
    );
}

// Cargar la conexión a la base de datos
require __DIR__ . '/app/config/database.php';

// Solicitar contraseña del administrador
echo "Contraseña (mínimo 10 caracteres; visible al escribir): ";

// Leer la contraseña ingresada
$pass = trim((string) fgets(STDIN));

// Validar la longitud de la contraseña
if (strlen($pass) < 10) {

    exit("Contraseña demasiado corta.\n");
}

// Preparar la consulta para registrar al administrador
$st = Database::connection()->prepare(
    'INSERT INTO usuarios_admin
    (nombre, correo, password_hash)
    VALUES (?, ?, ?)'
);

// Guardar el administrador en MySQL
$st->execute([
    $argv[1],
    $argv[2],
    password_hash($pass, PASSWORD_DEFAULT)
]);

// Confirmar la creación del administrador
echo "Administrador creado correctamente.\n";
