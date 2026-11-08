import {
  CarFrontBlueprint,
  CarRearBlueprint,
  CarSideBlueprint,
  CarTopBlueprint,
} from '../CarInspectionBlueprints'
import type { VistaInspeccion } from './tipos'

export function VistaVehiculo({ vista }: { vista: VistaInspeccion }) {
  if (vista === 'frontal') return <CarFrontBlueprint />
  if (vista === 'trasera') return <CarRearBlueprint />
  if (vista === 'lateral_izquierda') return <CarSideBlueprint />
  if (vista === 'lateral_derecha') return <CarSideBlueprint flip />
  return <CarTopBlueprint />
}
