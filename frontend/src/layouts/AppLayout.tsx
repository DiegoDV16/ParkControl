import { useState } from 'react'
import { NavLink, Outlet, useNavigate } from 'react-router-dom'
import { useAuth } from '../auth/AuthContext'

const iconos: Record<string, string> = {
  inicio: 'M3 10.5 12 3l9 7.5V21h-6v-6H9v6H3z',
  usuarios: 'M16 21v-2a4 4 0 0 0-8 0v2M12 3a4 4 0 1 1 0 8 4 4 0 0 1 0-8',
  perfil: 'M20 21a8 8 0 1 0-16 0M12 3a4 4 0 1 1 0 8 4 4 0 0 1 0-8',
  recepcion: 'M3 13h18M5 13V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v6M7 13v7m10-7v7',
  filas: 'M4 5h16M4 12h16M4 19h16',
  clientes: 'M16 21v-2a4 4 0 0 0-8 0v2M12 3a4 4 0 1 1 0 8 4 4 0 0 1 0-8',
  reportes: 'M4 19V5m0 14h16M8 15v-4m4 4V9m4 6v-7',
  config: 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.4-3a7.4 7.4 0 0 0-.1-1l2-1.6-2-3.4-2.4 1a7 7 0 0 0-1.7-1L14.8 3h-4l-.4 2.6a7 7 0 0 0-1.7 1l-2.4-1-2 3.4 2 1.6a7.4 7.4 0 0 0 0 2l-2 1.6 2 3.4 2.4-1a7 7 0 0 0 1.7 1l.4 2.6h4l.4-2.6a7 7 0 0 0 1.7-1l2.4 1 2-3.4-2-1.6c.1-.3.1-.7.1-1Z',
}

const menu = [
  { clave: 'inicio', texto: 'Inicio', ruta: '/', disponible: true },
  { clave: 'usuarios', texto: 'Usuarios', ruta: '/usuarios', disponible: true },
  { clave: 'recepcion', texto: 'Recepción en patio', ruta: '/recepcion', disponible: false },
  { clave: 'filas', texto: 'Filas y ocupación', ruta: '/filas', disponible: false },
  { clave: 'clientes', texto: 'Clientes', ruta: '/clientes', disponible: false },
  { clave: 'reportes', texto: 'Supervisión', ruta: '/reportes', disponible: false },
  { clave: 'perfil', texto: 'Mi perfil', ruta: '/perfil', disponible: true },
  { clave: 'config', texto: 'Configuración', ruta: '/configuracion', disponible: false },
]

function Icono({ nombre }: { nombre: string }) {
  return (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" className="h-5 w-5">
      <path d={iconos[nombre] ?? iconos.inicio} strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  )
}

export function AppLayout() {
  const { usuario, salir } = useAuth()
  const navegar = useNavigate()
  const [menuAbierto, setMenuAbierto] = useState(false)

  const nombre = usuario ? `${usuario.nombres} ${usuario.apellidoPaterno}` : ''
  const rol = usuario?.rol ?? ''

  async function cerrarSesion() {
    await salir()
    navegar('/login', { replace: true })
  }

  const navegacion = (
    <nav className="mt-6 flex flex-col gap-1">
      {menu.filter((item) => item.clave !== 'usuarios' || usuario?.idRol === 1).map((item) =>
        item.disponible ? (
          <NavLink
            key={item.clave}
            to={item.ruta}
            onClick={() => setMenuAbierto(false)}
            className={({ isActive }) =>
              `flex min-h-[44px] items-center gap-3 rounded-lg px-3 text-sm font-medium transition ${
                isActive
                  ? 'bg-pc-mint/15 text-pc-mint'
                  : 'text-pc-muted hover:bg-white/5 hover:text-pc-text'
              }`
            }
          >
            <Icono nombre={item.clave} />
            {item.texto}
          </NavLink>
        ) : (
          <span
            key={item.clave}
            className="flex min-h-[44px] cursor-not-allowed items-center gap-3 rounded-lg px-3 text-sm text-pc-muted/50"
            title="Disponible en una fase posterior"
          >
            <Icono nombre={item.clave} />
            {item.texto}
            <span className="ml-auto rounded bg-white/5 px-2 py-0.5 text-[10px] uppercase tracking-wide">
              Pronto
            </span>
          </span>
        ),
      )}
    </nav>
  )

  return (
    <div className="min-h-screen lg:flex">
      {/* Sidebar escritorio */}
      <aside className="hidden w-64 shrink-0 border-r border-pc-border bg-pc-panel/60 p-5 lg:flex lg:flex-col">
        <div className="flex items-center gap-3">
          <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-pc-mint text-lg font-black text-[#04121d]">
            P
          </span>
          <div>
            <p className="text-sm font-bold">ParkControl</p>
            <p className="text-[10px] uppercase tracking-[0.18em] text-pc-muted">Larga estadía</p>
          </div>
        </div>

        {navegacion}

        <div className="mt-auto border-t border-pc-border pt-4">
          <p className="text-xs font-semibold text-pc-text">{nombre}</p>
          <p className="text-[11px] text-pc-muted">{rol}</p>
        </div>
      </aside>

      {/* Drawer móvil/tablet */}
      {menuAbierto && (
        <div className="fixed inset-0 z-40 lg:hidden">
          <button
            type="button"
            aria-label="Cerrar menú"
            className="absolute inset-0 bg-black/60"
            onClick={() => setMenuAbierto(false)}
          />
          <aside className="relative z-50 h-full w-72 border-r border-pc-border bg-pc-panel p-5">
            <div className="flex items-center justify-between">
              <span className="text-sm font-bold">ParkControl</span>
              <button
                type="button"
                onClick={() => setMenuAbierto(false)}
                className="min-h-[44px] min-w-[44px] rounded-lg border border-pc-border text-pc-muted"
                aria-label="Cerrar"
              >
                ✕
              </button>
            </div>
            {navegacion}
          </aside>
        </div>
      )}

      <div className="flex min-w-0 flex-1 flex-col">
        <header className="sticky top-0 z-30 flex items-center gap-3 border-b border-pc-border bg-pc-bg/85 px-4 py-3 backdrop-blur lg:px-8">
          <button
            type="button"
            className="pc-btn-ghost min-h-[44px] px-3 lg:hidden"
            onClick={() => setMenuAbierto(true)}
            aria-label="Abrir menú"
          >
            ☰
          </button>

          <div className="min-w-0">
            <p className="truncate text-sm font-semibold">Panel de control</p>
            <p className="text-[11px] uppercase tracking-[0.16em] text-pc-muted">Fase 1 · base y acceso</p>
          </div>

          <div className="ml-auto flex items-center gap-3">
            <span className="hidden rounded-full border border-pc-mint/40 bg-pc-mint/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-pc-mint sm:inline">
              {rol}
            </span>
            <span className="hidden text-right text-xs sm:block">
              <span className="block font-semibold">{nombre}</span>
              <span className="block text-pc-muted">{usuario?.correo}</span>
            </span>
            <button type="button" className="pc-btn-ghost px-3" onClick={cerrarSesion}>
              Salir
            </button>
          </div>
        </header>

        <main className="flex-1 px-4 py-6 lg:px-8">
          <Outlet />
        </main>
      </div>
    </div>
  )
}
