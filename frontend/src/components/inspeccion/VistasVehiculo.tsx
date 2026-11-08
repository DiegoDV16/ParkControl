import type { VistaInspeccion } from './tipos'

interface VistaProps {
  className?: string
}

export function CarTopView({ className = 'h-full w-full' }: VistaProps) {
  return (
    <svg viewBox="0 0 200 420" fill="none" xmlns="http://www.w3.org/2000/svg"
      className={className} style={{ pointerEvents: 'none' }} aria-hidden="true">
      <path
        d="M50 35 C50 15, 150 15, 150 35 L156 85 C164 95, 168 115, 168 145 L168 315 C168 345, 164 370, 154 390 C145 408, 55 408, 46 390 C36 370, 32 345, 32 315 L32 145 C32 115, 36 95, 44 85 Z"
        stroke="#334155" strokeWidth="2.5" fill="#f8fafc"
      />
      <path d="M30 110 C20 110, 15 120, 20 132 C26 136, 32 132, 32 125 Z" stroke="#334155" strokeWidth="2" fill="#cbd5e1" />
      <path d="M170 110 C180 110, 185 120, 180 132 C174 136, 168 132, 168 125 Z" stroke="#334155" strokeWidth="2" fill="#cbd5e1" />
      <path d="M48 90 C70 85, 130 85, 152 90" stroke="#64748b" strokeWidth="1.5" strokeDasharray="3 3" />
      <path d="M44 105 C70 98, 130 98, 156 105 L146 160 C125 155, 75 155, 54 160 Z" stroke="#334155" strokeWidth="2" fill="#cbd5e1" fillOpacity="0.45" />
      <path d="M54 168 C75 164, 125 164, 146 168 L144 275 C125 279, 75 279, 56 275 Z" stroke="#475569" strokeWidth="1.5" fill="#ffffff" />
      <path d="M54 285 C75 280, 125 280, 146 285 L152 330 C130 325, 70 325, 48 330 Z" stroke="#334155" strokeWidth="2" fill="#cbd5e1" fillOpacity="0.45" />
      <path d="M46 345 C70 340, 130 340, 154 345" stroke="#64748b" strokeWidth="1.5" strokeDasharray="3 3" />
    </svg>
  )
}

export function CarSideView({ className = 'h-full w-full', flip = false }: VistaProps & { flip?: boolean }) {
  return (
    <svg viewBox="0 0 500 170" fill="none" xmlns="http://www.w3.org/2000/svg"
      className={className} style={{ pointerEvents: 'none' }} aria-hidden="true">
      <g transform={flip ? 'translate(500 0) scale(-1 1)' : undefined}>
        <path
          d="M25 125 C30 100, 48 95, 80 95 L145 92 C170 70, 205 40, 250 40 L345 40 C375 40, 410 75, 435 90 L465 94 C478 95, 485 105, 485 118 L480 132 C470 134, 460 134, 452 134 C446 115, 425 102, 405 102 C385 102, 364 115, 358 134 L180 134 C174 115, 153 102, 133 102 C113 102, 92 115, 86 134 L35 134 C25 134, 20 130, 25 125 Z"
          stroke="#334155" strokeWidth="2.5" fill="#f8fafc"
        />
        <circle cx="133" cy="134" r="28" stroke="#334155" strokeWidth="2.5" fill="#0f172a" />
        <circle cx="133" cy="134" r="14" stroke="#94a3b8" strokeWidth="2" fill="#e2e8f0" />
        <circle cx="405" cy="134" r="28" stroke="#334155" strokeWidth="2.5" fill="#0f172a" />
        <circle cx="405" cy="134" r="14" stroke="#94a3b8" strokeWidth="2" fill="#e2e8f0" />
        <path d="M172 90 L240 50 L275 50 L275 90 Z" stroke="#334155" strokeWidth="1.5" fill="#cbd5e1" fillOpacity="0.45" />
        <path d="M285 50 L342 50 C365 50, 392 78, 405 90 L285 90 Z" stroke="#334155" strokeWidth="1.5" fill="#cbd5e1" fillOpacity="0.45" />
        <path d="M280 48 L280 130" stroke="#64748b" strokeWidth="1.5" />
        <path d="M170 92 L195 132" stroke="#64748b" strokeWidth="1.5" />
        <path d="M408 92 L385 132" stroke="#64748b" strokeWidth="1.5" />
        <rect x="250" y="96" width="14" height="3" rx="1.5" fill="#334155" />
        <rect x="295" y="96" width="14" height="3" rx="1.5" fill="#334155" />
      </g>
    </svg>
  )
}

export function CarFrontView({ className = 'h-full w-full' }: VistaProps) {
  return (
    <svg viewBox="0 0 240 180" fill="none" xmlns="http://www.w3.org/2000/svg"
      className={className} style={{ pointerEvents: 'none' }} aria-hidden="true">
      <path d="M60 45 C75 40, 165 40, 180 45 L198 95 L42 95 Z" stroke="#334155" strokeWidth="2.5" fill="#cbd5e1" fillOpacity="0.45" />
      <path d="M38 85 C26 85, 22 92, 28 102 C34 104, 40 98, 40 90 Z" stroke="#334155" strokeWidth="1.5" fill="#cbd5e1" />
      <path d="M202 85 C214 85, 218 92, 212 102 C206 104, 200 98, 200 90 Z" stroke="#334155" strokeWidth="1.5" fill="#cbd5e1" />
      <path d="M38 95 C38 90, 202 90, 202 95 L208 120 C212 135, 208 152, 200 156 L40 156 C32 152, 28 135, 32 120 Z" stroke="#334155" strokeWidth="2.5" fill="#f8fafc" />
      <path d="M44 105 L74 105 C72 118, 55 120, 44 118 Z" stroke="#334155" strokeWidth="1.5" fill="#fef08a" />
      <path d="M196 105 L166 105 C168 118, 185 120, 196 118 Z" stroke="#334155" strokeWidth="1.5" fill="#fef08a" />
      <rect x="85" y="106" width="70" height="18" rx="3" stroke="#475569" strokeWidth="1.5" fill="#1e293b" />
      <rect x="95" y="134" width="50" height="16" rx="2" stroke="#334155" strokeWidth="1.5" fill="#ffffff" />
      <rect x="36" y="152" width="22" height="18" rx="2" fill="#0f172a" />
      <rect x="182" y="152" width="22" height="18" rx="2" fill="#0f172a" />
    </svg>
  )
}

export function CarRearView({ className = 'h-full w-full' }: VistaProps) {
  return (
    <svg viewBox="0 0 240 180" fill="none" xmlns="http://www.w3.org/2000/svg"
      className={className} style={{ pointerEvents: 'none' }} aria-hidden="true">
      <path d="M60 45 C75 40, 165 40, 180 45 L196 90 L44 90 Z" stroke="#334155" strokeWidth="2.5" fill="#cbd5e1" fillOpacity="0.45" />
      <path d="M38 85 L26 88 L30 96 L40 92 Z" stroke="#334155" strokeWidth="1.5" fill="#94a3b8" />
      <path d="M202 85 L214 88 L210 96 L200 92 Z" stroke="#334155" strokeWidth="1.5" fill="#94a3b8" />
      <path d="M38 90 L202 90 L208 120 C212 138, 206 154, 200 156 L40 156 C34 154, 28 138, 32 120 Z" stroke="#334155" strokeWidth="2.5" fill="#f8fafc" />
      <path d="M42 102 L72 104 C70 116, 54 118, 42 114 Z" stroke="#334155" strokeWidth="1.5" fill="#ef4444" />
      <path d="M198 102 L168 104 C170 116, 186 118, 198 114 Z" stroke="#334155" strokeWidth="1.5" fill="#ef4444" />
      <rect x="95" y="112" width="50" height="18" rx="2" stroke="#334155" strokeWidth="1.5" fill="#ffffff" />
      <line x1="45" y1="138" x2="195" y2="138" stroke="#64748b" strokeWidth="1.5" />
      <rect x="36" y="152" width="22" height="18" rx="2" fill="#0f172a" />
      <rect x="182" y="152" width="22" height="18" rx="2" fill="#0f172a" />
    </svg>
  )
}

export function VistaVehiculo({ vista }: { vista: VistaInspeccion }) {
  if (vista === 'frontal') return <CarFrontView />
  if (vista === 'trasera') return <CarRearView />
  if (vista === 'lateral_izquierda') return <CarSideView />
  if (vista === 'lateral_derecha') return <CarSideView flip />
  return <CarTopView />
}
