import { useRef } from 'react'
import type { FotoInspeccion } from '../../types'

export interface FotoPendiente {
  id: string
  url: string
  archivo: File
}

interface Props {
  fotos: FotoInspeccion[]
  pendientes?: FotoPendiente[]
  onAgregar: (archivos: File[]) => void
  onEliminar?: (idFoto: number) => void
  onQuitarPendiente?: (id: string) => void
  soloLectura?: boolean
  guardando?: boolean
  nota?: string
}

export function FotosVehiculo({
  fotos,
  pendientes = [],
  onAgregar,
  onEliminar,
  onQuitarPendiente,
  soloLectura = false,
  guardando = false,
  nota,
}: Props) {
  const entrada = useRef<HTMLInputElement>(null)
  const total = fotos.length + pendientes.length

  return (
    <div className="pc-card p-4">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 className="text-sm font-bold">Fotografías del vehículo</h2>
          <p className="text-xs text-pc-muted">
            {total > 0 ? `${total} fotografía(s) registradas.` : 'Aún no hay fotografías.'}
          </p>
        </div>

        {!soloLectura && (
          <div>
            <input
              ref={entrada}
              type="file"
              accept="image/*"
              capture="environment"
              multiple
              className="hidden"
              onChange={(evento) => {
                const archivos = Array.from(evento.target.files ?? [])
                if (archivos.length > 0) onAgregar(archivos)
                evento.target.value = ''
              }}
            />
            <button
              type="button"
              className="pc-btn-primary"
              disabled={guardando}
              onClick={() => entrada.current?.click()}
            >
              {guardando ? 'Subiendo…' : 'Tomar foto'}
            </button>
          </div>
        )}
      </div>

      {nota && <p className="mt-2 text-xs text-pc-muted">{nota}</p>}

      {total === 0 ? (
        <div className="mt-4 rounded-xl border border-dashed border-pc-border px-4 py-10 text-center text-sm text-pc-muted">
          Usa la cámara del dispositivo para registrar el estado del vehículo.
        </div>
      ) : (
        <div className="mt-4 grid gap-2 sm:grid-cols-3 lg:grid-cols-4">
          {fotos.map((foto) => (
            <div key={`foto-${foto.idFoto}`} className="relative overflow-hidden rounded-xl border border-pc-border bg-pc-panel2">
              <img src={foto.url} alt="Fotografía del vehículo" className="aspect-[4/3] w-full object-cover" />
              {!soloLectura && onEliminar && (
                <button
                  type="button"
                  className="absolute right-2 top-2 rounded-md bg-black/70 px-2 py-1 text-xs font-semibold text-white hover:bg-pc-danger"
                  onClick={() => onEliminar(foto.idFoto)}
                >
                  Eliminar
                </button>
              )}
            </div>
          ))}

          {pendientes.map((foto) => (
            <div key={foto.id} className="relative overflow-hidden rounded-xl border border-pc-mint/50 bg-pc-panel2">
              <img src={foto.url} alt="Fotografía pendiente" className="aspect-[4/3] w-full object-cover" />
              <span className="absolute left-2 top-2 rounded bg-pc-mint/90 px-2 py-1 text-[10px] font-bold uppercase text-[#04121d]">
                Pendiente
              </span>
              {!soloLectura && onQuitarPendiente && (
                <button
                  type="button"
                  className="absolute right-2 top-2 rounded-md bg-black/70 px-2 py-1 text-xs font-semibold text-white hover:bg-pc-danger"
                  onClick={() => onQuitarPendiente(foto.id)}
                >
                  Quitar
                </button>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  )
}
