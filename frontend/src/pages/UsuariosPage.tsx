import { useCallback, useEffect, useState, type FormEvent } from 'react'
import { Modal } from '../components/Modal'
import { useAuth } from '../auth/AuthContext'
import {
  actualizarUsuario,
  ApiError,
  cambiarEstadoUsuario,
  crearUsuario,
  eliminarUsuario,
  listarRoles,
  listarUsuarios,
} from '../lib/api'
import type { Paginated, Rol, Usuario, UsuarioPayload } from '../types'

interface PropsFormulario {
  usuario: Usuario | null
  roles: Rol[]
  guardando: boolean
  error: string | null
  onGuardar: (datos: UsuarioPayload) => void
  onCancelar: () => void
}

function UsuarioFormulario({ usuario, roles, guardando, error, onGuardar, onCancelar }: PropsFormulario) {
  const [idRol, setIdRol] = useState(String(usuario?.idRol ?? 2))
  const [rut, setRut] = useState(usuario?.rut ?? '')
  const [nombres, setNombres] = useState(usuario?.nombres ?? '')
  const [apellidoPaterno, setApellidoPaterno] = useState(usuario?.apellidoPaterno ?? '')
  const [apellidoMaterno, setApellidoMaterno] = useState(usuario?.apellidoMaterno ?? '')
  const [correo, setCorreo] = useState(usuario?.correo ?? '')
  const [contrasena, setContrasena] = useState('')

  const editando = usuario !== null

  function enviar(evento: FormEvent<HTMLFormElement>) {
    evento.preventDefault()

    onGuardar({
      idRol: Number(idRol),
      rut: rut.trim(),
      nombres: nombres.trim(),
      apellidoPaterno: apellidoPaterno.trim(),
      apellidoMaterno: apellidoMaterno.trim() || null,
      correo: correo.trim(),
      ...(contrasena ? { contrasena } : {}),
    })
  }

  return (
    <form onSubmit={enviar} className="space-y-4" noValidate>
      {error && (
        <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-3 py-2 text-sm text-pc-danger">
          {error}
        </div>
      )}

      <div className="grid gap-4 sm:grid-cols-2">
        <div>
          <label htmlFor="uRut" className="pc-label">RUT *</label>
          <input id="uRut" className="pc-input" maxLength={13} required value={rut}
            onChange={(evento) => setRut(evento.target.value)} placeholder="12.345.678-9" />
        </div>
        <div>
          <label htmlFor="uCorreo" className="pc-label">Correo *</label>
          <input id="uCorreo" type="email" className="pc-input" maxLength={65} required value={correo}
            onChange={(evento) => setCorreo(evento.target.value)} placeholder="usuario@parkcontrol.cl" />
        </div>
        <div>
          <label htmlFor="uNombres" className="pc-label">Nombres *</label>
          <input id="uNombres" className="pc-input" maxLength={65} required value={nombres}
            onChange={(evento) => setNombres(evento.target.value)} />
        </div>
        <div>
          <label htmlFor="uApellidoPaterno" className="pc-label">Apellido paterno *</label>
          <input id="uApellidoPaterno" className="pc-input" maxLength={30} required value={apellidoPaterno}
            onChange={(evento) => setApellidoPaterno(evento.target.value)} />
        </div>
        <div>
          <label htmlFor="uApellidoMaterno" className="pc-label">Apellido materno</label>
          <input id="uApellidoMaterno" className="pc-input" maxLength={30} value={apellidoMaterno}
            onChange={(evento) => setApellidoMaterno(evento.target.value)} />
        </div>
        <div>
          <label htmlFor="uRol" className="pc-label">Rol *</label>
          <select id="uRol" className="pc-input" required value={idRol}
            onChange={(evento) => setIdRol(evento.target.value)}>
            {roles.map((rol) => (
              <option key={rol.idRol} value={rol.idRol}>{rol.rol}</option>
            ))}
          </select>
        </div>
        <div className="sm:col-span-2">
          <label htmlFor="uContrasena" className="pc-label">
            {editando ? 'Nueva contraseña (opcional)' : 'Contraseña *'}
          </label>
          <input id="uContrasena" type="password" className="pc-input" minLength={8}
            required={!editando} value={contrasena} autoComplete="new-password"
            onChange={(evento) => setContrasena(evento.target.value)} />
          <p className="mt-1 text-xs text-pc-muted">
            Mínimo 8 caracteres. {editando ? 'Déjala vacía para conservar la actual.' : ''}
          </p>
        </div>
      </div>

      <div className="flex flex-wrap justify-end gap-2 border-t border-pc-border pt-4">
        <button type="button" className="pc-btn-ghost" onClick={onCancelar} disabled={guardando}>
          Cancelar
        </button>
        <button type="submit" className="pc-btn-primary" disabled={guardando}>
          {guardando ? 'Guardando…' : editando ? 'Guardar cambios' : 'Crear usuario'}
        </button>
      </div>
    </form>
  )
}

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

export function UsuariosPage() {
  const { usuario: sesion } = useAuth()

  const [filas, setFilas] = useState<Usuario[]>([])
  const [meta, setMeta] = useState<Paginated<Usuario>['meta'] | null>(null)
  const [roles, setRoles] = useState<Rol[]>([])
  const [cargando, setCargando] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const [buscarInput, setBuscarInput] = useState('')
  const [buscar, setBuscar] = useState('')
  const [filtroRol, setFiltroRol] = useState('')
  const [filtroEstado, setFiltroEstado] = useState('')
  const [pagina, setPagina] = useState(1)

  const [modal, setModal] = useState<{ modo: 'crear' | 'editar'; usuario: Usuario | null } | null>(null)
  const [confirmar, setConfirmar] = useState<{ tipo: 'eliminar' | 'estado'; usuario: Usuario } | null>(null)
  const [guardando, setGuardando] = useState(false)
  const [errorModal, setErrorModal] = useState<string | null>(null)

  const cargar = useCallback(async () => {
    setCargando(true)
    setError(null)

    try {
      const respuesta = await listarUsuarios({
        buscar,
        idRol: filtroRol === '' ? '' : Number(filtroRol),
        estado: filtroEstado === '' ? '' : Number(filtroEstado),
        page: pagina,
      })
      setFilas(respuesta.data)
      setMeta(respuesta.meta)
    } catch (fallo) {
      setError(mensajeDeError(fallo))
    } finally {
      setCargando(false)
    }
  }, [buscar, filtroRol, filtroEstado, pagina])

  useEffect(() => {
    void cargar()
  }, [cargar])

  useEffect(() => {
    listarRoles()
      .then((respuesta) => setRoles(respuesta.data))
      .catch(() => setRoles([{ idRol: 1, rol: 'Administrador' }, { idRol: 2, rol: 'Operador' }]))
  }, [])

  function aplicarFiltros(evento: FormEvent<HTMLFormElement>) {
    evento.preventDefault()
    setPagina(1)
    setBuscar(buscarInput)
  }

  async function guardar(datos: UsuarioPayload) {
    if (!modal) return

    setGuardando(true)
    setErrorModal(null)

    try {
      if (modal.modo === 'editar' && modal.usuario) {
        await actualizarUsuario(modal.usuario.idUsuario, datos)
      } else {
        await crearUsuario(datos)
      }
      setModal(null)
      await cargar()
    } catch (fallo) {
      setErrorModal(mensajeDeError(fallo))
    } finally {
      setGuardando(false)
    }
  }

  async function ejecutarConfirmacion() {
    if (!confirmar) return

    setGuardando(true)
    setErrorModal(null)

    try {
      if (confirmar.tipo === 'eliminar') {
        await eliminarUsuario(confirmar.usuario.idUsuario)
      } else {
        await cambiarEstadoUsuario(
          confirmar.usuario.idUsuario,
          confirmar.usuario.estado === 1 ? 0 : 1,
        )
      }
      setConfirmar(null)
      await cargar()
    } catch (fallo) {
      setErrorModal(mensajeDeError(fallo))
    } finally {
      setGuardando(false)
    }
  }

  return (
    <div className="space-y-5">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h1 className="text-xl font-bold">Usuarios</h1>
          <p className="text-sm text-pc-muted">Altas, edición, estado y eliminación desde la base de datos.</p>
        </div>
        <button
          type="button"
          className="pc-btn-primary"
          onClick={() => {
            setErrorModal(null)
            setModal({ modo: 'crear', usuario: null })
          }}
        >
          + Nuevo usuario
        </button>
      </div>

      <form onSubmit={aplicarFiltros} className="pc-card grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-4">
        <div>
          <label htmlFor="fBuscar" className="pc-label">Buscar</label>
          <input id="fBuscar" className="pc-input" value={buscarInput} placeholder="Nombre, RUT o correo"
            onChange={(evento) => setBuscarInput(evento.target.value)} />
        </div>
        <div>
          <label htmlFor="fRol" className="pc-label">Rol</label>
          <select id="fRol" className="pc-input" value={filtroRol}
            onChange={(evento) => { setFiltroRol(evento.target.value); setPagina(1) }}>
            <option value="">Todos</option>
            {roles.map((rol) => (
              <option key={rol.idRol} value={rol.idRol}>{rol.rol}</option>
            ))}
          </select>
        </div>
        <div>
          <label htmlFor="fEstado" className="pc-label">Estado</label>
          <select id="fEstado" className="pc-input" value={filtroEstado}
            onChange={(evento) => { setFiltroEstado(evento.target.value); setPagina(1) }}>
            <option value="">Todos</option>
            <option value="1">Activos</option>
            <option value="0">Inactivos</option>
          </select>
        </div>
        <div className="flex items-end">
          <button type="submit" className="pc-btn-ghost w-full">Filtrar</button>
        </div>
      </form>

      {error && (
        <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-4 py-3 text-sm text-pc-danger">
          {error}
        </div>
      )}

      <div className="pc-card overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full min-w-[820px] text-left text-sm">
            <thead className="border-b border-pc-border text-[11px] uppercase tracking-wide text-pc-muted">
              <tr>
                <th className="px-4 py-3">Usuario</th>
                <th className="px-4 py-3">RUT</th>
                <th className="px-4 py-3">Rol</th>
                <th className="px-4 py-3">Estado</th>
                <th className="px-4 py-3">Último acceso</th>
                <th className="px-4 py-3 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {cargando && (
                <tr>
                  <td colSpan={6} className="px-4 py-8 text-center text-pc-muted">Cargando usuarios…</td>
                </tr>
              )}

              {!cargando && filas.length === 0 && (
                <tr>
                  <td colSpan={6} className="px-4 py-8 text-center text-pc-muted">No hay usuarios con ese criterio.</td>
                </tr>
              )}

              {!cargando && filas.map((fila) => (
                <tr key={fila.idUsuario} className="border-b border-pc-border/60 last:border-0">
                  <td className="px-4 py-3">
                    <div className="font-medium">
                      {[fila.nombres, fila.apellidoPaterno, fila.apellidoMaterno].filter(Boolean).join(' ')}
                    </div>
                    <div className="text-xs text-pc-muted">{fila.correo}</div>
                  </td>
                  <td className="px-4 py-3 text-pc-muted">{fila.rut}</td>
                  <td className="px-4 py-3">
                    <span className="rounded-full border border-pc-border px-2 py-1 text-[11px] uppercase tracking-wide">
                      {fila.rol ?? `Rol ${fila.idRol}`}
                    </span>
                  </td>
                  <td className="px-4 py-3">
                    <span className={fila.estado === 1 ? 'text-pc-mint' : 'text-pc-muted'}>
                      {fila.estado === 1 ? 'Activo' : 'Inactivo'}
                    </span>
                  </td>
                  <td className="px-4 py-3 text-xs text-pc-muted">
                    {fila.ultimoAcceso ? new Date(fila.ultimoAcceso).toLocaleString('es-CL') : 'Nunca'}
                  </td>
                  <td className="px-4 py-3">
                    <div className="flex justify-end gap-1">
                      <button type="button" className="pc-btn-ghost px-3" title="Editar"
                        onClick={() => { setErrorModal(null); setModal({ modo: 'editar', usuario: fila }) }}>
                        Editar
                      </button>
                      <button type="button" className="pc-btn-ghost px-3" title="Activar o desactivar"
                        disabled={fila.idUsuario === sesion?.idUsuario}
                        onClick={() => { setErrorModal(null); setConfirmar({ tipo: 'estado', usuario: fila }) }}>
                        {fila.estado === 1 ? 'Desactivar' : 'Activar'}
                      </button>
                      <button type="button" className="pc-btn-ghost px-3 text-pc-danger" title="Eliminar"
                        disabled={fila.idUsuario === sesion?.idUsuario}
                        onClick={() => { setErrorModal(null); setConfirmar({ tipo: 'eliminar', usuario: fila }) }}>
                        Eliminar
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {meta && (
          <div className="flex flex-wrap items-center justify-between gap-3 border-t border-pc-border px-4 py-3 text-xs text-pc-muted">
            <span>
              {meta.total} usuarios · página {meta.current_page} de {meta.last_page}
            </span>
            <div className="flex gap-2">
              <button type="button" className="pc-btn-ghost px-3" disabled={meta.current_page <= 1 || cargando}
                onClick={() => setPagina((valor) => Math.max(1, valor - 1))}>
                Anterior
              </button>
              <button type="button" className="pc-btn-ghost px-3" disabled={meta.current_page >= meta.last_page || cargando}
                onClick={() => setPagina((valor) => valor + 1)}>
                Siguiente
              </button>
            </div>
          </div>
        )}
      </div>

      <Modal
        abierto={modal !== null}
        titulo={modal?.modo === 'editar' ? 'Editar usuario' : 'Nuevo usuario'}
        onCerrar={() => setModal(null)}
        ancho="max-w-2xl"
      >
        {modal && (
          <UsuarioFormulario
            usuario={modal.usuario}
            roles={roles}
            guardando={guardando}
            error={errorModal}
            onGuardar={guardar}
            onCancelar={() => setModal(null)}
          />
        )}
      </Modal>

      <Modal
        abierto={confirmar !== null}
        titulo={confirmar?.tipo === 'eliminar' ? 'Eliminar usuario' : 'Cambiar estado'}
        onCerrar={() => setConfirmar(null)}
        ancho="max-w-md"
      >
        {confirmar && (
          <div className="space-y-4">
            {errorModal && (
              <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-3 py-2 text-sm text-pc-danger">
                {errorModal}
              </div>
            )}
            <p className="text-sm text-pc-muted">
              {confirmar.tipo === 'eliminar'
                ? `Se eliminará permanentemente a ${confirmar.usuario.nombres} ${confirmar.usuario.apellidoPaterno}. Esta acción no se puede deshacer.`
                : `Se ${confirmar.usuario.estado === 1 ? 'desactivará' : 'activará'} la cuenta de ${confirmar.usuario.nombres} ${confirmar.usuario.apellidoPaterno}.`}
            </p>
            <div className="flex justify-end gap-2">
              <button type="button" className="pc-btn-ghost" onClick={() => setConfirmar(null)} disabled={guardando}>
                Cancelar
              </button>
              <button
                type="button"
                className={confirmar.tipo === 'eliminar' ? 'pc-btn bg-pc-danger text-[#1a0505] hover:brightness-110' : 'pc-btn-primary'}
                onClick={() => void ejecutarConfirmacion()}
                disabled={guardando}
              >
                {guardando ? 'Procesando…' : confirmar.tipo === 'eliminar' ? 'Eliminar' : 'Confirmar'}
              </button>
            </div>
          </div>
        )}
      </Modal>
    </div>
  )
}
