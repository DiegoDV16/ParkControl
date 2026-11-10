import type {
  Catalogos,
  CobroPayload,
  Danio,
  Fila,
  FotoInspeccion,
  IngresoPayload,
  IngresoResumen,
  LoginResponse,
  Paginated,
  PerfilPayload,
  PerfilResponse,
  PreviewCobro,
  Rol,
  Usuario,
  UsuarioPayload,
} from '../types'

const API_URL = (import.meta.env.VITE_API_URL as string | undefined) ?? 'http://127.0.0.1:8000/api'
const TOKEN_KEY = 'pc_token'

export class ApiError extends Error {
  status: number
  errors?: Record<string, string[]>

  constructor(status: number, message: string, errors?: Record<string, string[]>) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.errors = errors
  }
}

export function getToken(): string | null {
  return localStorage.getItem(TOKEN_KEY)
}

export function setToken(token: string): void {
  localStorage.setItem(TOKEN_KEY, token)
}

export function clearToken(): void {
  localStorage.removeItem(TOKEN_KEY)
}

async function request<T>(path: string, options: RequestInit = {}, withAuth = true): Promise<T> {
  const headers = new Headers(options.headers)
  headers.set('Accept', 'application/json')

  if (options.body && !headers.has('Content-Type')) {
    headers.set('Content-Type', 'application/json')
  }

  const token = getToken()
  if (withAuth && token) {
    headers.set('Authorization', `Bearer ${token}`)
  }

  let response: Response
  try {
    response = await fetch(`${API_URL}${path}`, { ...options, headers })
  } catch {
    throw new ApiError(0, 'No se pudo conectar con el servidor.')
  }

  const texto = await response.text()
  let datos: unknown = null
  if (texto) {
    try {
      datos = JSON.parse(texto)
    } catch {
      datos = null
    }
  }

  if (!response.ok) {
    const cuerpo = (datos ?? {}) as { message?: string; errors?: Record<string, string[]> }
    throw new ApiError(response.status, cuerpo.message ?? 'Ocurrió un error inesperado.', cuerpo.errors)
  }

  return datos as T
}

export function login(correo: string, contrasena: string): Promise<LoginResponse> {
  return request<LoginResponse>(
    '/login',
    { method: 'POST', body: JSON.stringify({ correo, contrasena }) },
    false,
  )
}

export function obtenerPerfil(): Promise<PerfilResponse> {
  return request<PerfilResponse>('/user')
}

export function cerrarSesion(): Promise<{ message: string }> {
  return request<{ message: string }>('/logout', { method: 'POST' })
}

function queryString(params: Record<string, string | number | undefined>): string {
  const partes = Object.entries(params)
    .filter(([, valor]) => valor !== undefined && valor !== '')
    .map(([clave, valor]) => `${encodeURIComponent(clave)}=${encodeURIComponent(String(valor))}`)

  return partes.length ? `?${partes.join('&')}` : ''
}

export function listarRoles(): Promise<{ data: Rol[] }> {
  return request<{ data: Rol[] }>('/roles')
}

export function listarUsuarios(params: {
  buscar?: string
  idRol?: number | ''
  estado?: number | ''
  page?: number
}): Promise<Paginated<Usuario>> {
  return request<Paginated<Usuario>>(`/usuarios${queryString(params)}`)
}

export function crearUsuario(datos: UsuarioPayload): Promise<{ data: Usuario }> {
  return request<{ data: Usuario }>('/usuarios', {
    method: 'POST',
    body: JSON.stringify(datos),
  })
}

export function actualizarUsuario(id: number, datos: UsuarioPayload): Promise<{ data: Usuario }> {
  return request<{ data: Usuario }>(`/usuarios/${id}`, {
    method: 'PUT',
    body: JSON.stringify(datos),
  })
}

export function cambiarEstadoUsuario(id: number, estado: number): Promise<{ data: Usuario }> {
  return request<{ data: Usuario }>(`/usuarios/${id}/estado`, {
    method: 'PATCH',
    body: JSON.stringify({ estado }),
  })
}

export function actualizarPerfil(datos: PerfilPayload): Promise<{ usuario: Usuario; message: string }> {
  return request<{ usuario: Usuario; message: string }>('/perfil', {
    method: 'PUT',
    body: JSON.stringify(datos),
  })
}

export function cambiarContrasenaPerfil(datos: {
  contrasena_actual: string
  contrasena: string
  contrasena_confirmation: string
}): Promise<{ message: string }> {
  return request<{ message: string }>('/perfil/password', {
    method: 'PUT',
    body: JSON.stringify(datos),
  })
}

export function listarFilas(): Promise<{ data: Fila[] }> {
  return request<{ data: Fila[] }>('/filas')
}

export function actualizarFila(
  id: number,
  capacidadTotal: number,
): Promise<{ data: Fila; message: string }> {
  return request<{ data: Fila; message: string }>(`/filas/${id}`, {
    method: 'PUT',
    body: JSON.stringify({ capacidadTotal }),
  })
}

export function obtenerCatalogos(): Promise<Catalogos> {
  return request<Catalogos>('/catalogos')
}

export function listarIngresos(): Promise<Paginated<IngresoResumen>> {
  return request<Paginated<IngresoResumen>>('/ingresos')
}

export function estimarIngreso(params: {
  tipo?: string
  servicio?: string
  fechaInicio: string
  horaInicio: string
  fechaRegreso?: string
  horaRegreso?: string
}): Promise<{ minutos: number; dias: number; monto: number }> {
  return request<{ minutos: number; dias: number; monto: number }>(
    `/ingresos/estimar${queryString(params)}`,
  )
}

export function crearIngreso(datos: IngresoPayload): Promise<{ data: IngresoResumen }> {
  return request<{ data: IngresoResumen }>('/ingresos', {
    method: 'POST',
    body: JSON.stringify(datos),
  })
}

export function previsualizarCobro(
  id: number,
  fechaRegreso: string,
  horaRegreso: string,
): Promise<PreviewCobro> {
  return request<PreviewCobro>(
    `/ingresos/${id}/preview${queryString({ fechaRegreso, horaRegreso })}`,
  )
}

export function cobrarIngreso(
  id: number,
  datos: CobroPayload,
): Promise<{ message: string; data: IngresoResumen; pago: { monto: number; vuelto: number | null; metodo: string } }> {
  return request(`/ingresos/${id}/cobrar`, {
    method: 'POST',
    body: JSON.stringify(datos),
  })
}

export function listarDanios(idIngreso: number): Promise<{ data: Danio[] }> {
  return request<{ data: Danio[] }>(`/ingresos/${idIngreso}/danios`)
}

export function crearDanio(
  idIngreso: number,
  datos: { vista: string; coordX: number; coordY: number; tipoDanio: string; detalle?: string | null },
): Promise<{ data: Danio }> {
  return request<{ data: Danio }>(`/ingresos/${idIngreso}/danios`, {
    method: 'POST',
    body: JSON.stringify(datos),
  })
}

export function actualizarDanio(
  idDanio: number,
  datos: Partial<{ vista: string; coordX: number; coordY: number; tipoDanio: string; detalle: string | null }>,
): Promise<{ data: Danio }> {
  return request<{ data: Danio }>(`/danios/${idDanio}`, {
    method: 'PUT',
    body: JSON.stringify(datos),
  })
}

export function eliminarDanio(idDanio: number): Promise<{ message: string }> {
  return request<{ message: string }>(`/danios/${idDanio}`, { method: 'DELETE' })
}

export async function subirInspeccion(
  idIngreso: number,
  imagen: Blob,
  sinDanios: boolean,
): Promise<{ message: string; url: string; ruta: string }> {
  const formulario = new FormData()
  formulario.append('imagen', imagen, `inspeccion-${idIngreso}.webp`)
  formulario.append('sinDanios', sinDanios ? '1' : '0')

  const cabeceras: Record<string, string> = { Accept: 'application/json' }
  const token = getToken()
  if (token) cabeceras.Authorization = `Bearer ${token}`

  let respuesta: Response
  try {
    respuesta = await fetch(`${API_URL}/ingresos/${idIngreso}/inspeccion`, {
      method: 'POST',
      headers: cabeceras,
      body: formulario,
    })
  } catch {
    throw new ApiError(0, 'No se pudo conectar con el servidor.')
  }

  const texto = await respuesta.text()
  let datos: { message?: string; url?: string; ruta?: string } = {}
  if (texto) {
    try {
      datos = JSON.parse(texto) as typeof datos
    } catch {
      datos = {}
    }
  }

  if (!respuesta.ok) {
    throw new ApiError(respuesta.status, datos.message ?? 'No se pudo guardar la inspección.')
  }

  return {
    message: datos.message ?? 'Inspección guardada.',
    url: datos.url ?? '',
    ruta: datos.ruta ?? '',
  }
}

export async function subirFotoInspeccion(
  idIngreso: number,
  imagen: File,
): Promise<{ data: FotoInspeccion }> {
  const formulario = new FormData()
  formulario.append('imagen', imagen, imagen.name || 'foto.jpg')

  const cabeceras: Record<string, string> = { Accept: 'application/json' }
  const token = getToken()
  if (token) cabeceras.Authorization = `Bearer ${token}`

  let respuesta: Response
  try {
    respuesta = await fetch(`${API_URL}/ingresos/${idIngreso}/fotos`, {
      method: 'POST',
      headers: cabeceras,
      body: formulario,
    })
  } catch {
    throw new ApiError(0, 'No se pudo conectar con el servidor.')
  }

  const texto = await respuesta.text()
  let datos: { message?: string; data?: FotoInspeccion } = {}
  if (texto) {
    try {
      datos = JSON.parse(texto) as typeof datos
    } catch {
      datos = {}
    }
  }

  if (!respuesta.ok || !datos.data) {
    throw new ApiError(respuesta.status, datos.message ?? 'No se pudo subir la fotografía.')
  }

  return { data: datos.data }
}

export function eliminarFotoInspeccion(idFoto: number): Promise<{ message: string }> {
  return request<{ message: string }>(`/inspeccion-fotos/${idFoto}`, { method: 'DELETE' })
}

export function eliminarUsuario(id: number, forzar = false): Promise<{ message: string }> {
  return request<{ message: string }>(`/usuarios/${id}${forzar ? '?force=1' : ''}`, {
    method: 'DELETE',
  })
}
