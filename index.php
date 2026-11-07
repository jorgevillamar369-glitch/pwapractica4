<?php
require __DIR__ . '/includes/funciones.php';
require __DIR__ . '/config/db.php';

try {
    $pdo = conexion();
    $productos = $pdo->query(
        'SELECT p.id, p.nombre, p.descripcion, p.precio, p.categoria, p.imagen, p.stock,
                v.nombre AS vendedor, v.ciudad
           FROM productos p
           INNER JOIN vendedores v ON v.id = p.id_vendedor
          ORDER BY p.categoria, p.id'
    )->fetchAll();
    $errorConexion = null;
} catch (Throwable $ex) {
    $productos = [];
    $errorConexion = 'No se pudo conectar con la base de datos. Revisa config/db.php.';
}

$categorias = [];
foreach ($productos as $producto) {
    $clave = $producto['categoria'];
    if(!isset($categorias[$clave])){
      $categorias[$clave] = ['nombre' => nombre_categoria($clave), 'total' => 0];
    }
    $categorias[$clave]["total"]++;
} 


$titulo = 'Molino 32 — café de especialidad y equipo de preparación';
require __DIR__ . '/includes/header.php';
?>

<main>
  <!-- Portada -->
  <section class="portada">
    <div class="contenedor portada__fila">
      <div class="portada__texto">
        <p class="rotulo">Tres talleres · una sola despensa</p>
        <h1 class="portada__titulo">Café tostado la semana pasada, equipo para prepararlo bien.</h1>
        <p class="portada__entrada">
          Compramos directo a Tostaduría Altura, Casa del Grano y Taller de Gres.
          Cada bolsa lleva la fecha de tueste y el nombre de quien la preparó.
        </p>
        <div class="portada__acciones">
          <a class="boton boton--lleno" href="#catalogo">Ver catálogo</a>
          <a class="enlace-flecha" href="#como-comprar">Cómo funciona el pedido</a>
        </div>
      </div>

      <figure class="portada__figura">
        <img src="img/hero.jpg" alt="Barra de café con máquina de espresso y vitrina de repostería" width="1100" height="1650">
        <figcaption>Barra de Tostaduría Altura, Quito. Tueste de los martes.</figcaption>
      </figure>
    </div>
  </section>

  <!-- Catálogo -->
  <section class="catalogo" id="catalogo">
    <div class="contenedor">
      <div class="catalogo__cabeza">
        <div>
          <p class="rotulo">Catálogo</p>
          <h2 class="titulo-seccion">Ocho cosas que usamos todos los días</h2>
        </div>

        <div class="filtros" role="group" aria-label="Filtrar por categoría">
          <button class="filtro is-activo" type="button" data-filtro="todos">Todo</button>
          <?php foreach ($categorias as $clave => $etiqueta): ?>
            <button class="filtro" type="button" data-filtro="<?= e($clave) ?>"><?= e($etiqueta['nombre']) ?><span><?=(int)$etiqueta['total'] ?> </span></button>
          <?php endforeach; ?>
        </div>
      </div>

      <?php if ($errorConexion !== null): ?>
        <p class="aviso-error"><?= e($errorConexion) ?></p>
      <?php elseif ($productos === []): ?>
        <p class="aviso-error">Todavía no hay productos cargados.</p>
      <?php endif; ?>

      <ul class="grilla" data-grilla>
        <?php foreach ($productos as $i => $producto): ?>
          <li class="tarjeta" data-categoria="<?= e($producto['categoria']) ?>">
            <figure class="tarjeta__figura">
              <img src="img/<?= e($producto['imagen']) ?>" alt="<?= e($producto['nombre']) ?>" loading="lazy" width="600" height="450">
              <span class="tarjeta__numero"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
            </figure>

            <div class="tarjeta__cuerpo">
              <p class="tarjeta__vendedor"><?= e($producto['vendedor']) ?> · <?= e($producto['ciudad']) ?></p>
              <h3 class="tarjeta__nombre"><?= e($producto['nombre']) ?></h3>
              <p class="tarjeta__descripcion"><?= e($producto['descripcion']) ?></p>

              <div class="tarjeta__pie">
                <span class="tarjeta__precio"><?= precio((float) $producto['precio']) ?></span>
                <button
                  class="boton boton--linea"
                  type="button"
                  data-agregar
                  data-id="<?= (int) $producto['id'] ?>"
                  data-nombre="<?= e($producto['nombre']) ?>"
                  data-precio="<?= (float) $producto['precio'] ?>"
                  data-imagen="img/<?= e($producto['imagen']) ?>"
                  <?= $producto['stock'] < 1 ? 'disabled' : '' ?>
                ><?= $producto['stock'] < 1 ? 'Agotado' : 'Agregar' ?></button>
              </div>

              <p class="tarjeta__stock"><?= (int) $producto['stock'] ?> disponibles</p>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>

      <p class="grilla__vacio" data-grilla-vacio hidden>No hay productos en esta categoría.</p>
    </div>
  </section>

  <!-- Cómo comprar -->
  <section class="como" id="como-comprar">
    <div class="contenedor como__fila">
      <div class="como__intro">
        <p class="rotulo">Cómo comprar</p>
        <h2 class="titulo-seccion">Eliges, dejas tus datos y el taller despacha.</h2>
        <p class="como__texto">
          No pedimos tarjeta. Confirmas el pedido, recibes un código y el vendedor
          te escribe para acordar el pago y la entrega en 24 horas.
        </p>
      </div>

      <ol class="pasos">
        <li class="paso">
          <span class="paso__numero">01</span>
          <h3>Agrega al carrito</h3>
          <p>Puedes mezclar café, equipo y cerámica. El stock se descuenta al confirmar.</p>
        </li>
        <li class="paso">
          <span class="paso__numero">02</span>
          <h3>Deja nombre, correo y dirección</h3>
          <p>Usamos esos datos solo para coordinar la entrega con el taller.</p>
        </li>
        <li class="paso">
          <span class="paso__numero">03</span>
          <h3>Sigue tu pedido</h3>
          <p>Con el código M32 puedes consultar el estado cuando quieras.</p>
        </li>
      </ol>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
