import { useEffect, useState, type FormEvent } from 'react'
import { Modal } from './Modal'
import { ApiError, cobrarIngreso, previsualizarCobro } from '../lib/api'
import type { CobroPayload, IngresoResumen, PreviewCobro } from '../types'

interface Props {
  ingreso: IngresoResumen
  onCerrar: () => void
  onCobrado: (mensaje: string) => void
}

function mensajeDeError(fallo: unknown): string {
  if (fallo instanceof ApiError) {
    if (fallo.errors) {
      const primero = Object.values(fallo.errors)[0]
      if (Array.isArray(primero) && primero.length > 0) return primero[0]
    }
    return fallo.message
  }
  return 'Ocurrió un error inesperado.'
}

export function CobroModal({ ingreso, onCerrar, onCobrado }: Props) {
  const [fecha, setFecha] = useState(ingreso.fechaRegreso ?? new Date().toISOString().slice(0, 10))
  const [hora, setHora] = useState(
    (ingreso.horaRegreso ?? new Date().toTimeString().slice(0, 5)).slice(0, 5),
  )
  const [metodo, setMetodo] = useState<CobroPayload['metodo']>('efectivo')
  const [recibido, setRecibido] = useState('')
  const [referencia, setReferencia] = useState('')

  const [preview, setPreview] = useState<PreviewCobro | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [guardando, setGuardando] = useState(false)

  useEffect(() => {
    setPreview(null)
    setError(null)

    const temporizador = window.setTimeout(() => {
      previsualizarCobro(ingreso.idIngreso, fecha, hora)
        .then(setPreview)
        .catch(() => setPreview(null))
    }, 300)

    return () => window.clearTimeout(temporizador)
  }, [ingreso.idIngreso, fecha, hora])

  const vuelto = metodo === 'efectivo' && preview !== null && recibido !== ''
    ? Number(recibido) - preview.monto
    : null

  async function enviar(evento: FormEvent<HTMLFormElement>) {
    evento.preventDefault()
    if (!preview) return

    setGuardando(true)
    setError(null)

    try {
      const respuesta = await cobrarIngreso(ingreso.idIngreso, {
        fechaRegreso: fecha,
        horaRegreso: hora,
        metodo,
        montoRecibido: metodo === 'efectivo' && recibido !== '' ? Number(recibido) : null,
        referencia: referencia.trim() || null,
      })
      onCobrado(respuesta.message)
    } catch (fallo) {
      setError(mensajeDeError(fallo))
    } finally {
      setGuardando(false)
    }
  }

  return (
    <Modal abierto titulo="Cobrar y registrar llegada" onCerrar={onCerrar} ancho="max-w-xl">
      <form onSubmit={enviar} className="space-y-4" noValidate>
        <div className="rounded-lg border border-pc-border bg-pc-panel2/60 px-4 py-3 text-sm">
          <p className="font-semibold">
            {ingreso.vehiculo.patente} · {ingreso.vehiculo.marca} {ingreso.vehiculo.modelo}
          </p>
          <p className="text-xs text-pc-muted">
            {ingreso.cliente.nombre} · {ingreso.cliente.rut} · Fila {ingreso.fila}
          </p>
          <p className="mt-1 text-xs text-pc-muted">
            Entrada: {ingreso.fechaInicio} {ingreso.horaInicio.slice(0, 5)}
          </p>
        </div>

        {error && (
          <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-3 py-2 text-sm text-pc-danger">
            {error}
          </div>
        )}

        <div>
          <p className="mb-2 text-xs uppercase tracking-wide text-pc-muted">
            Llegada real (corrige según el vuelo)
          </p>
          <div className="grid gap-3 sm:grid-cols-2">
            <div>
              <label htmlFor="cFecha" className="pc-label">Fecha de llegada *</label>
              <input id="cFecha" type="date" className="pc-input" required value={fecha}
                onChange={(evento) => setFecha(evento.target.value)} />
            </div>
            <div>
              <label htmlFor="cHora" className="pc-label">Hora de llegada *</label>
              <input id="cHora" type="time" className="pc-input" required value={hora}
                onChange={(evento) => setHora(evento.target.value)} />
            </div>
          </div>
        </div>

        <div className="rounded-lg border border-pc-mint/30 bg-pc-mint/5 px-4 py-3">
          <div className="flex items-center justify-between text-sm">
            <span className="text-pc-muted">Días cobrables</span>
            <span className="font-semibold">{preview ? preview.dias : '…'}</span>
          </div>
          <div className="mt-1 flex items-center justify-between">
            <span className="text-sm text-pc-muted">Total a pagar</span>
            <span className="text-xl font-bold text-pc-mint">
              {preview ? `$${preview.monto.toLocaleString('es-CL')}` : 'Calculando…'}
            </span>
          </div>
        </div>

        <div>
          <label htmlFor="cMetodo" className="pc-label">Método de pago *</label>
          <select id="cMetodo" className="pc-input" value={metodo}
            onChange={(evento) => setMetodo(evento.target.value as CobroPayload['metodo'])}>
            <option value="efectivo">Efectivo</option>
            <option value="debito">Débito</option>
            <option value="credito">Crédito</option>
            <option value="transferencia">Transferencia</option>
          </select>
        </div>

        {metodo === 'efectivo' ? (
          <div className="grid gap-3 sm:grid-cols-2">
            <div>
              <label htmlFor="cRecibido" className="pc-label">Monto recibido</label>
              <input id="cRecibido" type="number" min={0} className="pc-input" value={recibido}
                onChange={(evento) => setRecibido(evento.target.value)} placeholder="Opcional" />
            </div>
            <div>
              <span className="pc-label">Vuelto</span>
              <p className={`pc-input ${vuelto !== null && vuelto < 0 ? 'text-pc-danger' : ''}`}>
                {vuelto !== null ? `$${vuelto.toLocaleString('es-CL')}` : '—'}
              </p>
            </div>
          </div>
        ) : (
          <div>
            <label htmlFor="cReferencia" className="pc-label">Referencia</label>
            <input id="cReferencia" className="pc-input" maxLength={50} value={referencia}
              onChange={(evento) => setReferencia(evento.target.value)}
              placeholder="N° operación / comprobante" />
          </div>
        )}

        {vuelto !== null && vuelto < 0 && (
          <p className="text-sm text-pc-danger">El monto recibido es menor al total a pagar.</p>
        )}

        <div className="flex justify-end gap-2 border-t border-pc-border pt-4">
          <button type="button" className="pc-btn-ghost" onClick={onCerrar} disabled={guardando}>
            Cancelar
          </button>
          <button
            type="submit"
            className="pc-btn-primary"
            disabled={guardando || !preview || (vuelto !== null && vuelto < 0)}
          >
            {guardando ? 'Cobrando…' : 'Confirmar cobro'}
          </button>
        </div>
      </form>
    </Modal>
  )
}
