export interface Usuario {
  idUsuario: number
  rut: string
  nombres: string
  apellidoPaterno: string
  apellidoMaterno: string | null
  correo: string
  idRol: number
  rol: string | null
  estado: number
}

export interface LoginResponse {
  token: string
  token_type: string
  usuario: Usuario
}

export interface PerfilResponse {
  usuario: Usuario
}

export const ROLES = {
  ADMINISTRADOR: 1,
  OPERADOR: 2,
  JEFATURA: 3,
} as const
