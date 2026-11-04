<?php
/**
 * ==========================================================================
 * ParkControl · app/core/Validador.php
 * ==========================================================================
 * Validación de formularios del lado del servidor.
 *
 *   $v = (new Validador($_POST))->requerido('nombres', 'nombres')->rut('rut', 'RUT');
 *   if (!$v->valido()) { ... $v->errores() ... }
 * ==========================================================================
 */

class Validador
{
    /** @var array<string, string> */
    private array $errores = [];

    public function __construct(private array $datos)
    {
    }

    public function valor(string $campo): string
    {
        return trim((string) ($this->datos[$campo] ?? ''));
    }

    public function requerido(string $campo, string $etiqueta): self
    {
        if ($this->valor($campo) === '') {
            $this->errores[$campo] = 'El campo ' . $etiqueta . ' es obligatorio.';
        }
        return $this;
    }

    public function correo(string $campo, string $etiqueta): self
    {
        $valor = $this->valor($campo);
        if ($valor !== '' && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            $this->errores[$campo] = 'Ingresa un correo válido en ' . $etiqueta . '.';
        }
        return $this;
    }

    public function entero(string $campo, string $etiqueta, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): self
    {
        $valor = $this->valor($campo);
        if ($valor === '') {
            return $this;
        }
        if (!filter_var($valor, FILTER_VALIDATE_INT)) {
            $this->errores[$campo] = $etiqueta . ' debe ser un número entero.';
            return $this;
        }
        $numero = (int) $valor;
        if ($numero < $min || $numero > $max) {
            $this->errores[$campo] = $etiqueta . ' debe estar entre ' . $min . ' y ' . $max . '.';
        }
        return $this;
    }

    public function decimal(string $campo, string $etiqueta, float $min = 0.0): self
    {
        $valor = str_replace(['.', ','], ['', '.'], $this->valor($campo));
        if ($valor === '') {
            return $this;
        }
        if (!is_numeric($valor) || (float) $valor < $min) {
            $this->errores[$campo] = $etiqueta . ' debe ser un monto válido.';
        }
        return $this;
    }

    public function longitud(string $campo, string $etiqueta, int $min, int $max): self
    {
        $valor = $this->valor($campo);
        $largo = mb_strlen($valor);
        if ($valor !== '' && ($largo < $min || $largo > $max)) {
            $this->errores[$campo] = $etiqueta . ' debe tener entre ' . $min . ' y ' . $max . ' caracteres.';
        }
        return $this;
    }

    public function rut(string $campo, string $etiqueta): self
    {
        $valor = $this->valor($campo);
        if ($valor !== '' && !Rut::valido($valor)) {
            $this->errores[$campo] = $etiqueta . ' no es un RUT válido.';
        }
        return $this;
    }

    public function patente(string $campo, string $etiqueta): self
    {
        $valor = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $this->valor($campo)) ?? '');
        if ($valor !== '' && !preg_match('/^[A-Z]{2,4}\d{2,4}$/', $valor)) {
            $this->errores[$campo] = $etiqueta . ' debe tener el formato AB1234 o BBBB12.';
        }
        return $this;
    }

    public function fecha(string $campo, string $etiqueta): self
    {
        $valor = $this->valor($campo);
        if ($valor === '') {
            return $this;
        }
        $fecha = DateTime::createFromFormat('Y-m-d', $valor);
        if (!$fecha || $fecha->format('Y-m-d') !== $valor) {
            $this->errores[$campo] = $etiqueta . ' no es una fecha válida.';
        }
        return $this;
    }

    public function enLista(string $campo, string $etiqueta, array $permitidos): self
    {
        $valor = $this->valor($campo);
        if ($valor !== '' && !in_array($valor, array_map('strval', $permitidos), true)) {
            $this->errores[$campo] = $etiqueta . ' tiene un valor no permitido.';
        }
        return $this;
    }

    public function agregar(string $campo, string $mensaje): void
    {
        $this->errores[$campo] = $mensaje;
    }

    public function valido(): bool
    {
        return $this->errores === [];
    }

    /** @return array<string, string> */
    public function errores(): array
    {
        return $this->errores;
    }

    public function primerError(): ?string
    {
        return $this->errores === [] ? null : reset($this->errores);
    }
}
