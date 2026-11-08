import {
  forwardRef,
  useImperativeHandle,
  useRef,
  useState,
} from 'react'
import { CanvasDibujo } from './CanvasDibujo'
import { VistaVehiculo } from './VistasVehiculo'
import type { CanvasDibujoHandle, InspeccionExportada, InspeccionHandle, VistaInspeccion } from './tipos'

interface Props {
  onGuardar?: (resultado: InspeccionExportada) => Promise<void> | void
  deshabilitado?: boolean
  className?: string
}

interface ConfigVista {
  vista: VistaInspeccion
  etiqueta: string
  aspecto: string
  vw: number
  vh: number
}

const VISTAS: ConfigVista[] = [
  { vista: 'frontal', etiqueta: 'Frontal', aspecto: '200 / 160', vw: 200, vh: 160 },
  { vista: 'lateral_izquierda', etiqueta: 'Lateral izquierdo', aspecto: '320 / 140', vw: 320, vh: 140 },
  { vista: 'superior', etiqueta: 'Superior', aspecto: '160 / 260', vw: 160, vh: 260 },
  { vista: 'trasera', etiqueta: 'Trasera', aspecto: '200 / 160', vw: 200, vh: 160 },
  { vista: 'lateral_derecha', etiqueta: 'Lateral derecho', aspecto: '320 / 140', vw: 320, vh: 140 },
]

const COLORES = [
  { nombre: 'Daño / golpe fuerte', valor: '#ef4444' },
  { nombre: 'Rayón leve', valor: '#f59e0b' },
]

function cargarImagen(origen: string): Promise<HTMLImageElement> {
  return new Promise((resolver, rechazar) => {
    const imagen = new Image()
    imagen.onload = () => resolver(imagen)
    imagen.onerror = () => rechazar(new Error('No se pudo rasterizar la vista.'))
    imagen.src = origen
  })
}

function encajar(vw: number, vh: number, x: number, y: number, w: number, h: number) {
  const escala = Math.min(w / vw, h / vh)
  const ancho = vw * escala
  const alto = vh * escala
  return { x: x + (w - ancho) / 2, y: y + (h - alto) / 2, w: ancho, h: alto }
}

function blobADataUrl(blob: Blob): Promise<string> {
  return new Promise((resolver, rechazar) => {
    const lector = new FileReader()
    lector.onload = () => resolver(String(lector.result))
    lector.onerror = () => rechazar(lector.error)
    lector.readAsDataURL(blob)
  })
}

export const InspeccionVehicular = forwardRef<InspeccionHandle, Props>(
  function InspeccionVehicular({ onGuardar, deshabilitado = false, className = '' }, ref) {
    const [sinDanios, setSinDanios] = useState(false)
    const [color, setColor] = useState(COLORES[0].valor)
    const [guardando, setGuardando] = useState(false)
    const [error, setError] = useState<string | null>(null)

    const lienzos = useRef<Partial<Record<VistaInspeccion, CanvasDibujoHandle | null>>>({})
    const contenedores = useRef<Partial<Record<VistaInspeccion, HTMLDivElement | null>>>({})

    const bloqueado = deshabilitado || sinDanios

    function limpiarTodo() {
      VISTAS.forEach(({ vista }) => lienzos.current[vista]?.limpiar())
    }

    async function exportar(): Promise<InspeccionExportada> {
      const ancho = 1120
      const alto = 740
      const lienzo = document.createElement('canvas')
      lienzo.width = ancho
      lienzo.height = alto
      const ctx = lienzo.getContext('2d')
      if (!ctx) throw new Error('No se pudo crear el lienzo de exportación.')

      ctx.fillStyle = '#f8fafc'
      ctx.fillRect(0, 0, ancho, alto)

      const celdas = [
        { vista: 'frontal' as VistaInspeccion, x: 20, y: 60, w: 350, h: 300 },
        { vista: 'lateral_izquierda' as VistaInspeccion, x: 390, y: 60, w: 350, h: 300 },
        { vista: 'superior' as VistaInspeccion, x: 760, y: 60, w: 340, h: 300 },
        { vista: 'trasera' as VistaInspeccion, x: 20, y: 380, w: 350, h: 300 },
        { vista: 'lateral_derecha' as VistaInspeccion, x: 390, y: 380, w: 350, h: 300 },
      ]

      for (const celda of celdas) {
        const config = VISTAS.find((item) => item.vista === celda.vista)
        if (!config) continue

        ctx.fillStyle = '#ffffff'
        ctx.strokeStyle = '#cbd5e1'
        ctx.lineWidth = 1
        ctx.fillRect(celda.x, celda.y, celda.w, celda.h)
        ctx.strokeRect(celda.x, celda.y, celda.w, celda.h)

        const rect = encajar(config.vw, config.vh, celda.x + 10, celda.y + 10, celda.w - 20, celda.h - 40)
        const contenedor = contenedores.current[celda.vista]
        const svg = contenedor?.querySelector('svg')

        if (svg) {
          const xml = new XMLSerializer().serializeToString(svg)
          const imagen = await cargarImagen(`data:image/svg+xml;charset=utf-8,${encodeURIComponent(xml)}`)

          if (celda.vista === 'lateral_derecha') {
            ctx.save()
            ctx.translate(rect.x + rect.w, rect.y)
            ctx.scale(-1, 1)
            ctx.drawImage(imagen, 0, 0, rect.w, rect.h)
            ctx.restore()
          } else {
            ctx.drawImage(imagen, rect.x, rect.y, rect.w, rect.h)
          }
        }

        const capa = lienzos.current[celda.vista]?.exportarCanvas()
        if (capa && !sinDanios) {
          ctx.drawImage(capa, rect.x, rect.y, rect.w, rect.h)
        }

        ctx.fillStyle = '#334155'
        ctx.font = 'bold 13px sans-serif'
        ctx.textAlign = 'center'
        ctx.fillText(config.etiqueta.toUpperCase(), celda.x + celda.w / 2, celda.y + celda.h - 14)
      }

      ctx.fillStyle = '#334155'
      ctx.font = 'bold 20px sans-serif'
      ctx.textAlign = 'left'
      ctx.fillText('INSPECCIÓN VEHICULAR', 20, 34)
      ctx.font = '12px sans-serif'
      ctx.textAlign = 'right'
      ctx.fillText(new Date().toLocaleString('es-CL'), ancho - 20, 34)

      ctx.textAlign = 'left'
      ctx.font = 'bold 14px sans-serif'
      if (sinDanios) {
        ctx.fillStyle = '#15803d'
        ctx.fillText('SIN DAÑOS VISIBLES', 790, 400)
      } else {
        ctx.fillStyle = '#334155'
        ctx.fillText('Ficha de inspección', 790, 400)
        ctx.font = '12px sans-serif'
        ctx.fillText('Marcas en rojo: daño / golpe fuerte.', 790, 424)
        ctx.fillText('Marcas en amarillo: rayón leve.', 790, 444)
        ctx.fillText('Vehículo revisado al ingreso.', 790, 464)
      }

      const blob = await new Promise<Blob>((resolver, rechazar) => {
        lienzo.toBlob((resultado) => {
          if (resultado) resolver(resultado)
          else rechazar(new Error('No se pudo generar la imagen.'))
        }, 'image/webp', 0.92)
      })

      return { blob, dataUrl: await blobADataUrl(blob), sinDanios }
    }

    useImperativeHandle(ref, () => ({ exportar, limpiarTodo }))

    async function guardar() {
      if (!onGuardar) return
      setGuardando(true)
      setError(null)

      try {
        await onGuardar(await exportar())
      } catch (fallo) {
        setError(fallo instanceof Error ? fallo.message : 'No se pudo guardar la inspección.')
      } finally {
        setGuardando(false)
      }
    }

    function tarjeta(config: ConfigVista) {
      return (
        <div key={config.vista} className="rounded-xl border border-slate-300 bg-white p-2">
          <div className="mb-1 flex items-center justify-between gap-2">
            <span className="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-600">
              {config.etiqueta}
            </span>
            <div className="flex gap-1">
              <button type="button" title="Deshacer" disabled={bloqueado}
                className="h-6 w-7 rounded border border-slate-300 text-xs text-slate-600 disabled:opacity-40"
                onClick={() => lienzos.current[config.vista]?.deshacer()}>
                ↶
              </button>
              <button type="button" disabled={bloqueado}
                className="h-6 rounded border border-slate-300 px-1.5 text-[10px] text-slate-600 disabled:opacity-40"
                onClick={() => lienzos.current[config.vista]?.limpiar()}>
                Limpiar
              </button>
            </div>
          </div>

          <div
            ref={(elemento) => { contenedores.current[config.vista] = elemento }}
            className="relative w-full"
            style={{ aspectRatio: config.aspecto }}
          >
            <VistaVehiculo vista={config.vista} />
            <CanvasDibujo
              ref={(elemento) => { lienzos.current[config.vista] = elemento }}
              color={color}
              deshabilitado={bloqueado}
            />
          </div>
        </div>
      )
    }

    return (
      <div className={`space-y-3 ${className}`}>
        <div className="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-300 bg-white px-3 py-2">
          <label className="flex min-h-[40px] cursor-pointer items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" checked={sinDanios}
              onChange={(evento) => setSinDanios(evento.target.checked)} />
            Sin daños visibles
          </label>

          <div className="flex items-center gap-2">
            {COLORES.map((item) => (
              <button key={item.valor} type="button" title={item.nombre}
                className={`h-8 w-8 rounded-full border-2 ${color === item.valor ? 'border-slate-700' : 'border-transparent'}`}
                style={{ backgroundColor: item.valor }}
                onClick={() => setColor(item.valor)}
                aria-label={item.nombre} />
            ))}
            <button type="button" className="pc-btn-ghost px-2 text-xs" onClick={limpiarTodo} disabled={bloqueado}>
              Limpiar todo
            </button>
          </div>

          {onGuardar && (
            <button type="button" className="pc-btn-primary" onClick={() => void guardar()}
              disabled={guardando || deshabilitado}>
              {guardando ? 'Guardando…' : 'Guardar inspección'}
            </button>
          )}
        </div>

        {error && (
          <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-3 py-2 text-sm text-pc-danger">
            {error}
          </div>
        )}

        <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
          {tarjeta(VISTAS[0])}
          {tarjeta(VISTAS[1])}
          {tarjeta(VISTAS[2])}
          {tarjeta(VISTAS[3])}
          {tarjeta(VISTAS[4])}

          <div className="rounded-xl border border-slate-300 bg-white p-3 text-xs text-slate-600">
            <p className="text-sm font-bold text-slate-700">Inspección vehicular</p>
            <p className="mt-2">Dibuja con el dedo o lápiz sobre cada vista.</p>
            <p className="mt-1">Rojo: daño o golpe fuerte.</p>
            <p className="mt-1">Amarillo: rayón leve.</p>
            <p className="mt-1">Usa Deshacer o Limpiar por vista.</p>
          </div>
        </div>
      </div>
    )
  },
)
