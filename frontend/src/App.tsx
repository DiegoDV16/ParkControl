import { Navigate, Route, Routes } from 'react-router-dom'
import { ProtectedRoute } from './auth/ProtectedRoute'
import { AppLayout } from './layouts/AppLayout'
import { DashboardPage } from './pages/DashboardPage'
import { FilasPage } from './pages/FilasPage'
import { ForbiddenPage } from './pages/ForbiddenPage'
import { InspeccionPage } from './pages/InspeccionPage'
import { JefaturaPage } from './pages/JefaturaPage'
import { LoginPage } from './pages/LoginPage'
import { PerfilPage } from './pages/PerfilPage'
import { RecepcionPage } from './pages/RecepcionPage'
import { UsuariosPage } from './pages/UsuariosPage'

export default function App() {
  return (
    <Routes>
      <Route path="/login" element={<LoginPage />} />

      <Route element={<ProtectedRoute />}>
        <Route element={<AppLayout />}>
          <Route index element={<DashboardPage />} />
          <Route path="perfil" element={<PerfilPage />} />
          <Route path="filas" element={<FilasPage />} />

          <Route element={<ProtectedRoute roles={[1, 2]} />}>
            <Route path="recepcion" element={<RecepcionPage />} />
            <Route path="inspeccion" element={<InspeccionPage />} />
          </Route>

          <Route element={<ProtectedRoute roles={[1]} />}>
            <Route path="usuarios" element={<UsuariosPage />} />
          </Route>

          <Route element={<ProtectedRoute roles={[1, 3]} />}>
            <Route path="jefatura" element={<JefaturaPage />} />
          </Route>
        </Route>
      </Route>

      <Route path="/403" element={<ForbiddenPage />} />
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  )
}
