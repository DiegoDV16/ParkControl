import type { CSSProperties } from 'react'

interface BlueprintProps {
  className?: string
}

interface SideProps extends BlueprintProps {
  flip?: boolean
}

const sinEventos: CSSProperties = { pointerEvents: 'none' }

/**
 * Planos tÃ©cnicos de la ficha fÃ­sica de Estacionamientos Tepual Norte.
 * Trazo azul fino, carrocerÃ­a blanca y vidrios tenues.
 */
export function CarSideBlueprint({ className = 'h-full w-full', flip = false }: SideProps) {
  return (
    <svg
      viewBox="0 0 520 180"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      className={className}
      style={sinEventos}
      aria-hidden="true"
    >
      <g transform={flip ? 'translate(520 0) scale(-1 1)' : undefined}>
        {/* Silueta exterior / CarrocerÃ­a */}
        <path
          d="M20 126 L42 124 L60 92 L145 88 L188 44 L325 44 L382 86 L478 92 L498 100 L502 124 L482 130 L452 130 C445 110 424 96 400 96 C376 96 355 110 348 130 L188 130 C181 110 160 96 136 96 C112 96 91 110 84 130 L32 130 Z"
          stroke="#334155" strokeWidth="2.2" strokeLinejoin="round" fill="#f1f5f9"
        />
        {/* Rueda delantera */}
        <circle cx="136" cy="130" r="30" stroke="#334155" strokeWidth="2.5" fill="#f8fafc" />
        <circle cx="136" cy="130" r="20" stroke="#64748b" strokeWidth="1.8" />
        <circle cx="136" cy="130" r="8" stroke="#334155" strokeWidth="2" fill="#cbd5e1" />
        {/* Rueda trasera */}
        <circle cx="400" cy="130" r="30" stroke="#334155" strokeWidth="2.5" fill="#f8fafc" />
        <circle cx="400" cy="130" r="20" stroke="#64748b" strokeWidth="1.8" />
        <circle cx="400" cy="130" r="8" stroke="#334155" strokeWidth="2" fill="#cbd5e1" />

        {/* Ventanillas (Pilar A, B y C) */}
        <path d="M194 84 L252 84 L252 50 L195 50 Z" stroke="#334155" strokeWidth="1.5" fill="#e2e8f0" />
        <path d="M260 84 L322 84 L322 50 L260 50 Z" stroke="#334155" strokeWidth="1.5" fill="#e2e8f0" />
        <path d="M328 84 L368 84 L328 50 Z" stroke="#334155" strokeWidth="1.5" fill="#e2e8f0" />

        {/* LÃ­nea de moldura lateral y puertas */}
        <line x1="20" y1="104" x2="498" y2="104" stroke="#334155" strokeWidth="1.6" />
        <line x1="256" y1="48" x2="256" y2="128" stroke="#334155" strokeWidth="1.5" />
        <line x1="176" y1="88" x2="186" y2="128" stroke="#334155" strokeWidth="1.5" />
        <line x1="335" y1="86" x2="335" y2="128" stroke="#334155" strokeWidth="1.5" />

        {/* Manillas de puertas */}
        <rect x="230" y="90" width="14" height="4" rx="1.5" stroke="#334155" strokeWidth="1.2" fill="#94a3b8" />
        <rect x="270" y="90" width="14" height="4" rx="1.5" stroke="#334155" strokeWidth="1.2" fill="#94a3b8" />

        {/* Parachoques delantero y trasero */}
        <path d="M20 114 L55 114 L52 128 L24 128 Z" stroke="#334155" strokeWidth="1.5" fill="#e2e8f0" />
        <path d="M472 114 L500 114 L496 128 L470 128 Z" stroke="#334155" strokeWidth="1.5" fill="#e2e8f0" />
        {/* Foco delantero / intermitente */}
        <polygon points="54,92 78,92 74,104 50,104" stroke="#334155" strokeWidth="1.4" fill="#fef08a" />

        {/* Detalles técnicos: molduras, espejo, tapas y radios */}
        <line x1="80" y1="112" x2="455" y2="112" stroke="#94a3b8" strokeWidth="1" />
        <line x1="60" y1="94" x2="188" y2="90" stroke="#64748b" strokeWidth="1" />
        <circle cx="470" cy="104" r="4" stroke="#64748b" strokeWidth="1" />
        <path d="M188 62 q14 -4 18 4 l-8 6" stroke="#334155" strokeWidth="1.2" fill="#e2e8f0" />
        <g stroke="#64748b" strokeWidth="1">
          <line x1="136" y1="102" x2="136" y2="158" />
          <line x1="108" y1="130" x2="164" y2="130" />
          <line x1="116" y1="110" x2="156" y2="150" />
          <line x1="156" y1="110" x2="116" y2="150" />
          <line x1="400" y1="102" x2="400" y2="158" />
          <line x1="372" y1="130" x2="428" y2="130" />
          <line x1="380" y1="110" x2="420" y2="150" />
          <line x1="420" y1="110" x2="380" y2="150" />
        </g>
      </g>
    </svg>
  )
}

export function CarTopBlueprint({ className = 'h-full w-full' }: BlueprintProps) {
  return (
    <svg
      viewBox="0 0 200 440"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      className={className}
      style={sinEventos}
      aria-hidden="true"
    >
      {/* Contorno exterior */}
      <path
        d="M45 42 C45 18 155 18 155 42 L160 90 C166 102 170 125 170 150 L170 310 C170 340 166 368 158 395 C148 420 52 420 42 395 C34 368 30 340 30 310 L30 150 C30 125 34 102 40 90 Z"
        stroke="#334155" strokeWidth="2.2" strokeLinejoin="round" fill="#f1f5f9"
      />
      {/* Espejos laterales */}
      <path d="M28 116 C18 116 16 126 22 136 C28 138 32 134 32 126 Z" stroke="#334155" strokeWidth="1.8" fill="#e2e8f0" />
      <path d="M172 116 C182 116 184 126 178 136 C172 138 168 134 168 126 Z" stroke="#334155" strokeWidth="1.8" fill="#e2e8f0" />

      {/* CapÃ³ delantero */}
      <path d="M48 94 C75 90 125 90 152 94" stroke="#334155" strokeWidth="1.8" />
      <path d="M42 46 C75 42 125 42 158 46" stroke="#334155" strokeWidth="1.5" strokeDasharray="3 2" />

      {/* Parabrisas delantero */}
      <path d="M44 104 C75 98 125 98 156 104 L146 164 C125 158 75 158 54 164 Z" stroke="#334155" strokeWidth="2" fill="#e2e8f0" />

      {/* Techo y ventanillas laterales */}
      <rect x="54" y="172" width="92" height="106" rx="3" stroke="#334155" strokeWidth="1.8" fill="#f1f5f9" />
      <line x1="54" y1="225" x2="146" y2="225" stroke="#334155" strokeWidth="1.2" strokeDasharray="2 2" />

      {/* Luneta trasera */}
      <path d="M52 286 C75 280 125 280 148 286 L154 336 C130 330 70 330 46 336 Z" stroke="#334155" strokeWidth="2" fill="#e2e8f0" />

      {/* Maletero */}
      <path d="M44 348 C75 342 125 342 156 348" stroke="#334155" strokeWidth="1.8" />
    </svg>
  )
}

export function CarRearBlueprint({ className = 'h-full w-full' }: BlueprintProps) {
  return (
    <svg
      viewBox="0 0 240 180"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      className={className}
      style={sinEventos}
      aria-hidden="true"
    >
      {/* Luneta y techo */}
      <path d="M55 42 C75 38 165 38 185 42 L202 88 L38 88 Z" stroke="#334155" strokeWidth="2.2" fill="#e2e8f0" />
      {/* CarrocerÃ­a / Maletero */}
      <path d="M34 88 L206 88 L214 118 C216 136 210 152 202 154 L38 154 C30 152 24 136 26 118 Z" stroke="#334155" strokeWidth="2.2" strokeLinejoin="round" fill="#f1f5f9" />

      {/* Focos traseros rectangulares estriados */}
      <rect x="36" y="98" width="38" height="22" stroke="#334155" strokeWidth="1.8" fill="#ef4444" fillOpacity="0.85" />
      <line x1="48" y1="98" x2="48" y2="120" stroke="#ffffff" strokeWidth="1.2" />
      <line x1="60" y1="98" x2="60" y2="120" stroke="#ffffff" strokeWidth="1.2" />

      <rect x="166" y="98" width="38" height="22" stroke="#334155" strokeWidth="1.8" fill="#ef4444" fillOpacity="0.85" />
      <line x1="178" y1="98" x2="178" y2="120" stroke="#ffffff" strokeWidth="1.2" />
      <line x1="190" y1="98" x2="190" y2="120" stroke="#ffffff" strokeWidth="1.2" />

      {/* Porta patente */}
      <rect x="92" y="104" width="56" height="22" rx="2" stroke="#334155" strokeWidth="1.5" fill="#f8fafc" />
      {/* Parachoques trasero */}
      <rect x="30" y="128" width="180" height="18" rx="3" stroke="#334155" strokeWidth="1.8" fill="#e2e8f0" />
      {/* Ruedas visibles abajo */}
      <rect x="34" y="148" width="22" height="16" rx="2" fill="#1e293b" />
      <rect x="184" y="148" width="22" height="16" rx="2" fill="#1e293b" />

      {/* Detalles: tapa de maletero, luneta y escape */}
      <path d="M55 58 C75 53 165 53 185 58" stroke="#64748b" strokeWidth="1" />
      <line x1="62" y1="70" x2="178" y2="70" stroke="#64748b" strokeWidth="0.9" strokeDasharray="3 3" />
      <line x1="70" y1="78" x2="170" y2="78" stroke="#64748b" strokeWidth="0.8" strokeDasharray="2 3" />
      <rect x="116" y="144" width="30" height="8" rx="3" stroke="#64748b" strokeWidth="1" fill="#e2e8f0" />
      <line x1="30" y1="136" x2="210" y2="136" stroke="#94a3b8" strokeWidth="0.8" />
    </svg>
  )
}

export function CarFrontBlueprint({ className = 'h-full w-full' }: BlueprintProps) {
  return (
    <svg
      viewBox="0 0 240 180"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      className={className}
      style={sinEventos}
      aria-hidden="true"
    >
      {/* Parabrisas */}
      <path d="M55 42 C75 38 165 38 185 42 L202 88 L38 88 Z" stroke="#334155" strokeWidth="2.2" fill="#e2e8f0" />
      {/* CarrocerÃ­a frontal / CapÃ³ */}
      <path d="M34 88 L206 88 L214 118 C216 136 210 152 202 154 L38 154 C30 152 24 136 26 118 Z" stroke="#334155" strokeWidth="2.2" strokeLinejoin="round" fill="#f1f5f9" />

      {/* Faros delanteros clÃ¡sicos rectangulares */}
      <rect x="36" y="96" width="36" height="20" stroke="#334155" strokeWidth="1.8" fill="#fef08a" />
      <rect x="168" y="96" width="36" height="20" stroke="#334155" strokeWidth="1.8" fill="#fef08a" />

      {/* Parrilla central con rejilla */}
      <rect x="78" y="96" width="84" height="20" stroke="#334155" strokeWidth="1.8" fill="#1e293b" />
      <line x1="78" y1="106" x2="162" y2="106" stroke="#64748b" strokeWidth="1" />

      {/* Parachoques delantero */}
      <rect x="30" y="124" width="180" height="20" rx="3" stroke="#334155" strokeWidth="1.8" fill="#e2e8f0" />
      {/* Patente frontal */}
      <rect x="94" y="126" width="52" height="16" rx="2" stroke="#334155" strokeWidth="1.4" fill="#f1f5f9" />

      {/* Ruedas visibles abajo */}
      <rect x="34" y="148" width="22" height="16" rx="2" fill="#1e293b" />
      <rect x="184" y="148" width="22" height="16" rx="2" fill="#1e293b" />

      {/* Detalles: capó, rejilla, focos y plumillas */}
      <path d="M55 44 C75 39 165 39 185 44" stroke="#64748b" strokeWidth="1" />
      <path d="M48 72 H192" stroke="#64748b" strokeWidth="0.9" />
      <g stroke="#64748b" strokeWidth="0.8">
        <line x1="88" y1="97" x2="88" y2="115" />
        <line x1="100" y1="97" x2="100" y2="115" />
        <line x1="112" y1="97" x2="112" y2="115" />
        <line x1="124" y1="97" x2="124" y2="115" />
        <line x1="136" y1="97" x2="136" y2="115" />
        <line x1="148" y1="97" x2="148" y2="115" />
        <line x1="36" y1="102" x2="72" y2="102" />
        <line x1="168" y1="102" x2="204" y2="102" />
      </g>
      <path d="M70 90 L100 78 M170 90 L140 78" stroke="#64748b" strokeWidth="1" />
      <rect x="108" y="128" width="24" height="8" rx="2" stroke="#64748b" strokeWidth="0.8" fill="#f8fafc" />
    </svg>
  )
}
