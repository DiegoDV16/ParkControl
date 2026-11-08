import type { VistaInspeccion } from './tipos'

const TINTA = '#334155'
const CARROCERIA = '#f8fafc'
const VIDRIO = '#cbd5e1'

interface Props {
  vista: VistaInspeccion
}

function Frontal({ trasera = false }: { trasera?: boolean }) {
  return (
    <svg viewBox="0 0 200 160" width="200" height="160" xmlns="http://www.w3.org/2000/svg"
      className="h-full w-full" style={{ pointerEvents: 'none' }} aria-hidden="true">
      <g stroke={TINTA} strokeWidth="2.5" fill={CARROCERIA} strokeLinejoin="round" strokeLinecap="round">
        <path d="M34 46 Q34 34 46 34 H154 Q166 34 166 46 V120 H34 Z" />
        <path d="M58 46 L72 20 H128 L142 46 Z" fill={VIDRIO} fillOpacity="0.4" />
        <path d="M46 60 H154" fill="none" strokeWidth="1.5" />
        <path d="M34 100 H166" fill="none" strokeWidth="1.5" />
        <rect x="42" y="70" width="34" height="14" rx="4" fill={trasera ? '#fecaca' : CARROCERIA} />
        <rect x="124" y="70" width="34" height="14" rx="4" fill={trasera ? '#fecaca' : CARROCERIA} />
        <rect x="82" y="70" width="36" height="18" rx="3" />
        <path d="M86 76 H114 M86 82 H114" fill="none" strokeWidth="1.5" />
        <rect x="34" y="112" width="132" height="10" rx="4" />
        <rect x="88" y="114" width="24" height="6" rx="2" />
        <path d="M26 62 q-8 2 -6 12 l8 -2 Z" />
        <path d="M174 62 q8 2 6 12 l-8 -2 Z" />
        <rect x="44" y="120" width="18" height="6" rx="2" fill={TINTA} />
        <rect x="138" y="120" width="18" height="6" rx="2" fill={TINTA} />
      </g>
    </svg>
  )
}

function Lateral() {
  return (
    <svg viewBox="0 0 320 140" width="320" height="140" xmlns="http://www.w3.org/2000/svg"
      className="h-full w-full" style={{ pointerEvents: 'none' }} aria-hidden="true">
      <g stroke={TINTA} strokeWidth="2.5" fill={CARROCERIA} strokeLinejoin="round" strokeLinecap="round">
        <path d="M18 100 C18 84 28 76 50 72 L74 46 C86 32 108 24 140 24 H196 C226 24 246 32 258 46 L278 54 C298 58 306 68 306 82 V100 Z" />
        <path d="M84 44 L98 30 H148 V44 Z" fill={VIDRIO} fillOpacity="0.4" />
        <path d="M156 30 H196 L216 44 H156 Z" fill={VIDRIO} fillOpacity="0.4" />
        <line x1="152" y1="30" x2="152" y2="100" strokeWidth="1.5" />
        <line x1="216" y1="44" x2="216" y2="100" strokeWidth="1.5" />
        <line x1="30" y1="86" x2="290" y2="86" strokeWidth="1.2" />
        <path d="M164 58 H176 M224 58 H236" strokeWidth="1.8" fill="none" />
        <circle cx="92" cy="100" r="22" fill={CARROCERIA} />
        <circle cx="92" cy="100" r="10" fill={VIDRIO} fillOpacity="0.4" />
        <circle cx="248" cy="100" r="22" fill={CARROCERIA} />
        <circle cx="248" cy="100" r="10" fill={VIDRIO} fillOpacity="0.4" />
        <rect x="292" y="64" width="14" height="8" rx="2" />
        <rect x="18" y="66" width="12" height="8" rx="2" fill={VIDRIO} fillOpacity="0.4" />
        <path d="M212 44 q10 -4 12 4 l-6 6" fill={CARROCERIA} />
      </g>
    </svg>
  )
}

function Superior() {
  return (
    <svg viewBox="0 0 160 260" width="160" height="260" xmlns="http://www.w3.org/2000/svg"
      className="h-full w-full" style={{ pointerEvents: 'none' }} aria-hidden="true">
      <g stroke={TINTA} strokeWidth="2.5" fill={CARROCERIA} strokeLinejoin="round" strokeLinecap="round">
        <rect x="40" y="14" width="80" height="232" rx="30" />
        <path d="M54 66 L64 34 H96 L106 66 Z" />
        <path d="M54 78 L64 60 H96 L106 78 Z" fill={VIDRIO} fillOpacity="0.4" />
        <rect x="60" y="88" width="40" height="72" rx="12" />
        <path d="M54 172 L64 196 H96 L106 172 Z" fill={VIDRIO} fillOpacity="0.4" />
        <path d="M54 210 H106" strokeWidth="1.5" />
        <path d="M54 222 H106" strokeWidth="1.5" />
        <path d="M30 66 q-8 2 -6 12 l8 -2 Z" />
        <path d="M130 66 q8 2 6 12 l-8 -2 Z" />
        <rect x="34" y="54" width="8" height="22" rx="3" />
        <rect x="118" y="54" width="8" height="22" rx="3" />
        <rect x="34" y="180" width="8" height="22" rx="3" />
        <rect x="118" y="180" width="8" height="22" rx="3" />
      </g>
    </svg>
  )
}

export function VistaVehiculo({ vista }: Props) {
  if (vista === 'frontal') return <Frontal />
  if (vista === 'trasera') return <Frontal trasera />
  if (vista === 'lateral_derecha') {
    return <div className="h-full w-full" style={{ transform: 'scaleX(-1)' }}><Lateral /></div>
  }
  if (vista === 'lateral_izquierda') return <Lateral />
  return <Superior />
}
