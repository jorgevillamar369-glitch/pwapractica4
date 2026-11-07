<?php
/**
 * Conexión a MySQL.
 *
 * LOCAL (XAMPP):  host 127.0.0.1, puerto 3307, usuario root, sin clave.
 * HOSTING (InfinityFree): cambia las cuatro constantes por los datos
 * que aparecen en el panel -> MySQL Databases.
 */

define('DB_HOST', '127.0.0.1');
define('DB_PUERTO', '3307');
define('DB_NOMBRE', 'pwapractica4');
define('DB_USUARIO', 'root');
define('DB_CLAVE', '');

function conexion(): PDO
{
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PUERTO
         . ';dbname=' . DB_NOMBRE . ';charset=utf8mb4';

    return new PDO($dsn, DB_USUARIO, DB_CLAVE, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}
