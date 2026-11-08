import { useCallback, useEffect, useState, type FormEvent } from 'react'
import { Modal } from '../components/Modal'
import { useAuth } from '../auth/AuthContext'
import { actualizarFila, ApiError, listarFilas } from '../lib/api'
import type { Fila } from '../types'

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

export function FilasPage() {
  const { usuario } = useAuth()
  const esAdmin = usuario?.idRol === 1

  const [filas, setFilas] = useState<Fila[]>([])
  const [cargando, setCargando] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const [editar, setEditar] = useState<Fila | null>(null)
  const [capacidad, setCapacidad] = useState('')
  const [guardando, setGuardando] = useState(false)
  const [errorModal, setErrorModal] = useState<string | null>(null)

  const cargar = useCallback(async () => {
    setCargando(true)
    setError(null)

    try {
      const respuesta = await listarFilas()
      setFilas(respuesta.data)
    } catch (fallo) {
      setError(mensajeDeError(fallo))
    } finally {
      setCargando(false)
    }
  }, [])

  useEffect(() => {
    void cargar()
  }, [cargar])

  function abrirEdicion(fila: Fila) {
    setErrorModal(null)
    setCapacidad(String(fila.capacidadTotal))
    setEditar(fila)
  }

  async function guardar(evento: FormEvent<HTMLFormElement>) {
    evento.preventDefault()
    if (!editar) return

    setGuardando(true)
    setErrorModal(null)

    try {
      await actualizarFila(editar.idFila, Number(capacidad))
      setEditar(null)
      await cargar()
    } catch (fallo) {
      setErrorModal(mensajeDeError(fallo))
    } finally {
      setGuardando(false)
    }
  }

  const totalCapacidad = filas.reduce((suma, fila) => suma + fila.capacidadTotal, 0)
  const totalOcupadas = filas.reduce((suma, fila) => suma + fila.ocupadas, 0)
  const totalLibres = filas.reduce((suma, fila) => suma + fila.libres, 0)

  return (
    <div className="space-y-5">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h1 className="text-xl font-bold">Filas y ocupación</h1>
          <p className="text-sm text-pc-muted">
            Capacidad por fila calculada con los ingresos activos (sin fecha de término).
          </p>
        </div>
        <div className="flex gap-2 text-xs">
          <span className="rounded-full border border-pc-border px-3 py-1">
            {totalOcupadas} / {totalCapacidad} ocupados
          </span>
          <span className="rounded-full border border-pc-mint/40 bg-pc-mint/10 px-3 py-1 text-pc-mint">
            {totalLibres} libres
          </span>
        </div>
      </div>

      {error && (
        <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-4 py-3 text-sm text-pc-danger">
          {error}
        </div>
      )}

      <div className="pc-card overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full min-w-[720px] text-left text-sm">
            <thead className="border-b border-pc-border text-[11px] uppercase tracking-wide text-pc-muted">
              <tr>
                <th className="px-4 py-3">Fila</th>
                <th className="px-4 py-3">Capacidad</th>
                <th className="px-4 py-3">Ocupadas</th>
                <th className="px-4 py-3">Libres</th>
                <th className="px-4 py-3">Estado</th>
                <th className="px-4 py-3 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {cargando && (
                <tr>
                  <td colSpan={6} className="px-4 py-8 text-center text-pc-muted">Cargando filas…</td>
                </tr>
              )}

              {!cargando && filas.length === 0 && (
                <tr>
                  <td colSpan={6} className="px-4 py-8 text-center text-pc-muted">No hay filas registradas.</td>
                </tr>
              )}

              {!cargando && filas.map((fila) => {
                const llena = fila.ocupadas >= fila.capacidadTotal
                return (
                  <tr key={fila.idFila} className="border-b border-pc-border/60 last:border-0">
                    <td className="px-4 py-3 font-semibold">{fila.fila}</td>
                    <td className="px-4 py-3">{fila.capacidadTotal}</td>
                    <td className="px-4 py-3">{fila.ocupadas}</td>
                    <td className="px-4 py-3">{fila.libres}</td>
                    <td className="px-4 py-3">
                      <span className={llena ? 'text-pc-danger' : 'text-pc-mint'}>
                        {llena ? 'Llena' : 'Disponible'}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-right">
                      {esAdmin ? (
                        <button type="button" className="pc-btn-ghost px-3" onClick={() => abrirEdicion(fila)}>
                          Editar capacidad
                        </button>
                      ) : (
                        <span className="text-xs text-pc-muted">Solo lectura</span>
                      )}
                    </td>
                  </tr>
                )
              })}
            </tbody>
          </table>
        </div>
      </div>

      {!esAdmin && (
        <p className="text-xs text-pc-muted">
          Solo el administrador puede modificar la capacidad de las filas.
        </p>
      )}

      <Modal
        abierto={editar !== null}
        titulo={`Capacidad de la fila ${editar?.fila ?? ''}`}
        onCerrar={() => setEditar(null)}
        ancho="max-w-md"
      >
        {editar && (
          <form onSubmit={guardar} className="space-y-4" noValidate>
            {errorModal && (
              <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-3 py-2 text-sm text-pc-danger">
                {errorModal}
              </div>
            )}

            <p className="text-sm text-pc-muted">
              Ocupación actual: <strong className="text-pc-text">{editar.ocupadas}</strong> vehículos.
              La capacidad no puede ser menor a ese valor.
            </p>

            <div>
              <label htmlFor="fCapacidad" className="pc-label">Capacidad total *</label>
              <input
                id="fCapacidad"
                type="number"
                className="pc-input"
                min={1}
                max={500}
                required
                value={capacidad}
                onChange={(evento) => setCapacidad(evento.target.value)}
              />
            </div>

            <div className="flex justify-end gap-2 border-t border-pc-border pt-4">
              <button type="button" className="pc-btn-ghost" onClick={() => setEditar(null)} disabled={guardando}>
                Cancelar
              </button>
              <button type="submit" className="pc-btn-primary" disabled={guardando}>
                {guardando ? 'Guardando…' : 'Guardar capacidad'}
              </button>
            </div>
          </form>
        )}
      </Modal>
    </div>
  )
}
