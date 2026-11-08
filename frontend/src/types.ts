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
