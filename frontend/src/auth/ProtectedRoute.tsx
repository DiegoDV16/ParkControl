import { Navigate, Outlet } from 'react-router-dom'
import { useAuth } from './AuthContext'

interface Props {
  roles?: number[]
}

export function ProtectedRoute({ roles }: Props) {
  const { usuario, cargando } = useAuth()

  if (cargando) {
    return (
      <div className="flex min-h-screen items-center justify-center">
        <div className="h-10 w-10 animate-spin rounded-full border-2 border-pc-border border-t-pc-mint" />
      </div>
    )
  }

  if (!usuario) {
    return <Navigate to="/login" replace />
  }

  if (roles && !roles.includes(usuario.idRol)) {
    return <Navigate to="/403" replace />
  }

  return <Outlet />
}
