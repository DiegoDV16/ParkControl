import { useEffect, type ReactNode } from 'react'

interface Props {
  abierto: boolean
  titulo: string
  onCerrar: () => void
  children: ReactNode
  ancho?: string
}

export function Modal({ abierto, titulo, onCerrar, children, ancho = 'max-w-xl' }: Props) {
  useEffect(() => {
    if (!abierto) return

    function alPresionar(evento: KeyboardEvent) {
      if (evento.key === 'Escape') onCerrar()
    }

    document.addEventListener('keydown', alPresionar)
    return () => document.removeEventListener('keydown', alPresionar)
  }, [abierto, onCerrar])

  if (!abierto) return null

  return (
    <div className="fixed inset-0 z-50 overflow-y-auto bg-black/70 p-4">
      <div className={`mx-auto my-6 w-full ${ancho}`}>
        <div className="pc-card" role="dialog" aria-modal="true" aria-label={titulo}>
          <div className="flex items-center justify-between border-b border-pc-border px-5 py-4">
            <h2 className="text-sm font-semibold">{titulo}</h2>
            <button
              type="button"
              onClick={onCerrar}
              className="min-h-[44px] min-w-[44px] rounded-lg text-pc-muted hover:bg-white/5 hover:text-pc-text"
              aria-label="Cerrar"
            >
              ✕
            </button>
          </div>
          <div className="px-5 py-4">{children}</div>
        </div>
      </div>
    </div>
  )
}
