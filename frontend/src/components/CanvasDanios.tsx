import { useRef, type ReactNode } from 'react'
import type { Danio, VistaDanio } from '../types'

export const ETIQUETAS_VISTA: Record<VistaDanio, string> = {
  superior: 'Superior',
  lateral_izquierda: 'Lateral izquierda',
  lateral_derecha: 'Lateral derecha',
  frontal: 'Frontal',
  trasera: 'Trasera',
}

interface Props {
  danios: Danio[]
  soloLectura?: boolean
  onMarcar?: (vista: VistaDanio, coordX: number, coordY: number) => void
  onEliminar?: (idDanio: number) => void
}

interface VistaProps {
  vista: VistaDanio
  aspect: string
  espejo?: boolean
  children: ReactNode
  danios: Danio[]
  soloLectura: boolean
  onMarcar?: (vista: VistaDanio, coordX: number, coordY: number) => void
  onEliminar?: (idDanio: number) => void
}

function Vista({ vista, aspect, espejo = false, children, danios, soloLectura, onMarcar, onEliminar }: VistaProps) {
  const contenedor = useRef<HTMLDivElement>(null)
  const propios = danios.filter((danio) => danio.vista === vista)

  return (
    <div className="rounded-lg border border-pc-border bg-pc-panel/60 p-2">
      <div
        ref={contenedor}
        role={soloLectura ? 'img' : 'button'}
        aria-label={`Vista ${ETIQUETAS_VISTA[vista]}`}
        tabIndex={soloLectura ? -1 : 0}
        className={`relative w-full select-none ${soloLectura ? '' : 'cursor-crosshair'}`}
        style={{ aspectRatio: aspect, touchAction: 'none' }}
        onClick={(evento) => {
          if (soloLectura || !onMarcar) return
          const rect = evento.currentTarget.getBoundingClientRect()
          const x = ((evento.clientX - rect.left) / rect.width) * 100
          const y = ((evento.clientY - rect.top) / rect.height) * 100
          onMarcar(vista, Number(x.toFixed(2)), Number(y.toFixed(2)))
        }}
      >
        <svg
          viewBox="0 0 120 100"
          preserveAspectRatio="none"
          className="absolute inset-0 h-full w-full"
          style={espejo ? { transform: 'scaleX(-1)' } : undefined}
          aria-hidden="true"
        >
          {children}
        </svg>

        {propios.map((danio) => {
          const indice = danios.indexOf(danio) + 1
          return (
            <button
              key={danio.idDanio}
              type="button"
              title={`${danio.tipoDanio}${danio.detalle ? `: ${danio.detalle}` : ''}`}
              className="absolute flex h-6 w-6 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-2 border-white bg-pc-danger text-[11px] font-bold text-[#1a0505] shadow"
              style={{ left: `${danio.coordX}%`, top: `${danio.coordY}%` }}
              onClick={(evento) => {
                evento.stopPropagation()
                if (!soloLectura && onEliminar) onEliminar(danio.idDanio)
              }}
            >
              {indice}
            </button>
          )
        })}
      </div>
      <p className="mt-1 text-center text-[10px] font-semibold uppercase tracking-wide text-pc-muted">
        {ETIQUETAS_VISTA[vista]}
      </p>
    </div>
  )
}

/**
 * Ficha de inspección con cinco vistas del vehículo. Cada daño guarda la vista
 * y coordenadas relativas (0-100) dentro de esa vista.
 */
export function CanvasDanios({ danios, soloLectura = false, onMarcar, onEliminar }: Props) {
  return (
    <div className="grid gap-3 sm:grid-cols-2">
      <Vista vista="superior" aspect="120 / 130" danios={danios} soloLectura={soloLectura}
        onMarcar={onMarcar} onEliminar={onEliminar}>
        <rect x="35" y="10" width="50" height="80" rx="16" fill="#0d2438" stroke="#3ee0a1" strokeWidth="1.4" />
        <path d="M42 38 L48 26 H72 L78 38 Z" fill="#14344c" stroke="#3ee0a1" strokeWidth="1.1" />
        <rect x="44" y="42" width="32" height="30" rx="4" fill="#14344c" stroke="#3ee0a1" strokeWidth="1.1" />
        <path d="M42 78 L48 90 H72 L78 78 Z" fill="#14344c" stroke="#3ee0a1" strokeWidth="1.1" />
        <rect x="27" y="38" width="8" height="13" rx="3" fill="#14344c" stroke="#3ee0a1" strokeWidth="1" />
        <rect x="85" y="38" width="8" height="13" rx="3" fill="#14344c" stroke="#3ee0a1" strokeWidth="1" />
      </Vista>

      <Vista vista="frontal" aspect="120 / 100" danios={danios} soloLectura={soloLectura}
        onMarcar={onMarcar} onEliminar={onEliminar}>
        <path d="M22 24 Q22 14 32 14 H88 Q98 14 98 24 V88 H22 Z" fill="#0d2438" stroke="#3ee0a1" strokeWidth="1.4" />
        <path d="M30 38 L38 22 H82 L90 38 Z" fill="#14344c" stroke="#3ee0a1" strokeWidth="1.1" />
        <rect x="26" y="46" width="18" height="7" rx="3" fill="#3ee0a1" opacity="0.55" />
        <rect x="76" y="46" width="18" height="7" rx="3" fill="#3ee0a1" opacity="0.55" />
        <rect x="50" y="46" width="20" height="8" rx="2" fill="#14344c" stroke="#3ee0a1" strokeWidth="0.8" />
        <rect x="24" y="78" width="72" height="7" rx="3" fill="#14344c" stroke="#3ee0a1" strokeWidth="0.8" />
      </Vista>

      <Vista vista="lateral_izquierda" aspect="140 / 90" danios={danios} soloLectura={soloLectura}
        onMarcar={onMarcar} onEliminar={onEliminar}>
        <path d="M8 68 C8 54 16 48 28 46 L40 30 H86 L98 46 C110 48 118 54 118 68 V78 H8 Z"
          fill="#0d2438" stroke="#3ee0a1" strokeWidth="1.4" strokeLinejoin="round" />
        <path d="M40 30 L48 18 H80 L88 30 Z" fill="#14344c" stroke="#3ee0a1" strokeWidth="1.1" />
        <path d="M46 30 L52 21 H64 V30 Z" fill="#0a1c2e" stroke="#3ee0a1" strokeWidth="0.7" />
        <path d="M66 30 H80 L84 21 H68 Z" fill="#0a1c2e" stroke="#3ee0a1" strokeWidth="0.7" />
        <line x1="65" y1="31" x2="65" y2="74" stroke="#3ee0a1" strokeWidth="0.9" />
        <circle cx="34" cy="78" r="11" fill="#0a1c2e" stroke="#3ee0a1" strokeWidth="1.2" />
        <circle cx="34" cy="78" r="5" fill="#14344c" stroke="#3ee0a1" strokeWidth="0.8" />
        <circle cx="98" cy="78" r="11" fill="#0a1c2e" stroke="#3ee0a1" strokeWidth="1.2" />
        <circle cx="98" cy="78" r="5" fill="#14344c" stroke="#3ee0a1" strokeWidth="0.8" />
        <rect x="114" y="52" width="4" height="8" rx="1.5" fill="#3ee0a1" />
      </Vista>

      <Vista vista="lateral_derecha" aspect="140 / 90" espejo danios={danios} soloLectura={soloLectura}
        onMarcar={onMarcar} onEliminar={onEliminar}>
        <path d="M8 68 C8 54 16 48 28 46 L40 30 H86 L98 46 C110 48 118 54 118 68 V78 H8 Z"
          fill="#0d2438" stroke="#3ee0a1" strokeWidth="1.4" strokeLinejoin="round" />
        <path d="M40 30 L48 18 H80 L88 30 Z" fill="#14344c" stroke="#3ee0a1" strokeWidth="1.1" />
        <path d="M46 30 L52 21 H64 V30 Z" fill="#0a1c2e" stroke="#3ee0a1" strokeWidth="0.7" />
        <path d="M66 30 H80 L84 21 H68 Z" fill="#0a1c2e" stroke="#3ee0a1" strokeWidth="0.7" />
        <line x1="65" y1="31" x2="65" y2="74" stroke="#3ee0a1" strokeWidth="0.9" />
        <circle cx="34" cy="78" r="11" fill="#0a1c2e" stroke="#3ee0a1" strokeWidth="1.2" />
        <circle cx="34" cy="78" r="5" fill="#14344c" stroke="#3ee0a1" strokeWidth="0.8" />
        <circle cx="98" cy="78" r="11" fill="#0a1c2e" stroke="#3ee0a1" strokeWidth="1.2" />
        <circle cx="98" cy="78" r="5" fill="#14344c" stroke="#3ee0a1" strokeWidth="0.8" />
        <rect x="114" y="52" width="4" height="8" rx="1.5" fill="#3ee0a1" />
      </Vista>

      <Vista vista="trasera" aspect="120 / 100" danios={danios} soloLectura={soloLectura}
        onMarcar={onMarcar} onEliminar={onEliminar}>
        <path d="M22 24 Q22 14 32 14 H88 Q98 14 98 24 V88 H22 Z" fill="#0d2438" stroke="#3ee0a1" strokeWidth="1.4" />
        <path d="M30 38 L38 22 H82 L90 38 Z" fill="#14344c" stroke="#3ee0a1" strokeWidth="1.1" />
        <rect x="26" y="46" width="16" height="7" rx="3" fill="#ff6b6b" opacity="0.65" />
        <rect x="78" y="46" width="16" height="7" rx="3" fill="#ff6b6b" opacity="0.65" />
        <line x1="34" y1="60" x2="86" y2="60" stroke="#3ee0a1" strokeWidth="0.8" />
        <rect x="24" y="78" width="72" height="7" rx="3" fill="#14344c" stroke="#3ee0a1" strokeWidth="0.8" />
      </Vista>

      <div className="flex flex-col justify-center rounded-lg border border-dashed border-pc-border p-4 text-xs text-pc-muted">
        <p className="font-semibold text-pc-text">Ficha de inspección</p>
        <p className="mt-2">Toca la vista donde esté el daño. Cada marca guarda su vista y coordenadas.</p>
        <p className="mt-2">Toca un marcador para quitarlo.</p>
      </div>
    </div>
  )
}
