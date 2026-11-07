# Molino 32 — Práctica 4

Tienda de café de especialidad, equipo de preparación y cerámica.
Práctica 4 de Programación Web Avanzada: HTML, CSS, JavaScript, PHP y MySQL.

## Qué incluye

- `index.php` — portada, catálogo con filtros, sección de cómo comprar.
- `seguimiento.php` — consulta de un pedido por su código `M32-XXXX`.
- `api/pedido.php` — recibe el carrito en JSON y guarda cliente, pedido y detalle.
- `config/db.php` — conexión PDO a MySQL.
- `includes/` — cabecera, pie, carrito lateral y funciones de apoyo.
- `assets/css/estilos.css` — estilos propios (sin frameworks).
- `assets/js/app.js` — carrito con `localStorage`, filtros y `fetch`.
- `sql/pwapractica4.sql` — tablas y datos de ejemplo.
- `img/` — fotografías de Pexels.

## Base de datos

Tablas: `vendedores`, `productos`, `clientes`, y como apoyo `pedidos` y `pedido_detalle`.

## Probar en local (XAMPP)

1. Arranca MySQL. Si el puerto 3306 está ocupado por otro servidor:

   ```
   C:\xampp\mysql\bin\mysqld.exe --port=3307 --datadir=C:/xampp/mysql/data --console
   ```

2. Crea la base e importa el SQL:

   ```
   C:\xampp\mysql\bin\mysql.exe -u root -P 3307 -e "CREATE DATABASE pwapractica4 CHARACTER SET utf8mb4"
   C:\xampp\mysql\bin\mysql.exe -u root -P 3307 pwapractica4 < sql/pwapractica4.sql
   ```

3. Sirve el proyecto:

   ```
   C:\xampp\php\php.exe -S 127.0.0.1:8099 -t .
   ```

4. Abre `http://127.0.0.1:8099`.

## Publicar en InfinityFree

1. En el panel, **MySQL Databases**: crea una base y anota
   *host*, *nombre*, *usuario* y *contraseña*.
2. Abre phpMyAdmin de esa base e importa `sql/pwapractica4.sql`.
3. Edita `config/db.php` con esos cuatro datos y el puerto `3306`.
4. Sube todo por FTP o con el *File Manager* a `htdocs/`.
5. Comprueba que `index.php` abre y que se puede confirmar un pedido.

## Nota sobre imágenes

Fotografías descargadas de Pexels (uso libre, sin atribución obligatoria).
Crédito discreto en el pie del sitio.
