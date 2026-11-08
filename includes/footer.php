<?php /** Pie común + carrito lateral. */ ?>

<footer class="pie" id="pie">
  <div class="contenedor pie__fila">
    <div class="pie__bloque">
      <p class="pie__marca">Molino 32</p>
      <p class="pie__texto">
        Tienda de café de especialidad y equipo de preparación.
        Trabajamos con tres talleres ecuatorianos; cada producto sale del taller que lo hace.
      </p>
    </div>

    <div class="pie__bloque">
      <p class="pie__rotulo">Vendedores</p>
      <ul class="pie__lista">
        <li>Tostaduría Altura — Quito</li>
        <li>Casa del Grano — Cuenca</li>
        <li>Taller de Gres — Loja</li>
      </ul>
    </div>

    <div class="pie__bloque">
      <p class="pie__rotulo">Contacto</p>
      <ul class="pie__lista">
        <li><a href="mailto:hola@molino32.ec">hola@molino32.ec</a></li>
        <li>Lunes a sábado, 09:00 a 18:00</li>
        <li><a href="seguimiento.php">Seguir un pedido</a></li>
      </ul>
    </div>
  </div>

  <div class="contenedor pie__legal">
    <span>Práctica 4 — Programación Web Avanzada · Grupo 04</span>
    <span>Fotografías: Pexels</span>
  </div>
</footer>

<!-- Carrito lateral -->
<div class="velo" data-velo hidden></div>

<aside class="carrito" id="carrito" aria-label="Carrito de compras" aria-hidden="true">
  <div class="carrito__cabeza">
    <h2 class="carrito__titulo">Tu carrito</h2>
    <button class="carrito__cerrar" type="button" data-cerrar-carrito aria-label="Cerrar carrito">Cerrar</button>
  </div>

  <div class="carrito__cuerpo">
    <ul class="carrito__lista" data-lista-carrito></ul>
    <p class="carrito__vacio" data-carrito-vacio>El carrito está vacío. Agrega algo del catálogo.</p>
  </div>
  
  <div class="carrito__pie">
    <button class="carrito__vaciar" type="button" data-vaciar-carrito>Vaciar carrito</button>
    <div class="carrito__total">
      <span>Total</span>
      <strong data-total-carrito>$0.00</strong>
    
    </div>

    <form class="form-compra" data-form-compra novalidate>
      <label for="cliente-nombre">Nombre completo</label>
      <input id="cliente-nombre" name="nombre" type="text" autocomplete="name" required minlength="3">

      <label for="cliente-email">Correo electrónico</label>
      <input id="cliente-email" name="email" type="email" autocomplete="email" required>

      <label for="cliente-direccion">Dirección de entrega</label>
      <input id="cliente-direccion" name="direccion" type="text" autocomplete="street-address" required minlength="6">

      <button class="boton boton--lleno" type="submit" data-confirmar-compra>Confirmar pedido</button>
      <p class="form-compra__aviso" data-aviso-compra role="status" aria-live="polite"></p>
    </form>
  </div>
</aside>

<script src="assets/js/app.js" defer></script>
</body>
</html>
