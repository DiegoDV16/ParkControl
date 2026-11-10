<?php

namespace App\Support;

class Rut
{
    public static function normalizar(string $rut): string
    {
        $limpio = strtoupper(preg_replace('/[^0-9Kk]/', '', $rut) ?? '');

        if (strlen($limpio) < 2) {
            return $limpio;
        }

        return substr($limpio, 0, -1).'-'.substr($limpio, -1);
    }

    public static function valido(string $rut): bool
    {
        $normalizado = self::normalizar($rut);

        if (! preg_match('/^(\d{7,8})-([0-9K])$/', $normalizado, $partes)) {
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
