import { useRef, useState, type PointerEvent as ReactPointerEvent } from 'react'
import type { Danio, VistaDanio } from '../types'

export const ETIQUETAS_VISTA: Record<VistaDanio, string> = {
  superior: 'Superior',
  lateral_izquierda: 'Lateral izquierda',
  lateral_derecha: 'Lateral derecha',
  frontal: 'Frontal',
  trasera: 'Trasera',
}

type EstiloVehiculo = 'sedan' | 'hatchback' | 'suv' | 'camioneta'

function estiloDe(tipo?: string | null): EstiloVehiculo {
  const valor = (tipo ?? '').toUpperCase()
  if (valor.includes('HATCH')) return 'hatchback'
  if (valor.includes('SUV')) return 'suv'
  if (valor.includes('CAMIONETA') || valor.includes('PICKUP')) return 'camioneta'
  return 'sedan'
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

function Superior({ estilo }: { estilo: EstiloVehiculo }) {
  const medidas = estilo === 'suv' || estilo === 'camioneta'
    ? { x: 33, y: 8, w: 54, h: 84, rx: 14 }
    : estilo === 'hatchback'
      ? { x: 36, y: 14, w: 48, h: 72, rx: 16 }
      : { x: 35, y: 10, w: 50, h: 80, rx: 16 }

  return (
    <g stroke="#3ee0a1" strokeWidth="1.4" fill="#0d2438" strokeLinejoin="round">
      <rect x={medidas.x} y={medidas.y} width={medidas.w} height={medidas.h} rx={medidas.rx} />
      <path d={`M${medidas.x + 7} ${medidas.y + 28} L${medidas.x + 13} ${medidas.y + 16} H${medidas.x + medidas.w - 13} L${medidas.x + medidas.w - 7} ${medidas.y + 28} Z`} fill="#14344c" />
      <rect x={medidas.x + 9} y={medidas.y + 32} width={medidas.w - 18} height={estilo === 'camioneta' ? 22 : 30} rx="4" fill="#14344c" />
      {estilo === 'camioneta' ? (
        <rect x={medidas.x + 9} y={medidas.y + 60} width={medidas.w - 18} height={20} rx="3" fill="#0a1c2e" />
      ) : (
        <path d={`M${medidas.x + 7} ${medidas.y + 68} L${medidas.x + 13} ${medidas.y + 80} H${medidas.x + medidas.w - 13} L${medidas.x + medidas.w - 7} ${medidas.y + 68} Z`} fill="#14344c" />
      )}
      <rect x={medidas.x - 8} y={medidas.y + 28} width="8" height="13" rx="3" fill="#14344c" />
      <rect x={medidas.x + medidas.w} y={medidas.y + 28} width="8" height="13" rx="3" fill="#14344c" />
    </g>
  )
}

function Lateral({ estilo, espejo = false }: { estilo: EstiloVehiculo; espejo?: boolean }) {
  return (
    <g transform={espejo ? 'translate(120 0) scale(-1 1)' : undefined}>
      {estilo === 'sedan' && (
        <g stroke="#3ee0a1" strokeWidth="1.4" fill="none" strokeLinejoin="round">
          <path d="M8 68 C8 54 16 48 28 46 L40 30 H86 L98 46 C110 48 118 54 118 68 V78 H8 Z" fill="#0d2438" />
          <path d="M40 30 L48 18 H80 L88 30 Z" fill="#14344c" />
          <path d="M46 30 L52 21 H64 V30 Z" fill="#0a1c2e" strokeWidth="0.7" />
          <path d="M66 30 H80 L84 21 H68 Z" fill="#0a1c2e" strokeWidth="0.7" />
          <line x1="65" y1="31" x2="65" y2="74" strokeWidth="0.9" />
          <circle cx="34" cy="78" r="11" fill="#0a1c2e" strokeWidth="1.2" />
          <circle cx="34" cy="78" r="5" fill="#14344c" strokeWidth="0.8" />
          <circle cx="98" cy="78" r="11" fill="#0a1c2e" strokeWidth="1.2" />
          <circle cx="98" cy="78" r="5" fill="#14344c" strokeWidth="0.8" />
          <rect x="114" y="52" width="4" height="8" rx="1.5" fill="#3ee0a1" />
        </g>
      )}

      {estilo === 'hatchback' && (
        <g stroke="#3ee0a1" strokeWidth="1.4" fill="none" strokeLinejoin="round">
          <path d="M8 68 C8 54 16 48 28 46 L42 32 H96 L108 46 C115 48 118 54 118 68 V78 H8 Z" fill="#0d2438" />
          <path d="M42 32 L50 20 H90 L98 32 Z" fill="#14344c" />
          <path d="M48 32 L54 23 H66 V32 Z" fill="#0a1c2e" strokeWidth="0.7" />
          <path d="M68 32 H88 L92 23 H70 Z" fill="#0a1c2e" strokeWidth="0.7" />
          <line x1="67" y1="33" x2="67" y2="74" strokeWidth="0.9" />
          <circle cx="34" cy="78" r="11" fill="#0a1c2e" strokeWidth="1.2" />
          <circle cx="34" cy="78" r="5" fill="#14344c" strokeWidth="0.8" />
          <circle cx="98" cy="78" r="11" fill="#0a1c2e" strokeWidth="1.2" />
          <circle cx="98" cy="78" r="5" fill="#14344c" strokeWidth="0.8" />
          <rect x="114" y="52" width="4" height="8" rx="1.5" fill="#3ee0a1" />
        </g>
      )}

      {estilo === 'suv' && (
        <g stroke="#3ee0a1" strokeWidth="1.4" fill="none" strokeLinejoin="round">
          <path d="M8 62 C8 48 16 42 28 40 L38 24 H92 L104 40 C114 42 118 50 118 62 V78 H8 Z" fill="#0d2438" />
          <path d="M38 24 L46 12 H86 L94 24 Z" fill="#14344c" />
          <path d="M44 24 L50 15 H62 V24 Z" fill="#0a1c2e" strokeWidth="0.7" />
          <path d="M64 24 H84 L88 15 H66 Z" fill="#0a1c2e" strokeWidth="0.7" />
          <line x1="63" y1="25" x2="63" y2="72" strokeWidth="0.9" />
          <circle cx="34" cy="78" r="12" fill="#0a1c2e" strokeWidth="1.2" />
          <circle cx="34" cy="78" r="5" fill="#14344c" strokeWidth="0.8" />
          <circle cx="98" cy="78" r="12" fill="#0a1c2e" strokeWidth="1.2" />
          <circle cx="98" cy="78" r="5" fill="#14344c" strokeWidth="0.8" />
          <rect x="114" y="46" width="4" height="9" rx="1.5" fill="#3ee0a1" />
        </g>
      )}

      {estilo === 'camioneta' && (
        <g stroke="#3ee0a1" strokeWidth="1.4" fill="none" strokeLinejoin="round">
          <path d="M8 70 V58 C8 48 14 44 24 42 L34 28 H62 L72 42 H80 V46 H118 V72 H8 Z" fill="#0d2438" />
          <path d="M34 28 L42 18 H58 L64 28 Z" fill="#14344c" />
          <path d="M40 28 L46 20 H58 V28 Z" fill="#0a1c2e" strokeWidth="0.7" />
          <line x1="78" y1="46" x2="78" y2="70" strokeWidth="0.9" />
          <line x1="80" y1="50" x2="116" y2="50" strokeWidth="0.8" />
          <circle cx="30" cy="72" r="11" fill="#0a1c2e" strokeWidth="1.2" />
          <circle cx="30" cy="72" r="5" fill="#14344c" strokeWidth="0.8" />
          <circle cx="100" cy="72" r="11" fill="#0a1c2e" strokeWidth="1.2" />
          <circle cx="100" cy="72" r="5" fill="#14344c" strokeWidth="0.8" />
          <rect x="112" y="54" width="4" height="8" rx="1.5" fill="#3ee0a1" />
        </g>
      )}
    </g>
  )
}

function Frontal({ estilo, trasero = false }: { estilo: EstiloVehiculo; trasero?: boolean }) {
  const medidas = estilo === 'suv' || estilo === 'camioneta'
    ? { x: 18, y: 12, w: 84, h: 78 }
    : estilo === 'hatchback'
      ? { x: 24, y: 22, w: 72, h: 64 }
      : { x: 22, y: 20, w: 76, h: 68 }

  const { x, y, w, h } = medidas

  return (
    <g stroke="#3ee0a1" strokeWidth="1.4" fill="none" strokeLinejoin="round">
      <path d={`M${x} ${y + 8} Q${x} ${y} ${x + 8} ${y} H${x + w - 8} Q${x + w} ${y} ${x + w} ${y + 8} V${y + h} H${x} Z`} fill="#0d2438" />
      <path d={`M${x + 8} ${y + 18} L${x + 16} ${y + 4} H${x + w - 16} L${x + w - 8} ${y + 18} Z`} fill="#14344c" />
      <rect x={x + 4} y={y + 26} width="18" height="7" rx="3" fill={trasero ? '#ff6b6b' : '#3ee0a1'} opacity="0.6" />
      <rect x={x + w - 22} y={y + 26} width="18" height="7" rx="3" fill={trasero ? '#ff6b6b' : '#3ee0a1'} opacity="0.6" />
      <rect x={x + w / 2 - 10} y={y + 26} width="20" height="8" rx="2" fill="#14344c" />
      <line x1={x + 6} y1={y + 44} x2={x + w - 6} y2={y + 44} strokeWidth="0.8" />
      <rect x={x + 2} y={y + h - 10} width={w - 4} height="7" rx="3" fill="#14344c" />
    </g>
  )
}

interface VistaProps {
  vista: VistaDanio
  aspect: string
  estilo: EstiloVehiculo
  danios: Danio[]
  soloLectura: boolean
  onMarcar?: Props['onMarcar']
  onMover?: Props['onMover']
  onMoverFin?: Props['onMoverFin']
}

function Vista({ vista, aspect, estilo, danios, soloLectura, onMarcar, onMover, onMoverFin }: VistaProps) {
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
    <div className="rounded-lg border border-pc-border bg-pc-panel/60 p-2">
      <div className="relative">
        <div className="absolute right-1 top-1 z-10 flex items-center gap-1 rounded-md bg-pc-panel/90 p-1">
          <button type="button" className="h-6 w-6 rounded border border-pc-border text-xs text-pc-muted"
            onClick={() => setZoom((valor) => Math.max(1, Number((valor - 0.5).toFixed(1))))} aria-label="Alejar">
            −
          </button>
          <span className="min-w-8 text-center text-[10px] text-pc-muted">{zoom}x</span>
          <button type="button" className="h-6 w-6 rounded border border-pc-border text-xs text-pc-muted"
            onClick={() => setZoom((valor) => Math.min(3, Number((valor + 0.5).toFixed(1))))} aria-label="Acercar">
            +
          </button>
        </div>

        <div className="overflow-auto" style={{ maxHeight: 320 }}>
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
            <svg viewBox="0 0 120 100" preserveAspectRatio="none" className="absolute inset-0 h-full w-full" aria-hidden="true">
              {vista === 'superior' && <Superior estilo={estilo} />}
              {(vista === 'lateral_izquierda' || vista === 'lateral_derecha') && (
                <Lateral estilo={estilo} espejo={vista === 'lateral_derecha'} />
              )}
              {vista === 'frontal' && <Frontal estilo={estilo} />}
              {vista === 'trasera' && <Frontal estilo={estilo} trasero />}
            </svg>

            {propios.map((danio) => {
              const indice = danios.indexOf(danio) + 1
              return (
                <button
                  key={danio.idDanio}
                  type="button"
                  title={`${danio.tipoDanio}${danio.detalle ? `: ${danio.detalle}` : ''}`}
                  className="absolute flex h-6 w-6 -translate-x-1/2 -translate-y-1/2 cursor-grab touch-none items-center justify-center rounded-full border-2 border-white bg-pc-danger text-[11px] font-bold text-[#1a0505] shadow active:cursor-grabbing"
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
      <p className="mt-1 text-center text-[10px] font-semibold uppercase tracking-wide text-pc-muted">
        {ETIQUETAS_VISTA[vista]}
      </p>
    </div>
  )
}

export function CanvasDanios({ danios, tipo, soloLectura = false, onMarcar, onMover, onMoverFin }: Props) {
  const estilo = estiloDe(tipo)

  const vistas: Array<{ vista: VistaDanio; aspect: string }> = [
    { vista: 'superior', aspect: '120 / 130' },
    { vista: 'frontal', aspect: '120 / 100' },
    { vista: 'lateral_izquierda', aspect: '120 / 100' },
    { vista: 'lateral_derecha', aspect: '120 / 100' },
    { vista: 'trasera', aspect: '120 / 100' },
  ]

  return (
    <div className="grid gap-3 sm:grid-cols-2">
      {vistas.map(({ vista, aspect }) => (
        <Vista
          key={vista}
          vista={vista}
          aspect={aspect}
          estilo={estilo}
          danios={danios}
          soloLectura={soloLectura}
          onMarcar={onMarcar}
          onMover={onMover}
          onMoverFin={onMoverFin}
        />
      ))}

      <div className="flex flex-col justify-center rounded-lg border border-dashed border-pc-border p-4 text-xs text-pc-muted">
        <p className="font-semibold text-pc-text">Ficha de inspección</p>
        <p className="mt-2">Usa + / − para acercar la vista y arrastra un marcador para corregir su posición.</p>
        <p className="mt-2">Los daños se eliminan o editan desde la lista.</p>
      </div>
    </div>
  )
}
