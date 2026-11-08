import { useRef, useState, type PointerEvent as ReactPointerEvent, type ReactNode } from 'react'
import type { Danio, VistaDanio } from '../types'

export const ETIQUETAS_VISTA: Record<VistaDanio, string> = {
  superior: 'Superior',
  lateral_izquierda: 'Lateral izquierdo',
  lateral_derecha: 'Lateral derecho',
  frontal: 'Frontal',
  trasera: 'Trasera',
}

const TINTA = '#1f3a5f'
const RELLENO = '#eef2f7'

function Frontal() {
  return (
    <g stroke={TINTA} strokeWidth="1.2" fill="none" strokeLinejoin="round" strokeLinecap="round">
      <path d="M14 34 Q14 24 24 24 H96 Q106 24 106 34 V78 Q106 86 98 86 H22 Q14 86 14 78 Z" fill={RELLENO} />
      <path d="M20 46 H100" strokeWidth="0.8" />
      <path d="M32 34 L40 18 H80 L88 34 Z" fill="#ffffff" />
      <path d="M40 18 H80" strokeWidth="0.8" />
      <rect x="18" y="50" width="22" height="10" rx="4" fill="#ffffff" />
      <rect x="80" y="50" width="22" height="10" rx="4" fill="#ffffff" />
      <rect x="48" y="50" width="24" height="12" rx="3" fill="#ffffff" />
      <path d="M52 54 H68 M52 58 H68" strokeWidth="0.7" />
      <rect x="16" y="74" width="88" height="8" rx="4" fill="#ffffff" />
      <rect x="50" y="76" width="20" height="5" rx="1.5" fill="#ffffff" />
      <path d="M12 44 q-6 2 -4 8 l6 -1 Z" fill="#ffffff" />
      <path d="M108 44 q6 2 4 8 l-6 -1 Z" fill="#ffffff" />
      <path d="M34 40 L52 44 M86 40 L68 44" strokeWidth="0.8" />
    </g>
  )
}

function Trasera() {
  return (
    <g stroke={TINTA} strokeWidth="1.2" fill="none" strokeLinejoin="round" strokeLinecap="round">
      <path d="M14 34 Q14 24 24 24 H96 Q106 24 106 34 V78 Q106 86 98 86 H22 Q14 86 14 78 Z" fill={RELLENO} />
      <path d="M32 34 L40 18 H80 L88 34 Z" fill="#ffffff" />
      <path d="M40 18 H80" strokeWidth="0.8" />
      <rect x="18" y="48" width="24" height="10" rx="4" fill="#f3d6d6" />
      <rect x="78" y="48" width="24" height="10" rx="4" fill="#f3d6d6" />
      <path d="M20 64 H100" strokeWidth="0.8" />
      <rect x="16" y="74" width="88" height="8" rx="4" fill="#ffffff" />
      <rect x="50" y="76" width="20" height="5" rx="1.5" fill="#ffffff" />
      <path d="M12 44 q-6 2 -4 8 l6 -1 Z" fill="#ffffff" />
      <path d="M108 44 q6 2 4 8 l-6 -1 Z" fill="#ffffff" />
    </g>
  )
}

function Lateral({ espejo = false }: { espejo?: boolean }) {
  return (
    <g transform={espejo ? 'translate(160 0) scale(-1 1)' : undefined}
      stroke={TINTA} strokeWidth="1.2" fill="none" strokeLinejoin="round" strokeLinecap="round">
      <path d="M8 70 C8 62 12 58 20 56 L28 44 C34 32 46 26 60 24 H104 C116 24 126 30 134 40 L148 44 C156 46 158 50 158 56 V70 Z" fill={RELLENO} />
      <path d="M8 70 H158" strokeWidth="0.9" />
      <path d="M36 42 L44 30 H72 V42 Z" fill="#ffffff" />
      <path d="M78 30 H100 L112 42 H78 Z" fill="#ffffff" />
      <path d="M72 30 V42" strokeWidth="0.8" />
      <path d="M74 42 V66" strokeWidth="0.8" />
      <path d="M110 42 V66" strokeWidth="0.8" />
      <path d="M80 52 H88 M116 52 H124" strokeWidth="0.9" />
      <circle cx="38" cy="70" r="14" fill="#ffffff" />
      <circle cx="38" cy="70" r="7" fill="#ffffff" />
      <path d="M38 63 V77 M31 70 H45 M33 65 L43 75 M43 65 L33 75" strokeWidth="0.6" />
      <circle cx="126" cy="70" r="14" fill="#ffffff" />
      <circle cx="126" cy="70" r="7" fill="#ffffff" />
      <path d="M126 63 V77 M119 70 H133 M121 65 L131 75 M131 65 L121 75" strokeWidth="0.6" />
      <path d="M150 52 h6 v6 h-6 Z" fill="#ffffff" />
      <path d="M10 54 h6 v6 h-6 Z" fill="#ffffff" />
      <path d="M108 42 q6 -2 8 2 l-4 4" fill="#ffffff" />
      <path d="M20 60 H150" strokeWidth="0.7" />
    </g>
  )
}

function Superior() {
  return (
    <g stroke={TINTA} strokeWidth="1.2" fill="none" strokeLinejoin="round" strokeLinecap="round">
      <rect x="22" y="10" width="66" height="130" rx="24" fill={RELLENO} />
      <path d="M30 48 L37 26 H73 L80 48 Z" fill="#ffffff" />
      <path d="M37 26 H73" strokeWidth="0.8" />
      <rect x="32" y="54" width="46" height="44" rx="8" fill="#ffffff" />
      <path d="M30 104 L37 126 H73 L80 104 Z" fill="#ffffff" />
      <path d="M37 126 H73" strokeWidth="0.8" />
      <path d="M14 48 q-6 2 -4 10 l6 -2 Z" fill="#ffffff" />
      <path d="M96 48 q6 2 4 10 l-6 -2 Z" fill="#ffffff" />
      <path d="M22 40 q-6 2 -6 8 v10 q0 6 6 8" fill="#ffffff" />
      <path d="M88 40 q6 2 6 8 v10 q0 6 -6 8" fill="#ffffff" />
      <path d="M22 100 q-6 2 -6 8 v8 q0 6 6 8" fill="#ffffff" />
      <path d="M88 100 q6 2 6 8 v8 q0 6 -6 8" fill="#ffffff" />
    </g>
  )
}

interface Props {
  danios: Danio[]
  tipo?: string | null
  soloLectura?: boolean
  onMarcar?: (vista: VistaDanio, coordX: number, coordY: number) => void
  onEliminar?: (idDanio: number) => void
  onMover?: (idDanio: number, coordX: number, coordY: number) => void
  onMoverFin?: (idDanio: number, coordX: number, coordY: number) => void
}

interface VistaProps {
  vista: VistaDanio
  aspect: string
  viewBox: string
  children: ReactNode
  danios: Danio[]
  soloLectura: boolean
  maxAlto?: number
  onMarcar?: Props['onMarcar']
  onMover?: Props['onMover']
  onMoverFin?: Props['onMoverFin']
}

function Vista({ vista, aspect, viewBox, children, danios, soloLectura, maxAlto = 280, onMarcar, onMover, onMoverFin }: VistaProps) {
  const contenedor = useRef<HTMLDivElement>(null)
  const arrastre = useRef<{ id: number; x: number; y: number } | null>(null)
  const [zoom, setZoom] = useState(1)

  const propios = danios.filter((danio) => danio.vista === vista)

  function coords(clientX: number, clientY: number) {
    const rect = contenedor.current?.getBoundingClientRect()
    if (!rect) return null
    const x = Math.min(100, Math.max(0, ((clientX - rect.left) / rect.width) * 100))
    const y = Math.min(100, Math.max(0, ((clientY - rect.top) / rect.height) * 100))
    return { x: Number(x.toFixed(2)), y: Number(y.toFixed(2)) }
  }

  function mover(evento: ReactPointerEvent<HTMLButtonElement>, idDanio: number) {
    if (!arrastre.current || arrastre.current.id !== idDanio) return
    const punto = coords(evento.clientX, evento.clientY)
    if (!punto) return
    arrastre.current = { id: idDanio, ...punto }
    onMover?.(idDanio, punto.x, punto.y)
  }

  function soltar(evento: ReactPointerEvent<HTMLButtonElement>, idDanio: number) {
    if (!arrastre.current || arrastre.current.id !== idDanio) return
    const punto = coords(evento.clientX, evento.clientY) ?? arrastre.current
    onMoverFin?.(idDanio, punto.x, punto.y)
    arrastre.current = null
  }

  return (
    <div className="rounded-lg border border-[#c9d3e0] bg-[#f7f5ee] p-2 shadow-sm">
      <div className="mb-1 flex items-center justify-between gap-2">
        <span className="text-[10px] font-bold uppercase tracking-[0.12em] text-[#1f3a5f]">
          {ETIQUETAS_VISTA[vista]}
        </span>
        <div className="flex items-center gap-1">
          <button type="button" className="h-6 w-6 rounded border border-[#9fb0c4] text-xs text-[#1f3a5f]"
            onClick={() => setZoom((valor) => Math.max(1, Number((valor - 0.5).toFixed(1))))} aria-label="Alejar">
            −
          </button>
          <span className="min-w-8 text-center text-[10px] text-[#1f3a5f]">{zoom}x</span>
          <button type="button" className="h-6 w-6 rounded border border-[#9fb0c4] text-xs text-[#1f3a5f]"
            onClick={() => setZoom((valor) => Math.min(3, Number((valor + 0.5).toFixed(1))))} aria-label="Acercar">
            +
          </button>
        </div>
      </div>

      <div className="overflow-auto" style={{ maxHeight: maxAlto }}>
        <div
          ref={contenedor}
          role={soloLectura ? 'img' : 'button'}
          aria-label={`Vista ${ETIQUETAS_VISTA[vista]}`}
          tabIndex={soloLectura ? -1 : 0}
          className={`relative w-full select-none ${soloLectura ? '' : 'cursor-crosshair'}`}
          style={{ aspectRatio: aspect, width: `${zoom * 100}%`, touchAction: 'none' }}
          onClick={(evento) => {
            if (soloLectura || !onMarcar || arrastre.current) return
            const punto = coords(evento.clientX, evento.clientY)
            if (punto) onMarcar(vista, punto.x, punto.y)
          }}
        >
          <svg viewBox={viewBox} preserveAspectRatio="none" className="absolute inset-0 h-full w-full" aria-hidden="true">
            {children}
          </svg>

          {propios.map((danio) => {
            const indice = danios.indexOf(danio) + 1
            return (
              <button
                key={danio.idDanio}
                type="button"
                title={`${danio.tipoDanio}${danio.detalle ? `: ${danio.detalle}` : ''}`}
                className="absolute flex h-6 w-6 -translate-x-1/2 -translate-y-1/2 cursor-grab touch-none items-center justify-center rounded-full border-2 border-white bg-[#d64545] text-[11px] font-bold text-white shadow active:cursor-grabbing"
                style={{ left: `${danio.coordX}%`, top: `${danio.coordY}%` }}
                onPointerDown={(evento) => {
                  if (soloLectura || !onMover) return
                  evento.stopPropagation()
                  evento.preventDefault()
                  evento.currentTarget.setPointerCapture(evento.pointerId)
                  arrastre.current = { id: danio.idDanio, x: danio.coordX, y: danio.coordY }
                }}
                onPointerMove={(evento) => mover(evento, danio.idDanio)}
                onPointerUp={(evento) => soltar(evento, danio.idDanio)}
              >
                {indice}
              </button>
            )
          })}
        </div>
      </div>
    </div>
  )
}

export function CanvasDanios({ danios, tipo, soloLectura = false, onMarcar, onMover, onMoverFin }: Props) {
  const etiquetaTipo = (tipo ?? 'Sedán').toString()

  return (
    <div className="space-y-3">
      <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <div className="space-y-3">
          <Vista vista="frontal" aspect="120 / 100" viewBox="0 0 120 100" danios={danios}
            soloLectura={soloLectura} onMarcar={onMarcar} onMover={onMover} onMoverFin={onMoverFin}>
            <Frontal />
          </Vista>
          <Vista vista="trasera" aspect="120 / 100" viewBox="0 0 120 100" danios={danios}
            soloLectura={soloLectura} onMarcar={onMarcar} onMover={onMover} onMoverFin={onMoverFin}>
            <Trasera />
          </Vista>
        </div>

        <div className="space-y-3">
          <Vista vista="lateral_izquierda" aspect="160 / 90" viewBox="0 0 160 90" danios={danios}
            soloLectura={soloLectura} onMarcar={onMarcar} onMover={onMover} onMoverFin={onMoverFin}>
            <Lateral />
          </Vista>
          <Vista vista="lateral_derecha" aspect="160 / 90" viewBox="0 0 160 90" danios={danios}
            soloLectura={soloLectura} onMarcar={onMarcar} onMover={onMover} onMoverFin={onMoverFin}>
            <Lateral espejo />
          </Vista>
        </div>

        <div className="space-y-3">
          <Vista vista="superior" aspect="110 / 150" viewBox="0 0 110 150" danios={danios}
            maxAlto={470} soloLectura={soloLectura} onMarcar={onMarcar} onMover={onMover} onMoverFin={onMoverFin}>
            <Superior />
          </Vista>
          <div className="rounded-lg border border-dashed border-[#c9d3e0] bg-[#f7f5ee] p-3 text-[11px] text-[#1f3a5f]">
            <p className="font-bold uppercase tracking-wide">Ficha de inspección</p>
            <p className="mt-1">Vehículo: {etiquetaTipo}</p>
            <p className="mt-1">Toca la vista donde esté el daño. Usa + / − para acercar y arrastra un marcador para corregirlo.</p>
          </div>
        </div>
      </div>
    </div>
  )
}
