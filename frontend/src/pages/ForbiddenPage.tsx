import { Link } from 'react-router-dom'

export function ForbiddenPage() {
  return (
    <div className="flex min-h-screen items-center justify-center px-4">
      <div className="pc-card w-full max-w-md p-8 text-center">
        <p className="text-5xl font-black text-pc-mint">403</p>
        <h1 className="mt-3 text-lg font-semibold">Acceso denegado</h1>
        <p className="mt-2 text-sm text-pc-muted">
          Tu rol no tiene permisos para esta sección. Si crees que es un error, contacta al
          administrador.
        </p>
        <Link to="/" className="pc-btn-primary mt-6 w-full">
          Volver al inicio
        </Link>
      </div>
    </div>
  )
}
