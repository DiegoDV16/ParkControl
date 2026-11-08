export interface Usuario {
  idUsuario: number
  idRol: number
  rol: string | null
  rut: string
  nombres: string
  apellidoPaterno: string
  apellidoMaterno: string | null
  correo: string
  estado: number
  ultimoAcceso?: string | null
  creadoEn?: string | null
}

export interface Rol {
  idRol: number
  rol: string
}

export interface LoginResponse {
  token: string
  token_type: string
  usuario: Usuario
}

export interface PerfilResponse {
  usuario: Usuario
}

export interface Paginated<T> {
  data: T[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export interface PerfilPayload {
  nombres: string
  apellidoPaterno: string
  apellidoMaterno?: string | null
}

export interface Fila {
  idFila: number
  fila: string
  capacidadTotal: number
  estado: number
  ocupadas: number
  libres: number
}

export interface ModeloCatalogo {
  idModelo: number
  modelo: string
}

export interface MarcaCatalogo {
  idMarca: number
  marca: string
  modelos: ModeloCatalogo[]
}

export interface ServicioCatalogo {
  idServicioExtra: number
  servicioExtra: string
  valor: number | string
}

export interface TipoVehiculoCatalogo {
  idTipoVehiculo: number
  tipoVehiculo: string
}

export interface Catalogos {
  marcas: MarcaCatalogo[]
  servicios: ServicioCatalogo[]
  tipos: TipoVehiculoCatalogo[]
}

export type VistaDanio =
  | 'superior'
  | 'lateral_izquierda'
  | 'lateral_derecha'
  | 'frontal'
  | 'trasera'

export interface Danio {
  idDanio: number
  vista: VistaDanio
  coordX: number
  coordY: number
  tipoDanio: string
  detalle: string | null
}

export interface IngresoResumen {
  idIngreso: number
  estado: string
  fechaInicio: string
  horaInicio: string
  fechaRegreso: string | null
  horaRegreso: string | null
  numeroVuelo: string | null
  llaves: number
  fila: string
  cliente: { idCliente: number; rut: string; nombre: string }
  vehiculo: { idVehiculo: number; patente: string; marca: string | null; modelo: string | null; tipo: string | null }
  servicio: string | null
  dias: number
  montoProyectado: number
  danios?: Danio[]
}

export interface IngresoPayload {
  rut: string
  nombres: string
  apellidoPaterno: string
  apellidoMaterno?: string | null
  telefono?: string | null
  correo?: string | null
  patente: string
  idModelo: number
  idTipoVehiculo: number
  idFila: number
  idServicioExtra?: number | null
  fechaInicio: string
  horaInicio: string
  fechaRegreso?: string | null
  horaRegreso?: string | null
  numeroVuelo?: string | null
  llaves: number
  observacion?: string | null
}

export interface PreviewCobro {
  minutos: number
  dias: number
  monto: number
}

export interface CobroPayload {
  fechaRegreso: string
  horaRegreso: string
  metodo: 'debito' | 'credito' | 'transferencia'
  referencia?: string | null
}

export interface UsuarioPayload {
  idRol: number
  rut: string
  nombres: string
  apellidoPaterno: string
  apellidoMaterno?: string | null
  correo: string
  contrasena?: string
}

export const ROLES = {
  ADMINISTRADOR: 1,
  OPERADOR: 2,
  JEFATURA: 3,
} as const
