import { useCallback, useEffect, useState, type FormEvent } from 'react'
import { useAuth } from '../auth/AuthContext'
import {
  ApiError,
  crearIngreso,
  listarFilas,
  listarIngresos,
  obtenerCatalogos,
} from '../lib/api'
import type { Catalogos, Fila, IngresoResumen } from '../types'

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

function fechaHoy(): string {
  return new Date().toISOString().slice(0, 10)
}

function horaAhora(): string {
  return new Date().toTimeString().slice(0, 5)
}

function formularioInicial() {
  return {
    rut: '',
    nombres: '',
    apellidoPaterno: '',
    apellidoMaterno: '',
    telefono: '',
    correo: '',
    patente: '',
    idMarca: '',
    idModelo: '',
    idTipoVehiculo: '',
    idFila: '',
    idServicioExtra: '',
    fechaInicio: fechaHoy(),
    horaInicio: horaAhora(),
    fechaRegreso: '',
    horaRegreso: '',
    numeroVuelo: '',
    llaves: false,
    observacion: '',
  }
}

export function RecepcionPage() {
  const { usuario } = useAuth()
  const esJefatura = usuario?.idRol === 3

  const [catalogos, setCatalogos] = useState<Catalogos | null>(null)
  const [filas, setFilas] = useState<Fila[]>([])
  const [ingresos, setIngresos] = useState<IngresoResumen[]>([])
  const [cargando, setCargando] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [ok, setOk] = useState<string | null>(null)
  const [enviando, setEnviando] = useState(false)
  const [form, setForm] = useState(formularioInicial())

  const cargar = useCallback(async () => {
    setCargando(true)
    setError(null)

    try {
      const [cat, fil, ing] = await Promise.all([
        obtenerCatalogos(),
        listarFilas(),
        listarIngresos(),
      ])
      setCatalogos(cat)
      setFilas(fil.data)
      setIngresos(ing.data)
    } catch (fallo) {
      setError(mensajeDeError(fallo))
    } finally {
      setCargando(false)
    }
  }, [])

  useEffect(() => {
    void cargar()
  }, [cargar])

  function campo<K extends keyof ReturnType<typeof formularioInicial>>(
    nombre: K,
    valor: ReturnType<typeof formularioInicial>[K],
  ) {
    setForm((previo) => ({ ...previo, [nombre]: valor }))
  }

  const modelos = catalogos?.marcas.find((marca) => String(marca.idMarca) === form.idMarca)?.modelos ?? []
  const filaSeleccionada = filas.find((fila) => String(fila.idFila) === form.idFila)
  const servicioSeleccionado = catalogos?.servicios.find(
    (servicio) => String(servicio.idServicioExtra) === form.idServicioExtra,
  )

  async function enviar(evento: FormEvent<HTMLFormElement>) {
    evento.preventDefault()
    setEnviando(true)
    setError(null)
    setOk(null)

    try {
      const respuesta = await crearIngreso({
        rut: form.rut.trim(),
        nombres: form.nombres.trim(),
        apellidoPaterno: form.apellidoPaterno.trim(),
        apellidoMaterno: form.apellidoMaterno.trim() || null,
        telefono: form.telefono.trim() || null,
        correo: form.correo.trim() || null,
        patente: form.patente.trim().toUpperCase(),
        idModelo: Number(form.idModelo),
        idTipoVehiculo: Number(form.idTipoVehiculo),
        idFila: Number(form.idFila),
        idServicioExtra: form.idServicioExtra ? Number(form.idServicioExtra) : null,
        fechaInicio: form.fechaInicio,
        horaInicio: form.horaInicio,
        fechaRegreso: form.fechaRegreso || null,
        horaRegreso: form.horaRegreso || null,
        numeroVuelo: form.numeroVuelo.trim() || null,
        llaves: form.llaves ? 1 : 0,
        observacion: form.observacion.trim() || null,
      })

      setOk(
        `Ingreso #${respuesta.data.idIngreso} registrado para ${respuesta.data.vehiculo.patente} `
        + `en la fila ${respuesta.data.fila}. Monto proyectado: $${respuesta.data.montoProyectado.toLocaleString('es-CL')}.`,
      )
      setForm(formularioInicial())
      await cargar()
    } catch (fallo) {
      setError(mensajeDeError(fallo))
    } finally {
      setEnviando(false)
    }
  }

  return (
    <div className="space-y-5">
      <div>
        <h1 className="text-xl font-bold">Recepción en patio</h1>
        <p className="text-sm text-pc-muted">
          Registra cliente, vehículo y fila. El cliente se reutiliza por RUT y el vehículo por patente.
        </p>
      </div>

      {ok && (
        <div className="rounded-lg border border-pc-mint/40 bg-pc-mint/10 px-4 py-3 text-sm text-pc-mint">
          {ok}
        </div>
      )}
      {error && (
        <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-4 py-3 text-sm text-pc-danger">
          {error}
        </div>
      )}

      {esJefatura ? (
        <div className="pc-card p-6 text-sm text-pc-muted">
          Tu rol es de solo lectura. La recepción la realizan operadores o administradores.
        </div>
      ) : (
        <form onSubmit={enviar} className="pc-card space-y-5 p-5" noValidate>
          <section>
            <h2 className="mb-3 text-[11px] font-bold uppercase tracking-[0.14em] text-pc-mint">Datos del cliente</h2>
            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
              <div>
                <label htmlFor="rRut" className="pc-label">RUT *</label>
                <input id="rRut" className="pc-input" maxLength={13} required value={form.rut}
                  onChange={(evento) => campo('rut', evento.target.value)} placeholder="12.345.678-9" />
              </div>
              <div>
                <label htmlFor="rNombres" className="pc-label">Nombres *</label>
                <input id="rNombres" className="pc-input" maxLength={65} required value={form.nombres}
                  onChange={(evento) => campo('nombres', evento.target.value)} />
              </div>
              <div>
                <label htmlFor="rApellidoPaterno" className="pc-label">Apellido paterno *</label>
                <input id="rApellidoPaterno" className="pc-input" maxLength={30} required value={form.apellidoPaterno}
                  onChange={(evento) => campo('apellidoPaterno', evento.target.value)} />
              </div>
              <div>
                <label htmlFor="rApellidoMaterno" className="pc-label">Apellido materno</label>
                <input id="rApellidoMaterno" className="pc-input" maxLength={30} value={form.apellidoMaterno}
                  onChange={(evento) => campo('apellidoMaterno', evento.target.value)} />
              </div>
              <div>
                <label htmlFor="rTelefono" className="pc-label">Teléfono</label>
                <input id="rTelefono" className="pc-input" maxLength={13} value={form.telefono}
                  onChange={(evento) => campo('telefono', evento.target.value)} placeholder="+56912345678" />
              </div>
              <div>
                <label htmlFor="rCorreo" className="pc-label">Correo</label>
                <input id="rCorreo" type="email" className="pc-input" maxLength={65} value={form.correo}
                  onChange={(evento) => campo('correo', evento.target.value)} />
              </div>
            </div>
          </section>

          <section>
            <h2 className="mb-3 text-[11px] font-bold uppercase tracking-[0.14em] text-pc-mint">Vehículo</h2>
            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
              <div>
                <label htmlFor="rPatente" className="pc-label">Patente *</label>
                <input id="rPatente" className="pc-input text-uppercase" maxLength={7} required value={form.patente}
                  onChange={(evento) => campo('patente', evento.target.value)} placeholder="BBBB12" />
              </div>
              <div>
                <label htmlFor="rMarca" className="pc-label">Marca *</label>
                <select id="rMarca" className="pc-input" required value={form.idMarca}
                  onChange={(evento) => { campo('idMarca', evento.target.value); campo('idModelo', '') }}>
                  <option value="">Selecciona</option>
                  {catalogos?.marcas.map((marca) => (
                    <option key={marca.idMarca} value={marca.idMarca}>{marca.marca}</option>
                  ))}
                </select>
              </div>
              <div>
                <label htmlFor="rModelo" className="pc-label">Modelo *</label>
                <select id="rModelo" className="pc-input" required value={form.idModelo}
                  onChange={(evento) => campo('idModelo', evento.target.value)}>
                  <option value="">Selecciona</option>
                  {modelos.map((modelo) => (
                    <option key={modelo.idModelo} value={modelo.idModelo}>{modelo.modelo}</option>
                  ))}
                </select>
              </div>
              <div>
                <label htmlFor="rTipo" className="pc-label">Tipo *</label>
                <select id="rTipo" className="pc-input" required value={form.idTipoVehiculo}
                  onChange={(evento) => campo('idTipoVehiculo', evento.target.value)}>
                  <option value="">Selecciona</option>
                  {catalogos?.tipos.map((tipo) => (
                    <option key={tipo.idTipoVehiculo} value={tipo.idTipoVehiculo}>{tipo.tipoVehiculo}</option>
                  ))}
                </select>
              </div>
            </div>
          </section>

          <section>
            <h2 className="mb-3 text-[11px] font-bold uppercase tracking-[0.14em] text-pc-mint">Estadía</h2>
            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
              <div>
                <label htmlFor="rFila" className="pc-label">Fila *</label>
                <select id="rFila" className="pc-input" required value={form.idFila}
                  onChange={(evento) => campo('idFila', evento.target.value)}>
                  <option value="">Selecciona</option>
                  {filas.map((fila) => (
                    <option key={fila.idFila} value={fila.idFila} disabled={fila.libres <= 0}>
                      {fila.fila} · {fila.libres} libres
                    </option>
                  ))}
                </select>
                {filaSeleccionada && (
                  <p className="mt-1 text-xs text-pc-muted">
                    Ocupación: {filaSeleccionada.ocupadas}/{filaSeleccionada.capacidadTotal}
                  </p>
                )}
              </div>
              <div>
                <label htmlFor="rServicio" className="pc-label">Servicio extra</label>
                <select id="rServicio" className="pc-input" value={form.idServicioExtra}
                  onChange={(evento) => campo('idServicioExtra', evento.target.value)}>
                  <option value="">Sin servicio</option>
                  {catalogos?.servicios.map((servicio) => (
                    <option key={servicio.idServicioExtra} value={servicio.idServicioExtra}>
                      {servicio.servicioExtra} · ${Number(servicio.valor).toLocaleString('es-CL')}
                    </option>
                  ))}
                </select>
              </div>
              <div>
                <label htmlFor="rFechaInicio" className="pc-label">Fecha inicio *</label>
                <input id="rFechaInicio" type="date" className="pc-input" required value={form.fechaInicio}
                  onChange={(evento) => campo('fechaInicio', evento.target.value)} />
              </div>
              <div>
                <label htmlFor="rHoraInicio" className="pc-label">Hora inicio *</label>
                <input id="rHoraInicio" type="time" className="pc-input" required value={form.horaInicio}
                  onChange={(evento) => campo('horaInicio', evento.target.value)} />
              </div>
              <div>
                <label htmlFor="rFechaRegreso" className="pc-label">Fecha regreso</label>
                <input id="rFechaRegreso" type="date" className="pc-input" value={form.fechaRegreso}
                  onChange={(evento) => campo('fechaRegreso', evento.target.value)} />
              </div>
              <div>
                <label htmlFor="rHoraRegreso" className="pc-label">Hora regreso</label>
                <input id="rHoraRegreso" type="time" className="pc-input" value={form.horaRegreso}
                  onChange={(evento) => campo('horaRegreso', evento.target.value)} />
              </div>
              <div>
                <label htmlFor="rVuelo" className="pc-label">Número de vuelo</label>
                <input id="rVuelo" className="pc-input" maxLength={12} value={form.numeroVuelo}
                  onChange={(evento) => campo('numeroVuelo', evento.target.value)} />
              </div>
              <div className="flex items-end pb-3">
                <label className="flex min-h-[44px] cursor-pointer items-center gap-2 text-sm">
                  <input type="checkbox" checked={form.llaves}
                    onChange={(evento) => campo('llaves', evento.target.checked)} />
                  Se retienen las llaves
                </label>
              </div>
            </div>
            <div className="mt-3">
              <label htmlFor="rObservacion" className="pc-label">Observaciones</label>
              <textarea id="rObservacion" className="pc-input" rows={2} maxLength={500} value={form.observacion}
                onChange={(evento) => campo('observacion', evento.target.value)} />
            </div>
            {servicioSeleccionado && (
              <p className="mt-2 text-xs text-pc-muted">
                Servicio seleccionado: {servicioSeleccionado.servicioExtra}. El monto final se calcula con los
                días de estadía y el tipo de vehículo.
              </p>
            )}
          </section>

          <div className="flex justify-end border-t border-pc-border pt-4">
            <button type="submit" className="pc-btn-primary" disabled={enviando}>
              {enviando ? 'Registrando…' : 'Registrar ingreso'}
            </button>
          </div>
        </form>
      )}

      <section className="pc-card overflow-hidden">
        <div className="flex items-center justify-between border-b border-pc-border px-4 py-3">
          <h2 className="text-sm font-semibold">Vehículos activos</h2>
          <span className="text-xs text-pc-muted">{ingresos.length} en patio</span>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full min-w-[820px] text-left text-sm">
            <thead className="border-b border-pc-border text-[11px] uppercase tracking-wide text-pc-muted">
              <tr>
                <th className="px-4 py-3">Patente</th>
                <th className="px-4 py-3">Cliente</th>
                <th className="px-4 py-3">Fila</th>
                <th className="px-4 py-3">Entrada</th>
                <th className="px-4 py-3">Servicio</th>
                <th className="px-4 py-3">Días</th>
                <th className="px-4 py-3 text-right">Monto proyectado</th>
              </tr>
            </thead>
            <tbody>
              {cargando && (
                <tr>
                  <td colSpan={7} className="px-4 py-8 text-center text-pc-muted">Cargando…</td>
                </tr>
              )}

              {!cargando && ingresos.length === 0 && (
                <tr>
                  <td colSpan={7} className="px-4 py-8 text-center text-pc-muted">
                    No hay vehículos activos en este momento.
                  </td>
                </tr>
              )}

              {!cargando && ingresos.map((ingreso) => (
                <tr key={ingreso.idIngreso} className="border-b border-pc-border/60 last:border-0">
                  <td className="px-4 py-3 font-semibold">{ingreso.vehiculo.patente}</td>
                  <td className="px-4 py-3">
                    <div>{ingreso.cliente.nombre}</div>
                    <div className="text-xs text-pc-muted">{ingreso.cliente.rut}</div>
                  </td>
                  <td className="px-4 py-3">{ingreso.fila}</td>
                  <td className="px-4 py-3 text-xs">
                    {ingreso.fechaInicio} {ingreso.horaInicio.slice(0, 5)}
                  </td>
                  <td className="px-4 py-3 text-xs">{ingreso.servicio ?? 'Sin servicio'}</td>
                  <td className="px-4 py-3">{ingreso.dias}</td>
                  <td className="px-4 py-3 text-right font-semibold">
                    ${ingreso.montoProyectado.toLocaleString('es-CL')}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      <p className="text-xs text-pc-muted">
        La salida y el cobro se implementan en la siguiente fase. La inspección de daños en canvas queda pendiente.
      </p>
    </div>
  )
}
