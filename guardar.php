<?php
/**
 * La Libretita — Autodiagnóstico público
 * Vive en /herramientas/guardar.php y guarda en su PROPIA tabla
 * (libretita_autodiagnostico), separada de la de Emprendo Norte (respuestas).
 * Usa la misma conexión que el resto del sitio: ../config.php
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

require __DIR__ . '/../config.php';

$datos = json_decode(file_get_contents('php://input'), true);
if (!$datos || empty($datos['nombre'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Faltan datos (nombre obligatorio).']);
    exit;
}

$nombre = trim(mb_substr($datos['nombre'], 0, 200));
$email  = isset($datos['email']) ? trim(mb_substr((string)$datos['email'], 0, 200)) : '';
$email  = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
$fecha  = isset($datos['fecha']) ? date('Y-m-d H:i:s', strtotime($datos['fecha'])) : date('Y-m-d H:i:s');
$perfil = isset($datos['perfil']) ? mb_substr($datos['perfil'], 0, 120) : '';

$niveles = isset($datos['niveles']) && is_array($datos['niveles']) ? $datos['niveles'] : [];
$n = function ($k) use ($niveles) { return isset($niveles[$k]) ? floatval($niveles[$k]) : 0; };
$lista = function ($k) use ($datos) {
    return isset($datos[$k]) && is_array($datos[$k]) ? implode("\n", array_map('strval', $datos[$k])) : '';
};

$pdo = conectarDB();
$stmt = $pdo->prepare("
    INSERT INTO libretita_autodiagnostico
        (nombre, email, fecha, perfil,
         nivel_organizacion, nivel_administracion, nivel_presupuestos,
         nivel_equipo, nivel_ventas, nivel_comunicacion, nivel_tecnologia,
         comunicacion_items, tecnologia_items, ip)
    VALUES
        (:nombre, :email, :fecha, :perfil,
         :ope, :admin, :pres, :equi, :com, :marca, :tec,
         :com_items, :tec_items, :ip)
");

try {
    $stmt->execute([
        ':nombre' => $nombre, ':email' => $email, ':fecha' => $fecha, ':perfil' => $perfil,
        ':ope' => $n('ope'), ':admin' => $n('admin'), ':pres' => $n('pres'),
        ':equi' => $n('equi'), ':com' => $n('com'), ':marca' => $n('marca'), ':tec' => $n('tec'),
        ':com_items' => $lista('comunicacion_items'), ':tec_items' => $lista('tecnologia_items'),
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
    echo json_encode(['ok' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la respuesta.']);
}
