import { useEffect, useRef, useState } from 'react'
import { Modal } from '../Modal'

interface Props {
  abierto: boolean
  onCerrar: () => void
  onCapturar: (archivo: File) => void
}

function mensajeError(fallo: unknown): string {
  if (typeof window !== 'undefined' && !window.isSecureContext) {
    return 'La cámara requiere HTTPS o abrir la app en localhost. Si estás entrando por una IP de la red, el navegador bloquea la cámara.'
  }

  const nombre = fallo instanceof DOMException ? fallo.name : ''

  if (nombre === 'NotAllowedError' || nombre === 'SecurityError') {
    return 'Permiso de cámara denegado. Habilítalo en el navegador para este sitio.'
  }
  if (nombre === 'NotFoundError' || nombre === 'OverconstrainedError') {
    return 'No se encontró una cámara disponible en este dispositivo.'
  }
  if (nombre === 'NotReadableError') {
    return 'La cámara está siendo usada por otra aplicación.'
  }

  return 'No se pudo iniciar la cámara.'
}

export function CamaraFoto({ abierto, onCerrar, onCapturar }: Props) {
  const video = useRef<HTMLVideoElement>(null)
  const flujo = useRef<MediaStream | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [lista, setLista] = useState(false)
  const [frente, setFrente] = useState<'environment' | 'user'>('environment')
  const [capturando, setCapturando] = useState(false)

  useEffect(() => {
    if (!abierto) return

    let cancelado = false
    setError(null)
    setLista(false)

    async function iniciar() {
      if (!navigator.mediaDevices?.getUserMedia) {
        setError('Este navegador no permite acceder a la cámara. Usa “Subir archivo”.')
        return
      }

      try {
        const stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: frente },
          audio: false,
        })

        if (cancelado) {
          stream.getTracks().forEach((track) => track.stop())
          return
        }

        flujo.current = stream
        if (video.current) {
          video.current.srcObject = stream
          await video.current.play()
          setLista(true)
        }
      } catch (fallo) {
        setError(mensajeError(fallo))
      }
    }

    void iniciar()

    return () => {
      cancelado = true
      flujo.current?.getTracks().forEach((track) => track.stop())
      flujo.current = null
    }
  }, [abierto, frente])

  function cerrar() {
    flujo.current?.getTracks().forEach((track) => track.stop())
    flujo.current = null
    onCerrar()
  }

  function capturar() {
    const elemento = video.current
    if (!elemento || !lista) return

    setCapturando(true)

    const lienzo = document.createElement('canvas')
    lienzo.width = elemento.videoWidth
    lienzo.height = elemento.videoHeight
    const contexto = lienzo.getContext('2d')

    if (!contexto || lienzo.width === 0 || lienzo.height === 0) {
      setError('La cámara aún no está lista. Intenta nuevamente.')
      setCapturando(false)
      return
    }

    contexto.drawImage(elemento, 0, 0, lienzo.width, lienzo.height)

    lienzo.toBlob((blob) => {
      if (blob) {
        onCapturar(new File([blob], `foto-${Date.now()}.jpg`, { type: 'image/jpeg' }))
      } else {
        setError('No se pudo generar la fotografía.')
      }
      setCapturando(false)
    }, 'image/jpeg', 0.9)
  }

  if (!abierto) return null

  return (
    <Modal abierto titulo="Tomar fotografía" onCerrar={cerrar} ancho="max-w-2xl">
      <div className="space-y-3">
        {error ? (
          <div className="rounded-lg border border-pc-danger/40 bg-pc-danger/10 px-4 py-3 text-sm text-pc-danger">
            {error}
          </div>
        ) : (
          <div className="relative overflow-hidden rounded-xl border border-pc-border bg-black">
            <video
              ref={video}
              autoPlay
              playsInline
              muted
              className="mx-auto max-h-[65vh] w-full object-contain"
              style={frente === 'user' ? { transform: 'scaleX(-1)' } : undefined}
            />
            {!lista && (
              <div className="absolute inset-0 flex items-center justify-center text-sm text-white">
                Iniciando cámara…
              </div>
            )}
          </div>
        )}

        <div className="flex flex-wrap justify-end gap-2 border-t border-pc-border pt-4">
          <button type="button" className="pc-btn-ghost" onClick={cerrar}>
            Cancelar
          </button>
          <button
            type="button"
            className="pc-btn-ghost"
            onClick={() => setFrente((valor) => (valor === 'environment' ? 'user' : 'environment'))}
          >
            Girar cámara
          </button>
          <button type="button" className="pc-btn-primary" disabled={!lista || capturando} onClick={capturar}>
            {capturando ? 'Capturando…' : 'Capturar foto'}
          </button>
        </div>
      </div>
    </Modal>
  )
}
