import type { Danio } from '../types'

interface Props {
  danios: Danio[]
  soloLectura?: boolean
  onMarcar?: (coordX: number, coordY: number) => void
  onEliminar?: (idDanio: number) => void
}

export function CanvasDanios({ danios, soloLectura = false, onMarcar, onEliminar }: Props) {
  return (
    <div
      role={soloLectura ? 'img' : 'button'}
      aria-label="Esquema del vehículo para marcar daños"
      tabIndex={soloLectura ? -1 : 0}
      className={`relative aspect-[16/9] w-full select-none overflow-hidden rounded-xl border border-pc-border bg-pc-panel2 ${
        soloLectura ? '' : 'cursor-crosshair'
      }`}
      style={{ touchAction: 'none' }}
      onClick={(evento) => {
        if (soloLectura || !onMarcar) return
        const rect = evento.currentTarget.getBoundingClientRect()
        const x = ((evento.clientX - rect.left) / rect.width) * 100
        const y = ((evento.clientY - rect.top) / rect.height) * 100
        onMarcar(Number(x.toFixed(2)), Number(y.toFixed(2)))
      }}
    >
      <svg viewBox="0 0 160 90" className="absolute inset-0 h-full w-full" aria-hidden="true">
        <rect x="46" y="6" width="68" height="78" rx="20" fill="#0a1c2e" stroke="#2bc98c" strokeWidth="1.1" />
        <path d="M54 31 L61 18 H99 L106 31 Z" fill="#14344c" stroke="#3ee0a1" strokeWidth="0.8" />
        <path d="M54 60 L61 73 H99 L106 60 Z" fill="#14344c" stroke="#3ee0a1" strokeWidth="0.8" />
        <rect x="53" y="36" width="54" height="21" rx="5" fill="#0d2438" stroke="#2bc98c" strokeWidth="0.6" />
        <rect x="40" y="16" width="6" height="14" rx="2" fill="#3ee0a1" opacity="0.5" />
        <rect x="114" y="16" width="6" height="14" rx="2" fill="#3ee0a1" opacity="0.5" />
        <rect x="40" y="60" width="6" height="14" rx="2" fill="#3ee0a1" opacity="0.5" />
        <rect x="114" y="60" width="6" height="14" rx="2" fill="#3ee0a1" opacity="0.5" />
      </svg>

      {danios.map((danio, indice) => (
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
          {indice + 1}
        </button>
      ))}
    </div>
  )
}
