import { useState, type FormEvent } from 'react'
import { useAuth } from '../auth/AuthContext'
import { actualizarPerfil, ApiError, cambiarContrasenaPerfil } from '../lib/api'

function mensajeDeError(fallo: unknown): string {
  if (fallo instanceof ApiError) {
    if (fallo.errors) {
      const primero = Object.values(fallo.errors)[0]
      if (Array.isArray(primero) && primero.length > 0) return primero[0]
    }
    return fallo.message
  }
  return 'Ocurrió un error inesperado.'
}

export function PerfilPage() {
  const { usuario, actualizarUsuarioSesion } = useAuth()

  const [nombres, setNombres] = useState(usuario?.nombres ?? '')
  const [apellidoPaterno, setApellidoPaterno] = useState(usuario?.apellidoPaterno ?? '')
  const [apellidoMaterno, setApellidoMaterno] = useState(usuario?.apellidoMaterno ?? '')
  const [correo, setCorreo] = useState(usuario?.correo ?? '')

  const [guardandoDatos, setGuardandoDatos] = useState(false)
  const [datosOk, setDatosOk] = useState<string | null>(null)
  const [datosError, setDatosError] = useState<string | null>(null)

  const [actual, setActual] = useState('')
  const [nueva, setNueva] = useState('')
  const [confirmacion, setConfirmacion] = useState('')
  const [guardandoClave, setGuardandoClave] = useState(false)
  const [claveOk, setClaveOk] = useState<string | null>(null)
  const [claveError, setClaveError] = useState<string | null>(null)

  if (!usuario) return null

  async function guardarDatos(evento: FormEvent<HTMLFormElement>) {
    evento.preventDefault()
    setGuardandoDatos(true)
    setDatosOk(null)
    setDatosError(null)

    try {
      const respuesta = await actualizarPerfil({
        nombres: nombres.trim(),
        apellidoPaterno: apellidoPaterno.trim(),
        apellidoMaterno: apellidoMaterno.trim() || null,
        correo: correo.trim(),
      })
      actualizarUsuarioSesion(respuesta.usuario)
      setDatosOk(respuesta.message)
    } catch (fallo) {
      setDatosError(mensajeDeError(fallo))
    } finally {
      setGuardandoDatos(false)
    }
  }

  async function guardarClave(evento: FormEvent<HTMLFormElement>) {
    evento.preventDefault()
    setGuardandoClave(true)
    setClaveOk(null)
    setClaveError(null)

    try {
      const respuesta = await cambiarContrasenaPerfil({
        contrasena_actual: actual,
        contrasena: nueva,
        contrasena_confirmation: confirmacion,
      })
      setClaveOk(respuesta.message)
      setActual('')
      setNueva('')
      setConfirmacion('')
    } catch (fallo) {
      setClaveError(mensajeDeError(fallo))
    } finally {
      setGuardandoClave(false)
    }
  }

  return (
    <div className="space-y-5">
      <div>
        <h1 className="text-xl font-bold">Mi perfil</h1>
        <p className="text-sm text-pc-muted">
          Actualiza tus datos personales y tu contraseña. El RUT y el rol los administra la jefatura.
        </p>
      </div>

      <div className="grid gap-5 lg:grid-cols-2">
        <section className="pc-card p-5">
          <h2 className="text-sm font-semibold">Datos personales</h2>

          {datosOk && (
            <div className="mt-3 rounded-lg border border-pc-mint/40 bg-pc-mint/10 px-3 py-2 text-sm text-pc-mint">
              {datosOk}
            </div>
          )}
          {datosError && (
            <div className="mt-3 rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-3 py-2 text-sm text-pc-danger">
              {datosError}
            </div>
          )}

          <form onSubmit={guardarDatos} className="mt-4 space-y-4" noValidate>
            <div>
              <label htmlFor="pRut" className="pc-label">RUT</label>
              <input id="pRut" className="pc-input opacity-60" value={usuario.rut} disabled readOnly />
            </div>
            <div>
              <label htmlFor="pNombres" className="pc-label">Nombres *</label>
              <input id="pNombres" className="pc-input" maxLength={65} required value={nombres}
                onChange={(evento) => setNombres(evento.target.value)} />
            </div>
            <div className="grid gap-4 sm:grid-cols-2">
              <div>
                <label htmlFor="pApellidoPaterno" className="pc-label">Apellido paterno *</label>
                <input id="pApellidoPaterno" className="pc-input" maxLength={30} required value={apellidoPaterno}
                  onChange={(evento) => setApellidoPaterno(evento.target.value)} />
              </div>
              <div>
                <label htmlFor="pApellidoMaterno" className="pc-label">Apellido materno</label>
                <input id="pApellidoMaterno" className="pc-input" maxLength={30} value={apellidoMaterno}
                  onChange={(evento) => setApellidoMaterno(evento.target.value)} />
              </div>
            </div>
            <div>
              <label htmlFor="pCorreo" className="pc-label">Correo *</label>
              <input id="pCorreo" type="email" className="pc-input" maxLength={65} required value={correo}
                onChange={(evento) => setCorreo(evento.target.value)} />
            </div>
            <div>
              <label htmlFor="pRol" className="pc-label">Rol</label>
              <input id="pRol" className="pc-input opacity-60" value={usuario.rol ?? ''} disabled readOnly />
            </div>

            <div className="flex justify-end border-t border-pc-border pt-4">
              <button type="submit" className="pc-btn-primary" disabled={guardandoDatos}>
                {guardandoDatos ? 'Guardando…' : 'Guardar cambios'}
              </button>
            </div>
          </form>
        </section>

        <section className="pc-card p-5">
          <h2 className="text-sm font-semibold">Seguridad</h2>

          {claveOk && (
            <div className="mt-3 rounded-lg border border-pc-mint/40 bg-pc-mint/10 px-3 py-2 text-sm text-pc-mint">
              {claveOk}
            </div>
          )}
          {claveError && (
            <div className="mt-3 rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-3 py-2 text-sm text-pc-danger">
              {claveError}
            </div>
          )}

          <form onSubmit={guardarClave} className="mt-4 space-y-4" noValidate>
            <div>
              <label htmlFor="pActual" className="pc-label">Contraseña actual *</label>
              <input id="pActual" type="password" className="pc-input" autoComplete="current-password"
                required value={actual} onChange={(evento) => setActual(evento.target.value)} />
            </div>
            <div>
              <label htmlFor="pNueva" className="pc-label">Nueva contraseña *</label>
              <input id="pNueva" type="password" className="pc-input" minLength={8} autoComplete="new-password"
                required value={nueva} onChange={(evento) => setNueva(evento.target.value)} />
              <p className="mt-1 text-xs text-pc-muted">Mínimo 8 caracteres.</p>
            </div>
            <div>
              <label htmlFor="pConfirmacion" className="pc-label">Confirmar contraseña *</label>
              <input id="pConfirmacion" type="password" className="pc-input" minLength={8} autoComplete="new-password"
                required value={confirmacion} onChange={(evento) => setConfirmacion(evento.target.value)} />
            </div>

            <div className="flex justify-end border-t border-pc-border pt-4">
              <button type="submit" className="pc-btn-primary" disabled={guardandoClave}>
                {guardandoClave ? 'Actualizando…' : 'Cambiar contraseña'}
              </button>
            </div>
          </form>
        </section>
      </div>
    </div>
  )
}
