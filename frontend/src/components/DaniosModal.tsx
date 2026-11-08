import { useEffect, useState } from 'react'
import { Modal } from './Modal'
import { CanvasDanios, ETIQUETAS_VISTA } from './CanvasDanios'
import { ApiError, crearDanio, eliminarDanio, listarDanios } from '../lib/api'
import type { Danio, IngresoResumen, VistaDanio } from '../types'

const TIPOS = ['Rayón', 'Abolladura', 'Golpe', 'Vidrio', 'Espejo', 'Llanta', 'Otro']

interface Props {
  ingreso: IngresoResumen
  soloLectura: boolean
  onCerrar: () => void
}

function mensajeDeError(fallo: unknown): string {
  if (fallo instanceof ApiError) return fallo.message
  return 'Ocurrió un error inesperado.'
}

export function DaniosModal({ ingreso, soloLectura, onCerrar }: Props) {
  const [danios, setDanios] = useState<Danio[]>([])
  const [tipo, setTipo] = useState(TIPOS[0])
  const [detalle, setDetalle] = useState('')
  const [error, setError] = useState<string | null>(null)
  const [guardando, setGuardando] = useState(false)

  useEffect(() => {
    listarDanios(ingreso.idIngreso)
      .then((respuesta) => setDanios(respuesta.data))
      .catch((fallo) => setError(mensajeDeError(fallo)))
  }, [ingreso.idIngreso])

  async function marcar(vista: VistaDanio, coordX: number, coordY: number) {
    if (soloLectura || guardando) return

    setGuardando(true)
    setError(null)

    try {
      const respuesta = await crearDanio(ingreso.idIngreso, {
        vista,
        coordX,
        coordY,
        tipoDanio: tipo,
        detalle: detalle.trim() || null,
      })
      setDanios((previos) => [...previos, respuesta.data])
      setDetalle('')
    } catch (fallo) {
      setError(mensajeDeError(fallo))
    } finally {
      setGuardando(false)
    }
  }

  async function quitar(idDanio: number) {
    setError(null)

    try {
      await eliminarDanio(idDanio)
      setDanios((previos) => previos.filter((danio) => danio.idDanio !== idDanio))
    } catch (fallo) {
      setError(mensajeDeError(fallo))
    }
  }

  return (
    <Modal
      abierto
      titulo={`Inspección de daños · ${ingreso.vehiculo.patente}`}
      onCerrar={onCerrar}
      ancho="max-w-3xl"
    >
      <div className="space-y-4">
        <p className="text-sm text-pc-muted">
          {soloLectura
            ? 'Vista de solo lectura de los daños registrados al ingreso.'
            : 'Elige el tipo, escribe el detalle y toca la vista del vehículo (superior, lateral izquierda/derecha, frontal o trasera) para marcar el daño.'}
        </p>

        {error && (
          <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-3 py-2 text-sm text-pc-danger">
            {error}
          </div>
        )}

        <div className="grid gap-4 lg:grid-cols-[1.5fr_1fr]">
          <CanvasDanios
            danios={danios}
            soloLectura={soloLectura}
            onMarcar={(vista, x, y) => void marcar(vista, x, y)}
            onEliminar={(id) => void quitar(id)}
          />

          <div className="space-y-3">
            {!soloLectura && (
              <>
                <div>
                  <label htmlFor="dTipo" className="pc-label">Tipo de daño</label>
                  <select id="dTipo" className="pc-input" value={tipo}
                    onChange={(evento) => setTipo(evento.target.value)}>
                    {TIPOS.map((item) => (
                      <option key={item} value={item}>{item}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label htmlFor="dDetalle" className="pc-label">Detalle</label>
                  <input id="dDetalle" className="pc-input" maxLength={150} value={detalle}
                    onChange={(evento) => setDetalle(evento.target.value)}
                    placeholder="Puerta izquierda, parachoques…" />
                </div>
              </>
            )}

            <div className="rounded-lg border border-pc-border">
              <div className="border-b border-pc-border px-3 py-2 text-xs uppercase tracking-wide text-pc-muted">
                Daños marcados ({danios.length})
              </div>
              <ul className="max-h-56 divide-y divide-pc-border/60 overflow-y-auto text-sm">
                {danios.length === 0 && (
                  <li className="px-3 py-4 text-center text-xs text-pc-muted">Sin daños registrados.</li>
                )}
                {danios.map((danio, indice) => (
                  <li key={danio.idDanio} className="flex items-start gap-2 px-3 py-2">
                    <span className="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-pc-danger text-[10px] font-bold text-[#1a0505]">
                      {indice + 1}
                    </span>
                    <div className="min-w-0 flex-1">
                      <p className="font-medium">{danio.tipoDanio}</p>
                      <p className="text-[10px] uppercase tracking-wide text-pc-muted">
                        {ETIQUETAS_VISTA[danio.vista] ?? danio.vista}
                      </p>
                      {danio.detalle && <p className="text-xs text-pc-muted">{danio.detalle}</p>}
                    </div>
                    {!soloLectura && (
                      <button type="button" className="text-xs text-pc-danger hover:underline"
                        onClick={() => void quitar(danio.idDanio)}>
                        Quitar
                      </button>
                    )}
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </div>

        <div className="flex justify-end border-t border-pc-border pt-4">
          <button type="button" className="pc-btn-primary" onClick={onCerrar}>
            Cerrar
          </button>
        </div>
      </div>
    </Modal>
  )
}
