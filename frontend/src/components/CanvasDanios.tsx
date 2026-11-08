import type { Danio } from '../types'

interface Props {
  danios: Danio[]
  soloLectura?: boolean
  onMarcar?: (coordX: number, coordY: number) => void
  onEliminar?: (idDanio: number) => void
}

/**
 * Ficha de inspección con cuatro vistas del vehículo: superior, lateral,
 * frontal y trasera. Los daños se guardan como coordenadas relativas (0-100)
 * sobre la ficha completa.
 */
export function CanvasDanios({ danios, soloLectura = false, onMarcar, onEliminar }: Props) {
  return (
    <div
      role={soloLectura ? 'img' : 'button'}
      aria-label="Ficha del vehículo para marcar daños"
      tabIndex={soloLectura ? -1 : 0}
      className={`relative w-full select-none overflow-hidden rounded-xl border border-pc-border bg-pc-panel2 ${
        soloLectura ? '' : 'cursor-crosshair'
      }`}
      style={{ touchAction: 'none', aspectRatio: '320 / 300' }}
      onClick={(evento) => {
        if (soloLectura || !onMarcar) return
        const rect = evento.currentTarget.getBoundingClientRect()
        const x = ((evento.clientX - rect.left) / rect.width) * 100
        const y = ((evento.clientY - rect.top) / rect.height) * 100
        onMarcar(Number(x.toFixed(2)), Number(y.toFixed(2)))
      }}
    >
      <svg viewBox="0 0 320 300" className="absolute inset-0 h-full w-full" aria-hidden="true">
        <rect x="0" y="0" width="320" height="300" fill="#0a1c2e" />

        {/* separadores */}
        <line x1="160" y1="8" x2="160" y2="292" stroke="#14344c" strokeWidth="1" />
        <line x1="8" y1="150" x2="312" y2="150" stroke="#14344c" strokeWidth="1" />

        {/* ---------- VISTA SUPERIOR ---------- */}
        <g stroke="#3ee0a1" strokeWidth="1.4" fill="none" strokeLinejoin="round">
          <rect x="52" y="22" width="56" height="106" rx="18" fill="#0d2438" />
          <path d="M60 52 L66 38 H94 L100 52 Z" fill="#14344c" />
          <rect x="62" y="58" width="36" height="40" rx="5" fill="#14344c" />
          <path d="M60 108 L66 122 H94 L100 108 Z" fill="#14344c" />
          <rect x="44" y="52" width="8" height="15" rx="3" fill="#14344c" />
          <rect x="108" y="52" width="8" height="15" rx="3" fill="#14344c" />
          <path d="M64 24 H96" stroke="#2bc98c" strokeWidth="0.8" />
          <path d="M64 126 H96" stroke="#2bc98c" strokeWidth="0.8" />
        </g>

        {/* ---------- VISTA LATERAL ---------- */}
        <g stroke="#3ee0a1" strokeWidth="1.4" fill="none" strokeLinejoin="round">
          <path
            d="M176 92 C176 80 184 74 196 72 L206 54 H268 L280 72 C292 74 300 80 300 92 L300 104 H176 Z"
            fill="#0d2438"
          />
          <path d="M206 54 L214 44 H260 L268 54 Z" fill="#14344c" />
          <path d="M212 56 L218 48 H238 V56 Z" fill="#0a1c2e" />
          <path d="M242 56 H258 L262 48 H244 Z" fill="#0a1c2e" />
          <line x1="240" y1="56" x2="240" y2="98" strokeWidth="0.9" />
          <path d="M196 72 H206 M280 72 H292" strokeWidth="0.9" />
          <circle cx="202" cy="104" r="11" fill="#0a1c2e" />
          <circle cx="202" cy="104" r="5" fill="#14344c" />
          <circle cx="276" cy="104" r="11" fill="#0a1c2e" />
          <circle cx="276" cy="104" r="5" fill="#14344c" />
          <rect x="296" y="82" width="4" height="9" rx="1.5" fill="#3ee0a1" />
          <path d="M178 96 H186" strokeWidth="0.9" />
        </g>

        {/* ---------- VISTA FRONTAL ---------- */}
        <g stroke="#3ee0a1" strokeWidth="1.4" fill="none" strokeLinejoin="round">
          <path d="M34 192 Q34 184 42 184 H118 Q126 184 126 192 V252 H34 Z" fill="#0d2438" />
          <path d="M42 206 L48 192 H112 L118 206 Z" fill="#14344c" />
          <rect x="38" y="214" width="20" height="8" rx="3" fill="#3ee0a1" opacity="0.55" />
          <rect x="102" y="214" width="20" height="8" rx="3" fill="#3ee0a1" opacity="0.55" />
          <rect x="66" y="214" width="28" height="10" rx="2" fill="#14344c" />
          <rect x="36" y="242" width="88" height="7" rx="3" fill="#14344c" />
          <line x1="48" y1="226" x2="48" y2="240" strokeWidth="0.8" />
          <line x1="112" y1="226" x2="112" y2="240" strokeWidth="0.8" />
        </g>

        {/* ---------- VISTA TRASERA ---------- */}
        <g stroke="#3ee0a1" strokeWidth="1.4" fill="none" strokeLinejoin="round">
          <path d="M194 192 Q194 184 202 184 H278 Q286 184 286 192 V252 H194 Z" fill="#0d2438" />
          <path d="M202 206 L208 192 H272 L278 206 Z" fill="#14344c" />
          <rect x="198" y="214" width="18" height="8" rx="3" fill="#ff6b6b" opacity="0.6" />
          <rect x="264" y="214" width="18" height="8" rx="3" fill="#ff6b6b" opacity="0.6" />
          <line x1="208" y1="226" x2="272" y2="226" strokeWidth="0.9" />
          <rect x="196" y="242" width="88" height="7" rx="3" fill="#14344c" />
        </g>

        {/* etiquetas */}
        <g fill="#8fb2c6" fontSize="7" fontFamily="Segoe UI, sans-serif" textAnchor="middle">
          <text x="80" y="145">SUPERIOR</text>
          <text x="240" y="145">LATERAL</text>
          <text x="80" y="288">FRONTAL</text>
          <text x="240" y="288">TRASERA</text>
        </g>
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
