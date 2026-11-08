import { useState } from 'react'
import { Modal } from './Modal'
import { CanvasDanios, ETIQUETAS_VISTA } from './CanvasDanios'
import type { Danio, VistaDanio } from '../types'

const TIPOS = ['Rayón', 'Abolladura', 'Golpe', 'Vidrio', 'Espejo', 'Llanta', 'Otro']

interface Props {
  danios: Danio[]
  tipo?: string | null
  onCambiar: (danios: Danio[]) => void
  onCerrar: () => void
}

export function DaniosBorradorModal({ danios, tipo, onCambiar, onCerrar }: Props) {
  const [tipoDanio, setTipoDanio] = useState(TIPOS[0])
  const [detalle, setDetalle] = useState('')
  const [editando, setEditando] = useState<number | null>(null)
  const [editTipo, setEditTipo] = useState(TIPOS[0])
  const [editDetalle, setEditDetalle] = useState('')

  function marcar(vista: VistaDanio, coordX: number, coordY: number) {
    onCambiar([
      ...danios,
      {
        idDanio: -Date.now(),
        vista,
        coordX,
        coordY,
        tipoDanio,
        detalle: detalle.trim() || null,
      },
    ])
    setDetalle('')
  }

  function quitar(idDanio: number) {
    onCambiar(danios.filter((danio) => danio.idDanio !== idDanio))
  }

  function mover(idDanio: number, coordX: number, coordY: number) {
    onCambiar(danios.map((danio) => (
      danio.idDanio === idDanio ? { ...danio, coordX, coordY } : danio
    )))
  }

  function abrirEdicion(danio: Danio) {
    setEditando(danio.idDanio)
    setEditTipo(danio.tipoDanio)
    setEditDetalle(danio.detalle ?? '')
  }

  function guardarEdicion(idDanio: number) {
    onCambiar(danios.map((danio) => (
      danio.idDanio === idDanio
        ? { ...danio, tipoDanio: editTipo, detalle: editDetalle.trim() || null }
        : danio
    )))
    setEditando(null)
  }

  return (
    <Modal abierto titulo="Inspección de daños" onCerrar={onCerrar} ancho="max-w-5xl">
      <div className="space-y-4">
        <p className="text-sm text-pc-muted">
          Marca el daño sobre el dibujo del vehículo. Puedes arrastrar los marcadores, acercar cada
          vista y editar los daños desde la lista. Se guardarán al confirmar el ingreso.
        </p>

        <CanvasDanios
          danios={danios}
          tipo={tipo}
          onMarcar={marcar}
          onMover={mover}
          onMoverFin={mover}
        />

        <div className="grid gap-4 sm:grid-cols-2">
          <div className="space-y-3">
            <div>
              <label htmlFor="bTipo" className="pc-label">Tipo de daño</label>
              <select id="bTipo" className="pc-input" value={tipoDanio}
                onChange={(evento) => setTipoDanio(evento.target.value)}>
                {TIPOS.map((item) => (
                  <option key={item} value={item}>{item}</option>
                ))}
              </select>
            </div>
            <div>
              <label htmlFor="bDetalle" className="pc-label">Detalle</label>
              <input id="bDetalle" className="pc-input" maxLength={150} value={detalle}
                onChange={(evento) => setDetalle(evento.target.value)}
                placeholder="Puerta izquierda, parachoques…" />
            </div>
          </div>

          <div className="rounded-lg border border-pc-border">
            <div className="border-b border-pc-border px-3 py-2 text-xs uppercase tracking-wide text-pc-muted">
              Daños marcados ({danios.length})
            </div>
            <ul className="max-h-56 divide-y divide-pc-border/60 overflow-y-auto text-sm">
              {danios.length === 0 && (
                <li className="px-3 py-4 text-center text-xs text-pc-muted">
                  Aún no has marcado daños.
                </li>
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
                    <div className="flex gap-2">
                      <button type="button" className="text-xs text-pc-mint hover:underline"
                        onClick={() => abrirEdicion(danio)}>
                        Editar
                      </button>
                      <button type="button" className="text-xs text-pc-danger hover:underline"
                        onClick={() => quitar(danio.idDanio)}>
                        Quitar
                      </button>
                    </div>
                  </div>

                  {editando === danio.idDanio && (
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
                          onClick={() => guardarEdicion(danio.idDanio)}>
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
            Listo
          </button>
        </div>
      </div>
    </Modal>
  )
}
