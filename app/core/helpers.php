<?php
/**
 * ==========================================================================
 * ParkControl · app/core/helpers.php
 * ==========================================================================
 * Funciones utilitarias compartidas por controladores y vistas.
 * ==========================================================================
 */

/** Lee una clave de configuración con notación de puntos: config('db.host'). */
function config(string $clave, mixed $defecto = null): mixed
{
    $valor = $GLOBALS['pc_config'] ?? [];
    foreach (explode('.', $clave) as $parte) {
        if (!is_array($valor) || !array_key_exists($parte, $valor)) {
            return $defecto;
        }
        $valor = $valor[$parte];
    }
    return $valor;
}

/** Escapa texto para HTML (prevención de XSS). */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Construye una URL interna del front controller: url('usuarios', ['q' => 'x']). */
function url(string $ruta = 'dashboard', array $parametros = []): string
{
    $ruta = trim($ruta, '/');
    $parametros = $ruta === '' ? $parametros : ['ruta' => $ruta] + $parametros;
    $cadena = http_build_query($parametros);

    return BASE_URL . '/index.php' . ($cadena !== '' ? '?' . $cadena : '');
}

/** URL de un recurso estático dentro de app/assets. */
function asset(string $ruta): string
{
    return BASE_URL . '/assets/' . ltrim($ruta, '/');
}

/** Redirige a una ruta interna (con parámetros opcionales) y detiene la ejecución. */
function redirigir(string $ruta, array $parametros = []): never
{
    header('Location: ' . url($ruta, $parametros));
    exit;
}

/** Valor previo de un formulario (para repintar tras un error). */
function antiguo(string $campo, mixed $defecto = ''): mixed
{
    return $_SESSION['_old'][$campo] ?? $defecto;
}

/** Formatea un monto en pesos chilenos: $12.500. */
function dinero(float|int|string|null $monto): string
{
    return '$' . number_format((float) $monto, 0, ',', '.');
}

/** Fecha corta legible: 03-11-2026. */
function fechaCorta(?string $fecha): string
{
    if ($fecha === null || $fecha === '') {
        return '—';
    }
    $ts = strtotime($fecha);
    return $ts ? date('d-m-Y', $ts) : '—';
}

/** Hora corta legible: 19:45. */
function horaCorta(?string $hora): string
{
    if ($hora === null || $hora === '') {
        return '—';
    }
    $ts = strtotime($hora);
    return $ts ? date('H:i', $ts) : '—';
}

/** Iniciales de un nombre para el avatar: "Diego Soto" → "DS". */
function iniciales(string $nombre): string
{
    $partes = preg_split('/\s+/', trim($nombre)) ?: [];
    $letras = '';
    foreach ($partes as $parte) {
        if ($parte !== '') {
            $letras .= mb_strtoupper(mb_substr($parte, 0, 1));
        }
        if (mb_strlen($letras) >= 2) {
            break;
        }
    }
    return $letras !== '' ? $letras : 'US';
}

/** Nombre completo de una persona a partir de sus columnas. */
function nombreCompleto(array $fila): string
{
    return trim(implode(' ', array_filter([
        $fila['nombres'] ?? '',
        $fila['apellidoPaterno'] ?? '',
        $fila['apellidoMaterno'] ?? '',
    ])));
}

/** Fecha larga en español: "lunes, 3 de noviembre de 2026". */
function strftime_es(?int $marca = null): string
{
    $dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio',
        'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $ts = $marca ?? time();

    return $dias[(int) date('w', $ts)] . ', ' . (int) date('j', $ts)
        . ' de ' . $meses[(int) date('n', $ts) - 1] . ' de ' . date('Y', $ts);
}
