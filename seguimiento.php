<?php
require __DIR__ . '/includes/funciones.php';
require __DIR__ . '/config/db.php';

$codigo  = strtoupper(trim((string) ($_GET['codigo'] ?? '')));
$pedido  = null;
$lineas  = [];
$mensaje = null;

if ($codigo !== '') {
    try {
        $pdo = conexion();

        $consulta = $pdo->prepare(
            'SELECT p.codigo, p.total, p.estado, p.creado_en,
                    c.nombre AS cliente, c.email, c.direccion
               FROM pedidos p
               INNER JOIN clientes c ON c.id = p.id_cliente
              WHERE p.codigo = ?'
        );
        $consulta->execute([$codigo]);
        $pedido = $consulta->fetch();

        if ($pedido === false) {
            $mensaje = 'No encontramos un pedido con ese código.';
        } else {
            $consulta = $pdo->prepare(
                'SELECT d.cantidad, d.precio_unitario, pr.nombre, pr.imagen, v.nombre AS vendedor
                   FROM pedido_detalle d
                   INNER JOIN productos pr  ON pr.id = d.id_producto
                   INNER JOIN vendedores v  ON v.id = pr.id_vendedor
                  WHERE d.id_pedido = (SELECT id FROM pedidos WHERE codigo = ?)'
            );
            $consulta->execute([$codigo]);
            $lineas = $consulta->fetchAll();
        }
    } catch (Throwable $ex) {
        $mensaje = 'No se pudo consultar el pedido. Revisa la conexión a la base de datos.';
    }
}

$titulo = 'Seguir pedido — Molino 32';
require __DIR__ . '/includes/header.php';
?>

<main class="pagina-seguimiento">
  <div class="contenedor">
    <p class="rotulo">Seguimiento</p>
    <h1 class="titulo-seccion">Consulta tu pedido con el código M32</h1>
    <p class="seguimiento__texto">
      El código aparece al confirmar la compra y tiene el formato <strong>M32-XXXX</strong>.
    </p>

    <form class="buscador" method="get" action="seguimiento.php">
      <label for="codigo">Código del pedido</label>
      <div class="buscador__fila">
        <input id="codigo" name="codigo" type="text" value="<?= e($codigo) ?>" placeholder="M32-7F3A" maxlength="8" required>
        <button class="boton boton--lleno" type="submit">Buscar</button>
      </div>
    </form>

    <?php if ($mensaje !== null): ?>
      <p class="aviso-error"><?= e($mensaje) ?></p>
    <?php endif; ?>

    <?php if ($pedido !== null): ?>
      <section class="ficha">
        <header class="ficha__cabeza">
          <div>
            <p class="rotulo">Pedido <?= e($pedido['codigo']) ?></p>
            <h2 class="ficha__total"><?= precio((float) $pedido['total']) ?></h2>
          </div>
          <p class="ficha__estado ficha__estado--<?= e($pedido['estado']) ?>"><?= e(ucfirst($pedido['estado'])) ?></p>
        </header>

        <dl class="ficha__datos">
          <div>
            <dt>Cliente</dt>
            <dd><?= e($pedido['cliente']) ?></dd>
          </div>
          <div>
            <dt>Correo</dt>
            <dd><?= e($pedido['email']) ?></dd>
          </div>
          <div>
            <dt>Entrega</dt>
            <dd><?= e($pedido['direccion']) ?></dd>
          </div>
          <div>
            <dt>Fecha</dt>
            <dd><?= e(date('d/m/Y H:i', strtotime($pedido['creado_en']))) ?></dd>
          </div>
        </dl>

        <ul class="ficha__lineas">
          <?php foreach ($lineas as $linea): ?>
            <li class="linea">
              <img src="img/<?= e($linea['imagen']) ?>" alt="<?= e($linea['nombre']) ?>" width="72" height="54" loading="lazy">
              <div class="linea__texto">
                <p class="linea__nombre"><?= e($linea['nombre']) ?></p>
                <p class="linea__vendedor"><?= e($linea['vendedor']) ?></p>
              </div>
              <p class="linea__cantidad"><?= (int) $linea['cantidad'] ?> × <?= precio((float) $linea['precio_unitario']) ?></p>
              <p class="linea__subtotal"><?= precio((float) $linea['precio_unitario'] * (int) $linea['cantidad']) ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>
    <?php endif; ?>
  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
