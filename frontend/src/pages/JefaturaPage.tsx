import { useEffect, useState } from 'react'
import { ApiError, obtenerResumenJefatura } from '../lib/api'
import type { JefaturaResumen } from '../types'

function clp(valor: number): string {
  return `$${Math.round(valor).toLocaleString('es-CL')}`
}

function fechaCorta(fecha: string | null): string {
  if (!fecha) return '—'
  const [anio, mes, dia] = fecha.split('-')
  return `${dia}-${mes}-${anio}`
}

const COLOR_ESTADO: Record<string, string> = {
  PROGRAMADO: 'border-pc-border text-pc-muted',
  PRÓXIMO: 'border-pc-warn/50 bg-pc-warn/10 text-pc-warn',
  'LLEGADA VENCIDA': 'border-pc-danger/50 bg-pc-danger/10 text-pc-danger',
  PENDIENTE: 'border-pc-border text-pc-muted',
  'SIN PROGRAMAR': 'border-pc-border text-pc-muted',
}

export function JefaturaPage() {
  const [resumen, setResumen] = useState<JefaturaResumen | null>(null)
  const [cargando, setCargando] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [actualizado, setActualizado] = useState<string>('')

  async function cargar() {
    setCargando(true)
    setError(null)

    try {
      const datos = await obtenerResumenJefatura()
      setResumen(datos)
      setActualizado(new Date().toLocaleTimeString('es-CL'))
    } catch (fallo) {
      setError(fallo instanceof ApiError ? fallo.message : 'No se pudo cargar el panel.')
    } finally {
      setCargando(false)
    }
  }

  useEffect(() => {
    void cargar()
  }, [])

  function exportarCsv() {
    if (!resumen) return

    const cabeceras = [
      'Patente', 'Cliente', 'RUT', 'Fila', 'Entrada',
      'Días', 'Monto proyectado', 'Vuelo', 'Fecha regreso',
    ]
    const filas = resumen.estadias.map((ingreso) => [
      ingreso.vehiculo.patente,
      ingreso.cliente.nombre,
      ingreso.cliente.rut,
      ingreso.fila,
      `${ingreso.fechaInicio} ${ingreso.horaInicio.slice(0, 5)}`,
      ingreso.dias,
      ingreso.montoProyectado,
      ingreso.numeroVuelo ?? '',
      ingreso.fechaRegreso ?? '',
    ])

    const csv = [cabeceras, ...filas]
      .map((fila) => fila.map((valor) => `"${String(valor).replace(/"/g, '""')}"`).join(';'))
      .join('\r\n')

    const blob = new Blob([`\uFEFF${csv}`], { type: 'text/csv;charset=utf-8' })
    const enlace = document.createElement('a')
    enlace.href = URL.createObjectURL(blob)
    enlace.download = `estadias-${new Date().toISOString().slice(0, 10)}.csv`
    enlace.click()
    URL.revokeObjectURL(enlace.href)
  }

  if (!resumen && cargando) {
    return <div className="pc-card p-8 text-center text-sm text-pc-muted">Cargando panel de supervisión…</div>
  }

  return (
    <div className="space-y-5">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h1 className="text-xl font-bold">Supervisión & KPIs</h1>
          <p className="text-sm text-pc-muted">
            Ocupación, recaudación, estadías activas y vuelos del patio.
          </p>
        </div>
        <div className="flex flex-wrap items-center gap-2">
          {actualizado && <span className="text-xs text-pc-muted">Actualizado {actualizado}</span>}
          <button type="button" className="pc-btn-ghost" onClick={() => void cargar()} disabled={cargando}>
            Actualizar
          </button>
          <button type="button" className="pc-btn-ghost" onClick={exportarCsv} disabled={!resumen}>
            Exportar CSV
          </button>
        </div>
      </div>

      {error && (
        <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-4 py-3 text-sm text-pc-danger">
          {error}
        </div>
      )}

      {resumen && (
        <>
          <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            {[
              { etiqueta: 'Vehículos activos', valor: String(resumen.kpis.activos), nota: `${resumen.kpis.ingresosHoy} ingresos hoy` },
              { etiqueta: 'Ocupación', valor: `${resumen.kpis.ocupacionPorcentaje}%`, nota: `${resumen.kpis.ocupadas}/${resumen.kpis.capacidad} cupos` },
              { etiqueta: 'Recaudación hoy', valor: clp(resumen.kpis.recaudacionHoy), nota: 'Pagos registrados hoy' },
              { etiqueta: 'Recaudación mes', valor: clp(resumen.kpis.recaudacionMes), nota: 'Mes en curso' },
              { etiqueta: 'Llegadas próximas', valor: String(resumen.kpis.llegadasProximas), nota: 'Vuelos en la próxima hora' },
            ].map((kpi) => (
              <div key={kpi.etiqueta} className="pc-card p-4">
                <p className="text-[11px] uppercase tracking-[0.12em] text-pc-muted">{kpi.etiqueta}</p>
                <p className="mt-1 text-2xl font-bold text-pc-text">{kpi.valor}</p>
                <p className="text-xs text-pc-muted">{kpi.nota}</p>
              </div>
            ))}
          </div>

          <div className="grid gap-3 lg:grid-cols-2">
            <section className="pc-card p-4">
              <h2 className="text-sm font-semibold">Ocupación por fila</h2>
              <div className="mt-3 space-y-3">
                {resumen.filas.map((fila) => {
                  const porcentaje = fila.capacidadTotal > 0
                    ? Math.min(100, Math.round((fila.ocupadas / fila.capacidadTotal) * 100))
                    : 0
                  return (
                    <div key={fila.idFila}>
                      <div className="flex items-center justify-between text-xs">
                        <span className="font-semibold text-pc-text">{fila.fila}</span>
                        <span className="text-pc-muted">
                          {fila.ocupadas}/{fila.capacidadTotal} · {fila.libres} libres
                        </span>
                      </div>
                      <div className="mt-1 h-2 overflow-hidden rounded-full bg-pc-border/60">
                        <div
                          className={`h-full rounded-full ${porcentaje >= 100 ? 'bg-pc-danger' : 'bg-pc-mint'}`}
                          style={{ width: `${porcentaje}%` }}
                        />
                      </div>
                    </div>
                  )
                })}
              </div>
            </section>

            <section className="pc-card p-4">
              <h2 className="text-sm font-semibold">Recaudación de hoy por método</h2>
              {resumen.recaudacion.porMetodo.length === 0 ? (
                <p className="mt-3 text-sm text-pc-muted">Aún no hay pagos registrados hoy.</p>
              ) : (
                <ul className="mt-3 space-y-2 text-sm">
                  {resumen.recaudacion.porMetodo.map((item) => (
                    <li key={item.metodo} className="flex items-center justify-between border-b border-pc-border/60 pb-2 last:border-0">
                      <span className="capitalize text-pc-muted">{item.metodo}</span>
                      <span className="font-semibold">{clp(item.total)}</span>
                    </li>
                  ))}
                  <li className="flex items-center justify-between pt-1 text-pc-mint">
                    <span className="font-semibold">Total hoy</span>
                    <span className="font-bold">{clp(resumen.recaudacion.hoy)}</span>
                  </li>
                </ul>
              )}
            </section>
          </div>

          <section className="pc-card overflow-hidden">
            <div className="flex items-center justify-between border-b border-pc-border px-4 py-3">
              <h2 className="text-sm font-semibold">Estadías activas</h2>
              <span className="text-xs text-pc-muted">{resumen.estadias.length} vehículos en patio</span>
            </div>
            <div className="overflow-x-auto">
              <table className="w-full min-w-[860px] text-left text-sm">
                <thead className="border-b border-pc-border text-[11px] uppercase tracking-wide text-pc-muted">
                  <tr>
                    <th className="px-4 py-3">Patente</th>
                    <th className="px-4 py-3">Cliente</th>
                    <th className="px-4 py-3">Fila</th>
                    <th className="px-4 py-3">Entrada</th>
                    <th className="px-4 py-3">Días</th>
                    <th className="px-4 py-3">Vuelo</th>
                    <th className="px-4 py-3 text-right">Monto proyectado</th>
                  </tr>
                </thead>
                <tbody>
                  {resumen.estadias.length === 0 && (
                    <tr>
                      <td colSpan={7} className="px-4 py-8 text-center text-pc-muted">
                        No hay vehículos activos en este momento.
                      </td>
                    </tr>
                  )}
                  {resumen.estadias.map((ingreso) => (
                    <tr key={ingreso.idIngreso} className="border-b border-pc-border/60 last:border-0">
                      <td className="px-4 py-3 font-semibold">{ingreso.vehiculo.patente}</td>
                      <td className="px-4 py-3">
                        <div>{ingreso.cliente.nombre}</div>
                        <div className="text-xs text-pc-muted">{ingreso.cliente.rut}</div>
                      </td>
                      <td className="px-4 py-3">{ingreso.fila}</td>
                      <td className="px-4 py-3 text-xs">
                        {fechaCorta(ingreso.fechaInicio)} {ingreso.horaInicio.slice(0, 5)}
                      </td>
                      <td className="px-4 py-3">{ingreso.dias}</td>
                      <td className="px-4 py-3 text-xs">
                        {ingreso.numeroVuelo ?? '—'}
                        {ingreso.fechaRegreso && (
                          <div className="text-pc-muted">{fechaCorta(ingreso.fechaRegreso)}</div>
                        )}
                      </td>
                      <td className="px-4 py-3 text-right font-semibold">
                        {clp(ingreso.montoProyectado)}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </section>

          <section className="pc-card overflow-hidden">
            <div className="flex items-center justify-between border-b border-pc-border px-4 py-3">
              <h2 className="text-sm font-semibold">Vuelos y llegadas</h2>
              <span className="text-xs text-pc-muted">{resumen.vuelos.length} programados</span>
            </div>
            <div className="overflow-x-auto">
              <table className="w-full min-w-[760px] text-left text-sm">
                <thead className="border-b border-pc-border text-[11px] uppercase tracking-wide text-pc-muted">
                  <tr>
                    <th className="px-4 py-3">Patente</th>
                    <th className="px-4 py-3">Cliente</th>
                    <th className="px-4 py-3">Vuelo</th>
                    <th className="px-4 py-3">Llegada</th>
                    <th className="px-4 py-3">Fila</th>
                    <th className="px-4 py-3">Estado</th>
                  </tr>
                </thead>
                <tbody>
                  {resumen.vuelos.length === 0 && (
                    <tr>
                      <td colSpan={6} className="px-4 py-8 text-center text-pc-muted">
                        No hay vuelos asociados a vehículos activos.
                      </td>
                    </tr>
                  )}
                  {resumen.vuelos.map((vuelo) => (
                    <tr key={vuelo.idIngreso} className="border-b border-pc-border/60 last:border-0">
                      <td className="px-4 py-3 font-semibold">{vuelo.patente ?? '—'}</td>
                      <td className="px-4 py-3">{vuelo.cliente}</td>
                      <td className="px-4 py-3">{vuelo.numeroVuelo ?? '—'}</td>
                      <td className="px-4 py-3 text-xs">
                        {vuelo.fechaRegreso ? `${fechaCorta(vuelo.fechaRegreso)} ${vuelo.horaRegreso ?? ''}` : '—'}
                      </td>
                      <td className="px-4 py-3">{vuelo.fila ?? '—'}</td>
                      <td className="px-4 py-3">
                        <span className={`rounded-full border px-2 py-1 text-[10px] font-semibold uppercase ${COLOR_ESTADO[vuelo.estado] ?? 'border-pc-border text-pc-muted'}`}>
                          {vuelo.estado}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </section>
        </>
      )}
    </div>
  )
}
