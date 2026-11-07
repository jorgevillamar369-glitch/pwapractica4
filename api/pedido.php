<?php
/**
 * Recibe el carrito desde el navegador (fetch + JSON) y guarda el pedido.
 * Responde siempre en JSON.
 */

require __DIR__ . '/../includes/funciones.php';
require __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido.']);
    exit;
}

$cuerpo = file_get_contents('php://input');
$datos  = json_decode($cuerpo, true);

if (!is_array($datos)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'mensaje' => 'No se recibieron datos válidos.']);
    exit;
}

$nombre    = trim((string) ($datos['nombre'] ?? ''));
$email     = trim((string) ($datos['email'] ?? ''));
$direccion = trim((string) ($datos['direccion'] ?? ''));
$items     = $datos['items'] ?? [];

if (largo($nombre) < 3) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensaje' => 'Escribe tu nombre completo.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensaje' => 'El correo no tiene un formato válido.']);
    exit;
}

if (largo($direccion) < 6) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensaje' => 'Falta la dirección de entrega.']);
    exit;
}

if (!is_array($items) || $items === []) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensaje' => 'El carrito está vacío.']);
    exit;
}

try {
    $pdo = conexion();
    $pdo->beginTransaction();

    $total   = 0.0;
    $detalle = [];

    foreach ($items as $item) {
        $id       = (int) ($item['id'] ?? 0);
        $cantidad = (int) ($item['cantidad'] ?? 0);

        if ($id < 1 || $cantidad < 1) {
            throw new RuntimeException('Hay un producto con cantidad inválida.');
        }

        $consulta = $pdo->prepare('SELECT nombre, precio, stock FROM productos WHERE id = ? FOR UPDATE');
        $consulta->execute([$id]);
        $producto = $consulta->fetch();

        if ($producto === false) {
            throw new RuntimeException('Uno de los productos ya no existe.');
        }

        if ((int) $producto['stock'] < $cantidad) {
            throw new RuntimeException('No hay stock suficiente de ' . $producto['nombre'] . '.');
        }

        $subtotal = (float) $producto['precio'] * $cantidad;
        $total   += $subtotal;

        $detalle[] = [
            'id'       => $id,
            'cantidad' => $cantidad,
            'precio'   => (float) $producto['precio'],
        ];
    }

    // Cliente: se crea o se actualiza si ya compró antes con el mismo correo.
    $pdo->prepare(
        'INSERT INTO clientes (nombre, email, direccion) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), direccion = VALUES(direccion)'
    )->execute([$nombre, $email, $direccion]);

    $consulta = $pdo->prepare('SELECT id FROM clientes WHERE email = ?');
    $consulta->execute([$email]);
    $idCliente = (int) $consulta->fetchColumn();

    // Código único de seguimiento.
    $codigo = codigo_pedido();
    for ($intento = 0; $intento < 5; $intento++) {
        $consulta = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE codigo = ?');
        $consulta->execute([$codigo]);

        if ((int) $consulta->fetchColumn() === 0) {
            break;
        }

        $codigo = codigo_pedido();
    }

    $pdo->prepare('INSERT INTO pedidos (codigo, id_cliente, total) VALUES (?, ?, ?)')
        ->execute([$codigo, $idCliente, $total]);

    $idPedido = (int) $pdo->lastInsertId();

    $insertarDetalle = $pdo->prepare(
        'INSERT INTO pedido_detalle (id_pedido, id_producto, cantidad, precio_unitario)
         VALUES (?, ?, ?, ?)'
    );
    $descontarStock = $pdo->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?');

    foreach ($detalle as $linea) {
        $insertarDetalle->execute([$idPedido, $linea['id'], $linea['cantidad'], $linea['precio']]);
        $descontarStock->execute([$linea['cantidad'], $linea['id']]);
    }

    $pdo->commit();

    echo json_encode([
        'ok'     => true,
        'codigo' => $codigo,
        'total'  => precio($total),
    ]);
} catch (RuntimeException $ex) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(409);
    echo json_encode(['ok' => false, 'mensaje' => $ex->getMessage()]);
} catch (Throwable $ex) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode(['ok' => false, 'mensaje' => 'No se pudo guardar el pedido. Intenta otra vez.']);
}
