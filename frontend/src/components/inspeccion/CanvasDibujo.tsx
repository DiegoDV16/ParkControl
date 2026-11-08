import {
  forwardRef,
  useEffect,
  useImperativeHandle,
  useRef,
  type PointerEvent as ReactPointerEvent,
} from 'react'
import type { CanvasDibujoHandle } from './tipos'

interface Props {
  color: string
  deshabilitado?: boolean
  grosor?: number
  className?: string
}

/**
 * Capa de dibujo a mano alzada sobre una vista del vehículo.
 * Usa Pointer Events, que cubren mouse, dedo y lápiz.
 */
export const CanvasDibujo = forwardRef<CanvasDibujoHandle, Props>(
  function CanvasDibujo({ color, deshabilitado = false, grosor = 3, className = '' }, ref) {
    const canvasRef = useRef<HTMLCanvasElement>(null)
    const contexto = useRef<CanvasRenderingContext2D | null>(null)
    const dibujando = useRef(false)
    const historial = useRef<ImageData[]>([])
    const conTrazos = useRef(false)

    useEffect(() => {
      const canvas = canvasRef.current
      const contenedor = canvas?.parentElement
      if (!canvas || !contenedor) return

      const marco = requestAnimationFrame(() => {
        const rect = contenedor.getBoundingClientRect()
        const dpr = window.devicePixelRatio || 1
        canvas.width = Math.max(1, Math.round(rect.width * dpr))
        canvas.height = Math.max(1, Math.round(rect.height * dpr))

        const ctx = canvas.getContext('2d')
        if (!ctx) return
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0)
        ctx.lineCap = 'round'
        ctx.lineJoin = 'round'
        ctx.lineWidth = grosor
        ctx.strokeStyle = color
        contexto.current = ctx
      })

      return () => cancelAnimationFrame(marco)
      // El tamaño se fija al montar; el canvas se escala por CSS si cambia el contenedor.
      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [])

    useEffect(() => {
      const ctx = contexto.current
      if (ctx) {
        ctx.strokeStyle = color
        ctx.lineWidth = grosor
      }
    }, [color, grosor])

    function coordenadas(evento: ReactPointerEvent<HTMLCanvasElement>) {
      const rect = evento.currentTarget.getBoundingClientRect()
      return { x: evento.clientX - rect.left, y: evento.clientY - rect.top }
    }

    function guardarHistorial() {
      const canvas = canvasRef.current
      const ctx = contexto.current
      if (!canvas || !ctx) return
      try {
        historial.current.push(ctx.getImageData(0, 0, canvas.width, canvas.height))
        if (historial.current.length > 20) historial.current.shift()
      } catch {
        // Si el canvas aún no tiene tamaño, no hay nada que guardar.
      }
    }

    useImperativeHandle(ref, () => ({
      limpiar() {
        const canvas = canvasRef.current
        const ctx = contexto.current
        if (!canvas || !ctx) return
        ctx.save()
        ctx.setTransform(1, 0, 0, 1, 0, 0)
        ctx.clearRect(0, 0, canvas.width, canvas.height)
        ctx.restore()
        historial.current = []
        conTrazos.current = false
      },
      deshacer() {
        const canvas = canvasRef.current
        const ctx = contexto.current
        if (!canvas || !ctx) return
        const anterior = historial.current.pop()
        if (anterior) {
          ctx.putImageData(anterior, 0, 0)
        } else {
          ctx.save()
          ctx.setTransform(1, 0, 0, 1, 0, 0)
          ctx.clearRect(0, 0, canvas.width, canvas.height)
          ctx.restore()
        }
      },
      exportarCanvas() {
        return canvasRef.current
      },
      tieneTrazos() {
        return conTrazos.current
      },
    }))

    return (
      <canvas
        ref={canvasRef}
        className={`absolute inset-0 h-full w-full ${deshabilitado ? 'cursor-not-allowed' : 'cursor-crosshair'} ${className}`}
        style={{ touchAction: 'none' }}
        onPointerDown={(evento) => {
          if (deshabilitado) return
          const ctx = contexto.current
          if (!ctx) return
          evento.stopPropagation()
          evento.currentTarget.setPointerCapture(evento.pointerId)
          guardarHistorial()
          const punto = coordenadas(evento)
          ctx.beginPath()
          ctx.moveTo(punto.x, punto.y)
          dibujando.current = true
        }}
        onPointerMove={(evento) => {
          if (!dibujando.current || deshabilitado) return
          const ctx = contexto.current
          if (!ctx) return
          const punto = coordenadas(evento)
          ctx.lineTo(punto.x, punto.y)
          ctx.stroke()
          conTrazos.current = true
        }}
        onPointerUp={() => {
          dibujando.current = false
        }}
        onPointerLeave={() => {
          dibujando.current = false
        }}
        onPointerCancel={() => {
          dibujando.current = false
        }}
      />
    )
  },
)
