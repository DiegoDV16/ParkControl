import { useEffect, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import { FotosVehiculo } from '../components/inspeccion/FotosVehiculo'
import {
  ApiError,
  eliminarFotoInspeccion,
  listarIngresos,
  subirFotoInspeccion,
} from '../lib/api'
import type { FotoInspeccion, IngresoResumen } from '../types'

export function InspeccionPage() {
  const [parametros] = useSearchParams()
  const [ingresos, setIngresos] = useState<IngresoResumen[]>([])
  const [idSeleccionado, setIdSeleccionado] = useState('')
  const [cargando, setCargando] = useState(true)
  const [guardando, setGuardando] = useState(false)
  const [mensaje, setMensaje] = useState<string | null>(null)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    listarIngresos()
      .then((respuesta) => {
        setIngresos(respuesta.data)
        const solicitado = parametros.get('ingreso')
        const existe = respuesta.data.some((ingreso) => String(ingreso.idIngreso) === solicitado)
        if (existe && solicitado) {
          setIdSeleccionado(solicitado)
        } else if (respuesta.data[0]) {
          setIdSeleccionado(String(respuesta.data[0].idIngreso))
        }
      })
      .catch((fallo) => {
        setError(fallo instanceof ApiError ? fallo.message : 'No se pudieron cargar los vehículos.')
      })
      .finally(() => setCargando(false))
  }, [parametros])

  const seleccionado = ingresos.find((ingreso) => String(ingreso.idIngreso) === idSeleccionado) ?? null
  const fotos: FotoInspeccion[] = seleccionado?.inspeccionFotos ?? []

  function actualizarFotos(idIngreso: number, nuevas: FotoInspeccion[]) {
    setIngresos((previos) => previos.map((ingreso) => (
      ingreso.idIngreso === idIngreso ? { ...ingreso, inspeccionFotos: nuevas } : ingreso
    )))
  }

  async function agregar(archivos: File[]) {
    if (!seleccionado) return

    setGuardando(true)
    setMensaje(null)
    setError(null)

    try {
      const subidas: FotoInspeccion[] = []
      for (const archivo of archivos) {
        const respuesta = await subirFotoInspeccion(seleccionado.idIngreso, archivo)
        subidas.push(respuesta.data)
      }
      actualizarFotos(seleccionado.idIngreso, [...fotos, ...subidas])
      setMensaje(`${subidas.length} fotografía(s) guardada(s).`)
    } catch (fallo) {
      setError(fallo instanceof ApiError ? fallo.message : 'No se pudieron subir las fotografías.')
    } finally {
      setGuardando(false)
    }
  }

  async function eliminar(idFoto: number) {
    if (!seleccionado) return

    setMensaje(null)
    setError(null)

    try {
      await eliminarFotoInspeccion(idFoto)
      actualizarFotos(
        seleccionado.idIngreso,
        fotos.filter((foto) => foto.idFoto !== idFoto),
      )
      setMensaje('Fotografía eliminada.')
    } catch (fallo) {
      setError(fallo instanceof ApiError ? fallo.message : 'No se pudo eliminar la fotografía.')
    }
  }

  return (
    <div className="space-y-4">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h1 className="text-xl font-bold">Inspección vehicular</h1>
          <p className="text-sm text-pc-muted">
            Registra fotografías del estado del vehículo al ingreso.
          </p>
        </div>

        <div className="w-full sm:w-80">
          <label htmlFor="iIngreso" className="pc-label">Vehículo en patio</label>
          <select
            id="iIngreso"
            className="pc-input"
            value={idSeleccionado}
            disabled={cargando || ingresos.length === 0}
            onChange={(evento) => {
              setIdSeleccionado(evento.target.value)
              setMensaje(null)
              setError(null)
            }}
          >
            {ingresos.length === 0 && <option value="">Sin vehículos activos</option>}
            {ingresos.map((ingreso) => (
              <option key={ingreso.idIngreso} value={ingreso.idIngreso}>
                {ingreso.vehiculo.patente} · {ingreso.cliente.nombre} · {ingreso.fila}
              </option>
            ))}
          </select>
        </div>
      </div>

      {mensaje && (
        <div className="rounded-lg border border-pc-mint/40 bg-pc-mint/10 px-4 py-3 text-sm text-pc-mint">
          {mensaje}
        </div>
      )}
      {error && (
        <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-4 py-3 text-sm text-pc-danger">
          {error}
        </div>
      )}

      {!seleccionado && !cargando ? (
        <div className="pc-card p-6 text-sm text-pc-muted">
          No hay vehículos activos. Registra un ingreso en Recepción para inspeccionarlo.
        </div>
      ) : seleccionado ? (
        <FotosVehiculo
          fotos={fotos}
          onAgregar={(archivos) => void agregar(archivos)}
          onEliminar={(idFoto) => void eliminar(idFoto)}
          guardando={guardando}
          nota="Las fotografías se guardan de inmediato en el servidor."
        />
      ) : null}
    </div>
  )
}
