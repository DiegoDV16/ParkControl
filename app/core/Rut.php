<?php
/**
 * ==========================================================================
 * ParkControl · app/core/Rut.php
 * ==========================================================================
 * Normalización, formato y validación del RUT chileno (módulo 11).
 * ==========================================================================
 */

class Rut
{
    /** Quita puntos y espacios y deja el formato 12345678-9. */
    public static function normalizar(string $rut): string
    {
        $limpio = strtoupper(preg_replace('/[^0-9Kk]/', '', $rut) ?? '');
        if (strlen($limpio) < 2) {
            return $limpio;
        }
        return substr($limpio, 0, -1) . '-' . substr($limpio, -1);
    }

    /** Formato de presentación con puntos: 12.345.678-9. */
    public static function formatear(string $rut): string
    {
        $normalizado = self::normalizar($rut);
        if (!str_contains($normalizado, '-')) {
            return $normalizado;
        }
        [$cuerpo, $dv] = explode('-', $normalizado, 2);
        return number_format((int) $cuerpo, 0, '', '.') . '-' . $dv;
    }

    /** Valida el dígito verificador. */
    public static function valido(string $rut): bool
    {
        $normalizado = self::normalizar($rut);
        if (!preg_match('/^(\d{7,8})-([0-9K])$/', $normalizado, $partes)) {
            return false;
        }

        $cuerpo = $partes[1];
        $dv = $partes[2];
        $suma = 0;
        $factor = 2;

        for ($i = strlen($cuerpo) - 1; $i >= 0; $i--) {
            $suma += (int) $cuerpo[$i] * $factor;
            $factor = $factor === 7 ? 2 : $factor + 1;
        }

        $resto = 11 - ($suma % 11);
        $esperado = $resto === 11 ? '0' : ($resto === 10 ? 'K' : (string) $resto);

        return $dv === $esperado;
    }
}
