import { useEffect, useState } from 'react'
import { InspeccionVehicular } from '../components/inspeccion/InspeccionVehicular'
import type { InspeccionExportada } from '../components/inspeccion/tipos'
import { ApiError, listarIngresos, subirInspeccion } from '../lib/api'
import type { IngresoResumen } from '../types'

export function InspeccionPage() {
  const [ingresos, setIngresos] = useState<IngresoResumen[]>([])
  const [idSeleccionado, setIdSeleccionado] = useState('')
  const [cargando, setCargando] = useState(true)
  const [mensaje, setMensaje] = useState<string | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [redibujar, setRedibujar] = useState(false)

  useEffect(() => {
    listarIngresos()
      .then((respuesta) => {
        setIngresos(respuesta.data)
        if (respuesta.data[0]) setIdSeleccionado(String(respuesta.data[0].idIngreso))
      })
      .catch((fallo) => {
        setError(fallo instanceof ApiError ? fallo.message : 'No se pudieron cargar los vehículos.')
      })
      .finally(() => setCargando(false))
  }, [])

  const seleccionado = ingresos.find((ingreso) => String(ingreso.idIngreso) === idSeleccionado) ?? null

  async function guardar(resultado: InspeccionExportada) {
    if (!seleccionado) return

    setMensaje(null)
    setError(null)

    try {
      const respuesta = await subirInspeccion(
        seleccionado.idIngreso,
        resultado.blob,
        resultado.sinDanios,
      )
      setMensaje(respuesta.message)
      setRedibujar(false)
      setIngresos((previos) => previos.map((ingreso) => (
        ingreso.idIngreso === seleccionado.idIngreso
          ? { ...ingreso, inspeccionUrl: respuesta.url, inspeccionSinDanios: resultado.sinDanios }
          : ingreso
      )))
    } catch (fallo) {
      setError(fallo instanceof ApiError ? fallo.message : 'No se pudo guardar la inspección.')
      throw fallo
    }
  }

  return (
    <div className="space-y-4">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h1 className="text-xl font-bold">Inspección vehicular</h1>
          <p className="text-sm text-pc-muted">
            Marca los daños con el dedo o lápiz sobre el esquema del vehículo.
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
              setRedibujar(false)
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

      {!seleccionado && !cargando && (
        <div className="pc-card p-6 text-sm text-pc-muted">
          No hay vehículos activos. Registra un ingreso en Recepción para inspeccionarlo.
        </div>
      )}

      {seleccionado && seleccionado.inspeccionUrl && !redibujar && (
        <div className="space-y-3">
          <div className="pc-card overflow-hidden">
            <img
              src={seleccionado.inspeccionUrl}
              alt={`Inspección de ${seleccionado.vehiculo.patente}`}
              className="mx-auto block max-h-[70vh] w-auto"
            />
          </div>
          <div className="flex justify-end gap-2">
            <button type="button" className="pc-btn-ghost" onClick={() => setRedibujar(true)}>
              Volver a inspeccionar
            </button>
          </div>
        </div>
      )}

      {seleccionado && (!seleccionado.inspeccionUrl || redibujar) && (
        <InspeccionVehicular key={seleccionado.idIngreso} onGuardar={guardar} />
      )}
    </div>
  )
}
