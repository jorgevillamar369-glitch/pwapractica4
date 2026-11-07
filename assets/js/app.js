/* ============================================================
   Molino 32 — carrito, filtros y confirmación del pedido
   JavaScript sin librerías.
   ============================================================ */

const CLAVE_CARRITO = 'molino32-carrito';

const carrito = document.querySelector('#carrito');
const velo = document.querySelector('[data-velo]');
const lista = document.querySelector('[data-lista-carrito]');
const vacio = document.querySelector('[data-carrito-vacio]');
const totalCarrito = document.querySelector('[data-total-carrito]');
const cuenta = document.querySelector('[data-cuenta-carrito]');
const formCompra = document.querySelector('[data-form-compra]');
const avisoCompra = document.querySelector('[data-aviso-compra]');
const botonComprar = document.querySelector('[data-confirmar-compra]');
const grilla = document.querySelector('[data-grilla]');
const grillaVacio = document.querySelector('[data-grilla-vacio]');

let items = leerCarrito();

/* ---------- Guardado ---------- */

function leerCarrito() {
  try {
    const guardado = JSON.parse(localStorage.getItem(CLAVE_CARRITO) || '[]');
    return Array.isArray(guardado) ? guardado : [];
  } catch (error) {
    return [];
  }
}

function guardarCarrito() {
  localStorage.setItem(CLAVE_CARRITO, JSON.stringify(items));
}

function formatear(valor) {
  return '$' + valor.toFixed(2);
}

function total() {
  return items.reduce((suma, item) => suma + item.precio * item.cantidad, 0);
}

/* ---------- Pintar ---------- */

function pintarCarrito() {
  lista.innerHTML = '';

  items.forEach((item) => {
    const fila = document.createElement('li');
    fila.className = 'carrito__item';
    fila.innerHTML = `
      <img src="${item.imagen}" alt="" width="64" height="48">
      <div>
        <p class="carrito__item-nombre">${item.nombre}</p>
        <p class="carrito__item-precio">${formatear(item.precio)} c/u</p>
        <div class="cantidad">
          <button type="button" data-menos="${item.id}" aria-label="Quitar una unidad de ${item.nombre}">−</button>
          <span>${item.cantidad}</span>
          <button type="button" data-mas="${item.id}" aria-label="Agregar una unidad de ${item.nombre}">+</button>
        </div>
      </div>
      <div class="carrito__item-derecha">
        <p class="carrito__item-subtotal">${formatear(item.precio * item.cantidad)}</p>
      </div>
    `;
    lista.appendChild(fila);
  });

  vacio.hidden = items.length > 0;
  totalCarrito.textContent = formatear(total());

  const unidades = items.reduce((suma, item) => suma + item.cantidad, 0);
  cuenta.textContent = unidades;
  cuenta.hidden = unidades === 0;

  guardarCarrito();
}

/* ---------- Carrito ---------- */

function agregar(producto) {
  const existente = items.find((item) => item.id === producto.id);

  if (existente) {
    existente.cantidad += 1;
  } else {
    items.push({ ...producto, cantidad: 1 });
  }

  pintarCarrito();
  abrirCarrito();
}

function cambiarCantidad(id, delta) {
  const item = items.find((fila) => fila.id === id);
  if (!item) return;

  item.cantidad += delta;

  if (item.cantidad < 1) {
    items = items.filter((fila) => fila.id !== id);
  }

  pintarCarrito();
}

function abrirCarrito() {
  carrito.classList.add('is-abierto');
  carrito.setAttribute('aria-hidden', 'false');
  velo.hidden = false;
  requestAnimationFrame(() => velo.classList.add('is-visible'));
  document.body.style.overflow = 'hidden';
  document.querySelector('[data-cerrar-carrito]').focus();
}

function cerrarCarrito() {
  carrito.classList.remove('is-abierto');
  carrito.setAttribute('aria-hidden', 'true');
  velo.classList.remove('is-visible');
  document.body.style.overflow = '';

  setTimeout(() => {
    if (!carrito.classList.contains('is-abierto')) velo.hidden = true;
  }, 240);
}

/* ---------- Confirmar pedido ---------- */

async function confirmarPedido(evento) {
  evento.preventDefault();
  avisoCompra.className = 'form-compra__aviso';

  if (items.length === 0) {
    avisoCompra.textContent = 'Agrega al menos un producto.';
    avisoCompra.classList.add('is-error');
    return;
  }

  if (!formCompra.checkValidity()) {
    formCompra.reportValidity();
    return;
  }

  const datos = {
    nombre: formCompra.nombre.value.trim(),
    email: formCompra.email.value.trim(),
    direccion: formCompra.direccion.value.trim(),
    items: items.map((item) => ({ id: item.id, cantidad: item.cantidad })),
  };

  botonComprar.disabled = true;
  botonComprar.textContent = 'Guardando…';

  try {
    const respuesta = await fetch('api/pedido.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(datos),
    });

    const resultado = await respuesta.json();

    if (!respuesta.ok || !resultado.ok) {
      throw new Error(resultado.mensaje || 'No se pudo guardar el pedido.');
    }

    items = [];
    pintarCarrito();
    formCompra.reset();

    avisoCompra.className = 'form-compra__aviso is-ok';
    avisoCompra.innerHTML = `Pedido <strong>${resultado.codigo}</strong> guardado por ${resultado.total}.
      <a href="seguimiento.php?codigo=${resultado.codigo}">Seguir el pedido</a>.`;
  } catch (error) {
    avisoCompra.className = 'form-compra__aviso is-error';
    avisoCompra.textContent = error.message;
  } finally {
    botonComprar.disabled = false;
    botonComprar.textContent = 'Confirmar pedido';
  }
}

/* ---------- Filtros ---------- */

function filtrar(categoria, boton) {
  document.querySelectorAll('.filtro').forEach((filtro) => {
    filtro.classList.toggle('is-activo', filtro === boton);
  });

  let visibles = 0;

  grilla.querySelectorAll('.tarjeta').forEach((tarjeta) => {
    const coincide = categoria === 'todos' || tarjeta.dataset.categoria === categoria;
    tarjeta.hidden = !coincide;
    if (coincide) visibles += 1;
  });

  grillaVacio.hidden = visibles > 0;
}

/* ---------- Eventos ---------- */

document.addEventListener('click', (evento) => {
  const agregarBoton = evento.target.closest('[data-agregar]');
  if (agregarBoton) {
    agregar({
      id: Number(agregarBoton.dataset.id),
      nombre: agregarBoton.dataset.nombre,
      precio: Number(agregarBoton.dataset.precio),
      imagen: agregarBoton.dataset.imagen,
    });
    return;
  }

  if (evento.target.closest('[data-abrir-carrito]')) {
    abrirCarrito();
    return;
  }

  if (evento.target.closest('[data-cerrar-carrito]') || evento.target === velo) {
    cerrarCarrito();
    return;
  }

  const menos = evento.target.closest('[data-menos]');
  if (menos) {
    cambiarCantidad(Number(menos.dataset.menos), -1);
    return;
  }

  const mas = evento.target.closest('[data-mas]');
  if (mas) {
    cambiarCantidad(Number(mas.dataset.mas), 1);
    return;
  }

  const filtro = evento.target.closest('.filtro');
  if (filtro) {
    filtrar(filtro.dataset.filtro, filtro);
  }
});

document.addEventListener('keydown', (evento) => {
  if (evento.key === 'Escape' && carrito.classList.contains('is-abierto')) {
    cerrarCarrito();
  }
});

formCompra.addEventListener('submit', confirmarPedido);

pintarCarrito();
