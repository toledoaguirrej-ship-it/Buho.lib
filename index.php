<?php
declare(strict_types=1);

session_start();

// Conexión y archivos principales
require __DIR__ . '/app/config/database.php';
require __DIR__ . '/app/models/Biblioteca.php';
require __DIR__ . '/app/controllers/BibliotecaController.php';

// Funciones auxiliares
function h(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function redirect(string $to): never {
    header('Location: ' . $to);
    exit;
}

function flash(string $kind, string $message): void {
    $_SESSION['flash'] = [
        'kind' => $kind,
        'message' => $message
    ];
}

function csrf_token(): string {
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrf_check(): void {
    if (!hash_equals(csrf_token(), $_POST['csrf'] ?? '')) {
        http_response_code(419);
        exit('La sesión expiró. Recarga la página e inténtalo de nuevo.');
    }
}

// Autenticación y autorización por rol
require __DIR__ . '/app/auth.php';
$pagina = $_GET['pagina'] ?? 'inicio';
if ($pagina === 'salir') {
    csrf_check();
    logout_user();
    redirect('?pagina=login');
}
if (in_array($pagina, ['login', 'registro'], true)) {
    auth_page($pagina);
    exit;
}
if (!current_user()) redirect('?pagina=login');
if ($_SERVER['REQUEST_METHOD'] === 'POST') csrf_check();
$controller = new BibliotecaController(new Biblioteca(Database::connection()));
$rutas = ['inicio'=>'inicio','catalogo'=>'catalogo','nuevo-libro'=>'nuevoLibro','lectores'=>'lectores','prestamos'=>'prestamos'];
if ($pagina === 'mis-prestamos') {
    if (current_user()['rol'] !== 'cliente') { http_response_code(403); exit('Acceso denegado'); }
    $stmt = Database::connection()->prepare('SELECT p.*, l.titulo FROM prestamos p JOIN libros l ON l.id=p.libro_id WHERE p.lector_id=? ORDER BY p.fecha_prestamo DESC');
    $stmt->execute([current_user()['lector_id']]);
    $misPrestamos = $stmt->fetchAll();
    require __DIR__.'/app/views/mis_prestamos.php';
    exit;
}
if (!isset($rutas[$pagina])) { http_response_code(404); exit('Página no encontrada'); }
if (current_user()['rol'] === 'cliente' && $pagina !== 'catalogo') {
    http_response_code(403); exit('No tienes permisos para esta sección. <a href="?pagina=catalogo">Volver al catálogo</a>');
}
$controller->{$rutas[$pagina]}();
