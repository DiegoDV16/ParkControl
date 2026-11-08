# Diseños de Stitch

Carpeta para dejar los diseños exportados desde Google Stitch y que el agente
pueda revisarlos.

## Formatos que el agente puede leer

| Formato | Cómo exportarlo desde Stitch |
|---|---|
| HTML + CSS | Pestaña **Code** del proyecto → copiar el código y pegarlo en un archivo `.html` aquí |
| Captura de pantalla | Botón de captura del navegador o exportar imagen → guardar `.png` o `.jpg` aquí |
| ZIP del proyecto | Descargar el proyecto y descomprimirlo dentro de esta carpeta |
| PDF | Imprimir/exportar la pantalla a PDF y guardarlo aquí |

## Recomendaciones

- Un archivo por pantalla, con nombre claro: `login.html`, `dashboard.png`,
  `ingresos-modal.png`.
- Si exportas código, incluye también el CSS o los estilos usados.
- Stitch suele generar Tailwind; el proyecto usa **Bootstrap 5**, así que el
  agente traducirá el diseño a los componentes y clases `pc-` existentes.
- Para capturas, usa la ventana completa y buena resolución (mínimo 1280 px de
  ancho) para apreciar espaciados y tipografía.

## Cómo avisar al agente

Después de dejar los archivos aquí, escribe en el chat la ruta, por ejemplo:

> Revisa `docs/stitch/dashboard.png` y `docs/stitch/login.html`

También puedes adjuntar las imágenes directamente en el chat.
