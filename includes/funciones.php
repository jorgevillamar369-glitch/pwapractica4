<?php
/**
 * Funciones de apoyo que usan todas las páginas.
 */

/** Escapa texto antes de imprimirlo en HTML. */
function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

/** Formatea un precio con dos decimales y separador de miles. */
function precio(float $valor): string
{
    return '$' . number_format($valor, 2, '.', ',');
}

/** Largo de un texto, con o sin la extensión mbstring. */
function largo(string $texto): int
{
    return function_exists('mb_strlen') ? mb_strlen($texto) : strlen($texto);
}

/** Código corto y legible para seguir un pedido (ej. M32-7F3A). */
function codigo_pedido(): string
{
    $abecedario = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $codigo = '';

    for ($i = 0; $i < 4; $i++) {
        $codigo .= $abecedario[random_int(0, strlen($abecedario) - 1)];
    }

    return 'M32-' . $codigo;
}

/** Etiqueta legible de una categoría guardada en la base. */
function nombre_categoria(string $categoria): string
{
    $etiquetas = [
        'cafe'     => 'Café',
        'equipo'   => 'Equipo',
        'ceramica' => 'Cerámica',
    ];

    return $etiquetas[$categoria] ?? ucfirst($categoria);
}
