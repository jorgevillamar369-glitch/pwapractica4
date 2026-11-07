<?php
/** Cabecera común. Se incluye desde index.php y seguimiento.php. */
$titulo = $titulo ?? 'Molino 32 — café de especialidad';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titulo) ?></title>
  <meta name="description" content="Café de especialidad, equipo de goteo y cerámica de tres talleres ecuatorianos. Compra directo al vendedor.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Karla:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

<a class="saltar" href="#catalogo">Ir al catálogo</a>

<header class="cabecera">
  <div class="contenedor cabecera__fila">
    <a class="marca" href="index.php">
      <span class="marca__nombre">Molino&nbsp;32</span>
      <span class="marca__bajada">café de especialidad</span>
    </a>

    <nav class="nav" aria-label="Navegación principal">
      <a href="index.php#catalogo">Catálogo</a>
      <a href="index.php#como-comprar">Cómo comprar</a>
      <a href="seguimiento.php">Seguir pedido</a>
    </nav>

    <button class="carrito-boton" type="button" data-abrir-carrito>
      Carrito
      <span class="carrito-boton__cuenta" data-cuenta-carrito>0</span>
    </button>
  </div>
</header>
