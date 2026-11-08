import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from 'react'
import {
  cerrarSesion,
  clearToken,
  getToken,
  login as apiLogin,
  obtenerPerfil,
  setToken,
} from '../lib/api'
import type { Usuario } from '../types'

interface AuthContextValue {
  usuario: Usuario | null
  cargando: boolean
  iniciarSesion: (correo: string, contrasena: string) => Promise<void>
  salir: () => Promise<void>
  actualizarUsuarioSesion: (usuario: Usuario) => void
}

const AuthContext = createContext<AuthContextValue | null>(null)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [usuario, setUsuario] = useState<Usuario | null>(null)
  const [cargando, setCargando] = useState(true)

  useEffect(() => {
    let vivo = true

    if (!getToken()) {
      setCargando(false)
      return
    }

    obtenerPerfil()
      .then((respuesta) => {
        if (vivo) setUsuario(respuesta.usuario)
      })
      .catch(() => {
        clearToken()
      })
      .finally(() => {
        if (vivo) setCargando(false)
      })

    return () => {
      vivo = false
    }
  }, [])

  const iniciarSesion = useCallback(async (correo: string, contrasena: string) => {
    const respuesta = await apiLogin(correo, contrasena)
    setToken(respuesta.token)
    setUsuario(respuesta.usuario)
  }, [])

  const salir = useCallback(async () => {
    try {
      await cerrarSesion()
    } catch {
      // Si el token ya expiró, igual cerramos la sesión local.
    }
    clearToken()
    setUsuario(null)
  }, [])

  const actualizarUsuarioSesion = useCallback((actualizado: Usuario) => {
    setUsuario(actualizado)
  }, [])

  const valor = useMemo(
    () => ({ usuario, cargando, iniciarSesion, salir, actualizarUsuarioSesion }),
    [usuario, cargando, iniciarSesion, salir, actualizarUsuarioSesion],
  )

  return <AuthContext.Provider value={valor}>{children}</AuthContext.Provider>
}

export function useAuth(): AuthContextValue {
  const contexto = useContext(AuthContext)
  if (!contexto) {
    throw new Error('useAuth debe usarse dentro de AuthProvider')
  }
  return contexto
}
