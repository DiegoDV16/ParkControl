import { useState, type FormEvent } from 'react'
import { Navigate, useNavigate } from 'react-router-dom'
import { useAuth } from '../auth/AuthContext'
import { ApiError } from '../lib/api'

export function LoginPage() {
  const { usuario, iniciarSesion } = useAuth()
  const navegar = useNavigate()

  const [correo, setCorreo] = useState('')
  const [contrasena, setContrasena] = useState('')
  const [verClave, setVerClave] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [enviando, setEnviando] = useState(false)

  if (usuario) {
    return <Navigate to="/" replace />
  }

  async function enviar(evento: FormEvent<HTMLFormElement>) {
    evento.preventDefault()
    setError(null)
    setEnviando(true)

    try {
      await iniciarSesion(correo.trim(), contrasena)
      navegar('/', { replace: true })
    } catch (fallo) {
      if (fallo instanceof ApiError) {
        setError(fallo.message)
      } else {
        setError('No se pudo iniciar sesión. Inténtalo nuevamente.')
      }
    } finally {
      setEnviando(false)
    }
  }

  return (
    <div className="flex min-h-screen flex-col items-center justify-center px-4 py-10">
      <div className="mb-8 flex items-center gap-3">
        <span className="flex h-12 w-12 items-center justify-center rounded-xl bg-pc-mint text-2xl font-black text-[#04121d]">
          P
        </span>
        <div>
          <p className="text-xl font-bold tracking-tight">ParkControl</p>
          <p className="text-[11px] uppercase tracking-[0.22em] text-pc-muted">
            Larga estadía · aeropuerto
          </p>
        </div>
      </div>

      <div className="w-full max-w-md pc-card p-6 sm:p-8">
        <div className="mb-6">
          <h1 className="text-lg font-semibold">Iniciar sesión</h1>
          <p className="mt-1 text-sm text-pc-muted">
            Acceso para administración, operación en patio y jefatura.
          </p>
        </div>

        {error && (
          <div
            role="alert"
            className="mb-4 rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-4 py-3 text-sm text-pc-danger"
          >
            {error}
          </div>
        )}

        <form onSubmit={enviar} className="space-y-4" noValidate>
          <div>
            <label htmlFor="correo" className="pc-label">
              Correo corporativo
            </label>
            <input
              id="correo"
              type="email"
              autoComplete="username"
              className="pc-input"
              placeholder="usuario@parkcontrol.cl"
              value={correo}
              onChange={(evento) => setCorreo(evento.target.value)}
              required
            />
          </div>

          <div>
            <label htmlFor="contrasena" className="pc-label">
              Contraseña
            </label>
            <div className="relative">
              <input
                id="contrasena"
                type={verClave ? 'text' : 'password'}
                autoComplete="current-password"
                className="pc-input pr-20"
                placeholder="••••••••••"
                value={contrasena}
                onChange={(evento) => setContrasena(evento.target.value)}
                required
              />
              <button
                type="button"
                onClick={() => setVerClave((valor) => !valor)}
                className="absolute right-2 top-1/2 -translate-y-1/2 rounded-md px-3 py-2 text-xs font-semibold uppercase tracking-wide text-pc-muted hover:text-pc-mint"
              >
                {verClave ? 'Ocultar' : 'Ver'}
              </button>
            </div>
          </div>

          <button type="submit" className="pc-btn-primary w-full" disabled={enviando}>
            {enviando ? 'Ingresando…' : 'Ingresar al sistema'}
          </button>
        </form>

        <p className="mt-6 text-center text-xs text-pc-muted">
          El rol se valida en el servidor. Si tu cuenta está inactiva, el acceso será rechazado.
        </p>
      </div>

      <div className="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-[11px] uppercase tracking-[0.16em] text-pc-muted">
        <span className="flex items-center gap-2">
          <span className="h-2 w-2 rounded-full bg-pc-mint" /> API Laravel · Sanctum
        </span>
        <span>MySQL · parkcontrol</span>
        <span>Fase 1</span>
      </div>
    </div>
  )
}
