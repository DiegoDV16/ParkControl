import { useEffect, useState } from 'react'
import { Modal } from './Modal'
import { CanvasDanios, ETIQUETAS_VISTA } from './CanvasDanios'
import {
  actualizarDanio,
  ApiError,
  crearDanio,
  eliminarDanio,
  listarDanios,
} from '../lib/api'
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
  const [editando, setEditando] = useState<number | null>(null)
  const [editTipo, setEditTipo] = useState(TIPOS[0])
  const [editDetalle, setEditDetalle] = useState('')

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

  function mover(idDanio: number, coordX: number, coordY: number) {
    setDanios((previos) => previos.map((danio) => (
      danio.idDanio === idDanio ? { ...danio, coordX, coordY } : danio
    )))
  }

  async function moverFin(idDanio: number, coordX: number, coordY: number) {
    try {
      const respuesta = await actualizarDanio(idDanio, { coordX, coordY })
      setDanios((previos) => previos.map((danio) => (
        danio.idDanio === idDanio ? respuesta.data : danio
      )))
    } catch (fallo) {
      setError(mensajeDeError(fallo))
    }
  }

  function abrirEdicion(danio: Danio) {
    setEditando(danio.idDanio)
    setEditTipo(danio.tipoDanio)
    setEditDetalle(danio.detalle ?? '')
  }

  async function guardarEdicion(idDanio: number) {
    setGuardando(true)
    setError(null)

    try {
      const respuesta = await actualizarDanio(idDanio, {
        tipoDanio: editTipo,
        detalle: editDetalle.trim() || null,
      })
      setDanios((previos) => previos.map((danio) => (
        danio.idDanio === idDanio ? respuesta.data : danio
      )))
      setEditando(null)
    } catch (fallo) {
      setError(mensajeDeError(fallo))
    } finally {
      setGuardando(false)
    }
  }

  return (
    <Modal
      abierto
      titulo={`Inspección de daños · ${ingreso.vehiculo.patente}`}
      onCerrar={onCerrar}
      ancho="max-w-5xl"
    >
      <div className="space-y-4">
        <p className="text-sm text-pc-muted">
          {soloLectura
            ? 'Vista de solo lectura de los daños registrados al ingreso.'
            : 'Marca el daño sobre el dibujo del vehículo. Puedes arrastrar los marcadores, acercar cada vista y editar los daños desde la lista.'}
        </p>

        {error && (
          <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-3 py-2 text-sm text-pc-danger">
            {error}
          </div>
        )}

        <CanvasDanios
          danios={danios}
          tipo={ingreso.vehiculo.tipo}
          soloLectura={soloLectura}
          onMarcar={(vista, x, y) => void marcar(vista, x, y)}
          onMover={mover}
          onMoverFin={(id, x, y) => void moverFin(id, x, y)}
        />

        <div className="grid gap-4 sm:grid-cols-2">
          {!soloLectura && (
            <div className="space-y-3">
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
            </div>
          )}

          <div className={`rounded-lg border border-pc-border ${soloLectura ? 'sm:col-span-2' : ''}`}>
            <div className="border-b border-pc-border px-3 py-2 text-xs uppercase tracking-wide text-pc-muted">
              Daños marcados ({danios.length})
            </div>
            <ul className="max-h-56 divide-y divide-pc-border/60 overflow-y-auto text-sm">
              {danios.length === 0 && (
                <li className="px-3 py-4 text-center text-xs text-pc-muted">Sin daños registrados.</li>
              )}
              {danios.map((danio, indice) => (
                <li key={danio.idDanio} className="px-3 py-2">
                  <div className="flex items-start gap-2">
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
                      <div className="flex gap-2">
                        <button type="button" className="text-xs text-pc-mint hover:underline"
                          onClick={() => abrirEdicion(danio)}>
                          Editar
                        </button>
                        <button type="button" className="text-xs text-pc-danger hover:underline"
                          onClick={() => void quitar(danio.idDanio)}>
                          Quitar
                        </button>
                      </div>
                    )}
                  </div>

                  {editando === danio.idDanio && !soloLectura && (
                    <div className="mt-2 space-y-2 rounded-lg border border-pc-border bg-pc-panel2/50 p-2">
                      <select className="pc-input" value={editTipo}
                        onChange={(evento) => setEditTipo(evento.target.value)}>
                        {TIPOS.map((item) => (
                          <option key={item} value={item}>{item}</option>
                        ))}
                      </select>
                      <input className="pc-input" maxLength={150} value={editDetalle}
                        onChange={(evento) => setEditDetalle(evento.target.value)}
                        placeholder="Detalle" />
                      <div className="flex justify-end gap-2">
                        <button type="button" className="pc-btn-ghost px-2 text-xs"
                          onClick={() => setEditando(null)}>
                          Cancelar
                        </button>
                        <button type="button" className="pc-btn-primary px-2 text-xs"
                          disabled={guardando}
                          onClick={() => void guardarEdicion(danio.idDanio)}>
                          Guardar
                        </button>
                      </div>
                    </div>
                  )}
                </li>
              ))}
            </ul>
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
