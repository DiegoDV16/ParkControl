export type VistaInspeccion =
  | 'frontal'
  | 'trasera'
  | 'lateral_izquierda'
  | 'lateral_derecha'
  | 'superior'

export interface InspeccionExportada {
  blob: Blob
  dataUrl: string
  sinDanios: boolean
}

export interface CanvasDibujoHandle {
  limpiar: () => void
  deshacer: () => void
  exportarCanvas: () => HTMLCanvasElement | null
  tieneTrazos: () => boolean
}

export interface InspeccionHandle {
  exportar: () => Promise<InspeccionExportada>
  limpiarTodo: () => void
}
