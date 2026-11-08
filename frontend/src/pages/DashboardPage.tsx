import { useAuth } from '../auth/AuthContext'

function Dato({ etiqueta, valor }: { etiqueta: string; valor: string }) {
  return (
    <div className="flex items-center justify-between gap-4 border-b border-pc-border/60 py-2 last:border-0">
      <span className="text-xs uppercase tracking-wide text-pc-muted">{etiqueta}</span>
      <span className="text-right text-sm font-medium">{valor}</span>
    </div>
  )
}

export function DashboardPage() {
  const { usuario } = useAuth()

  if (!usuario) return null

  const nombreCompleto = [usuario.nombres, usuario.apellidoPaterno, usuario.apellidoMaterno]
    .filter(Boolean)
    .join(' ')

  return (
    <div className="space-y-6">
      <section className="pc-card p-6">
        <div className="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p className="text-[11px] uppercase tracking-[0.2em] text-pc-muted">Bienvenido</p>
            <h1 className="mt-1 text-2xl font-bold">{nombreCompleto}</h1>
            <p className="mt-1 text-sm text-pc-muted">
              Sesión iniciada con el rol <span className="font-semibold text-pc-mint">{usuario.rol}</span>.
            </p>
          </div>
          <span className="inline-flex items-center gap-2 self-start rounded-full border border-pc-mint/40 bg-pc-mint/10 px-3 py-1 text-xs font-semibold text-pc-mint">
            <span className="h-2 w-2 rounded-full bg-pc-mint" /> Token Sanctum activo
          </span>
        </div>
      </section>

      <div className="grid gap-4 lg:grid-cols-3">
        <section className="pc-card p-5">
          <h2 className="mb-3 text-sm font-semibold">Tu cuenta</h2>
          <Dato etiqueta="RUT" valor={usuario.rut} />
          <Dato etiqueta="Correo" valor={usuario.correo} />
          <Dato etiqueta="Rol" valor={`${usuario.rol} (#${usuario.idRol})`} />
          <Dato etiqueta="Estado" valor={usuario.estado === 1 ? 'Activo' : 'Inactivo'} />
        </section>

        <section className="pc-card p-5">
          <h2 className="mb-3 text-sm font-semibold">Conexión</h2>
          <Dato etiqueta="API" valor="Laravel 12 · /api" />
          <Dato etiqueta="Autenticación" valor="Sanctum (Bearer)" />
          <Dato etiqueta="Base de datos" valor="MySQL · parkcontrol" />
          <Dato etiqueta="Sesión" valor="Token persistido" />
        </section>

        <section className="pc-card p-5">
          <h2 className="mb-3 text-sm font-semibold">Fase 1 completada</h2>
          <ul className="space-y-2 text-sm text-pc-muted">
            {[
              'Login y logout con tokens',
              'Rutas protegidas por rol',
              'Layout responsivo (oficina y tablet)',
              'Catálogo de roles del sistema',
            ].map((item) => (
              <li key={item} className="flex items-start gap-2">
                <span className="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-pc-mint" />
                {item}
              </li>
            ))}
          </ul>
        </section>
      </div>

      <section className="pc-card p-6">
        <h2 className="text-sm font-semibold">Siguiente fase</h2>
        <p className="mt-1 text-sm text-pc-muted">
          Recepción en patio con tablet: datos del cliente y vehículo, asignación de fila con
          ocupación en tiempo real, inspección de daños en canvas y cálculo de tarifa estimada.
        </p>
      </section>
    </div>
  )
}
