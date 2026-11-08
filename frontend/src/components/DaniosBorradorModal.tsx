import { useState } from 'react'
import { Modal } from './Modal'
import { CanvasDanios, ETIQUETAS_VISTA } from './CanvasDanios'
import type { Danio, VistaDanio } from '../types'

const TIPOS = ['Rayón', 'Abolladura', 'Golpe', 'Vidrio', 'Espejo', 'Llanta', 'Otro']

interface Props {
  danios: Danio[]
  onCambiar: (danios: Danio[]) => void
  onCerrar: () => void
}

/**
 * Ficha de inspección antes de registrar el ingreso: los daños quedan en
 * memoria y se guardan junto con el vehículo al confirmar la recepción.
 */
export function DaniosBorradorModal({ danios, onCambiar, onCerrar }: Props) {
  const [tipo, setTipo] = useState(TIPOS[0])
  const [detalle, setDetalle] = useState('')

  function marcar(vista: VistaDanio, coordX: number, coordY: number) {
    onCambiar([
      ...danios,
      {
        idDanio: -Date.now(),
        vista,
        coordX,
        coordY,
        tipoDanio: tipo,
        detalle: detalle.trim() || null,
      },
    ])
    setDetalle('')
  }

  function quitar(idDanio: number) {
    onCambiar(danios.filter((danio) => danio.idDanio !== idDanio))
  }

  return (
    <Modal abierto titulo="Inspección de daños" onCerrar={onCerrar} ancho="max-w-3xl">
      <div className="space-y-4">
        <p className="text-sm text-pc-muted">
          Elige el tipo, escribe el detalle y toca la vista donde esté el daño. Los daños se guardarán
          al confirmar el ingreso.
        </p>

        <div className="grid gap-4 lg:grid-cols-[1.5fr_1fr]">
          <CanvasDanios
            danios={danios}
            onMarcar={marcar}
            onEliminar={quitar}
          />

          <div className="space-y-3">
            <div>
              <label htmlFor="bTipo" className="pc-label">Tipo de daño</label>
              <select id="bTipo" className="pc-input" value={tipo}
                onChange={(evento) => setTipo(evento.target.value)}>
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

            <div className="rounded-lg border border-pc-border">
              <div className="border-b border-pc-border px-3 py-2 text-xs uppercase tracking-wide text-pc-muted">
                Daños marcados ({danios.length})
              </div>
              <ul className="max-h-64 divide-y divide-pc-border/60 overflow-y-auto text-sm">
                {danios.length === 0 && (
                  <li className="px-3 py-4 text-center text-xs text-pc-muted">
                    Aún no has marcado daños.
                  </li>
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
                    <button type="button" className="text-xs text-pc-danger hover:underline"
                      onClick={() => quitar(danio.idDanio)}>
                      Quitar
                    </button>
                  </li>
                ))}
              </ul>
            </div>
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
