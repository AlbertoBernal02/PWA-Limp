# Nítido — Gestión de tareas para empresa de limpieza

Aplicación web (PWA) para que una empresa de limpieza gestione clientes, operarios y
las tareas diarias de limpieza. Cada operario consulta desde el móvil, sin pasar por
ninguna App Store, el listado de tareas que tiene asignadas ese día, las marca como
hechas con foto/nota, y ficha su jornada. El administrador gestiona todo desde el panel
web: clientes, operarios, tareas, fichajes e informes.

## Stack

- **Backend**: Laravel 13 + MySQL/MariaDB.
- **Frontend**: Blade + Tailwind CSS + Alpine.js + [Turbo](https://turbo.hotwired.dev/)
  (navegación tipo SPA sin recargar CSS/JS en cada clic).
- **PWA**: `manifest.json` + Service Worker propio (caché de estáticos, página offline,
  prompt de instalación en Android/desktop e instrucciones en iOS).
- **PDF**: [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) (gratis, sin API key).
- **Autocompletado de direcciones**: [Photon](https://photon.komoot.io/) (OpenStreetMap),
  gratis y sin cuenta ni API key — alternativa a Google Places.
- Sin dependencias de pago: se evaluaron Twilio (WhatsApp) y Google Places y se
  descartaron por requerir facturación; todo lo que hay en producción es gratuito.

## Roles

- **Admin**: gestiona clientes, operarios, tareas, fichajes e informes.
- **Operario**: consulta sus tareas del día, las marca como hechas, ficha su jornada
  y consulta su historial — sin acceso a nada de otros operarios.

## Funcionalidad

### Panel de administración

- **Dashboard**: tareas pendientes/hechas de hoy, próximos 7 días, resumen por operario,
  **duración media por tarea** (global), y un **aviso destacado de tareas atrasadas**
  (pendientes de un día anterior, o de hoy con más de 2h desde su hora prevista).
- **Clientes**: alta/edición/baja, foto del local, búsqueda por nombre/dirección/teléfono,
  **autocompletado de direcciones** al escribir (calle + número en la misma línea),
  ficha de cliente con historial completo de tareas, % de cumplimiento y **duración
  media** de sus limpiezas, e **informe mensual descargable en PDF o Excel (CSV)**
  para facturación.
- **Operarios**: alta/edición/baja, foto de perfil, teléfono, búsqueda,
  **activar/desactivar** (sin borrar su historial; un operario inactivo no puede
  iniciar sesión), y **vacaciones/ausencias** (mientras un periodo esté activo, las
  tareas recurrentes que caigan ahí se omiten automáticamente para ese operario).
- **Tareas**: alta/edición/baja, búsqueda por cliente/operario/descripción y filtros
  por fecha/operario/estado, **asignación múltiple** (misma tarea a varios operarios
  a la vez), **repetición semanal** (crea automáticamente una tarea igual cada semana
  hasta una fecha), **duplicar** una tarea suelta a otro día/cliente/operario sin
  pasar por el formulario de repetición, columna de **duración real** (fin − inicio)
  visible también en la ficha de cliente y en el informe mensual, y **duración media**
  del filtro activo (por cliente, operario, fecha o estado).
- **Fichajes** (registro horario, conforme al registro de jornada obligatorio en
  España): resumen de todos los operarios con su estado actual y horas de la semana;
  detalle semanal por operario con navegación semana a semana, **gráfico de horas por
  día**, corrección manual de fichajes (añadir/eliminar, para cuando alguien se
  olvida de fichar), e **informe mensual descargable en PDF o Excel (CSV)** para nómina.
- **Configuración → Herramientas** (icono de engranaje en la barra): CRUD del
  catálogo de herramientas/material, con **cantidad en almacén** por herramienta
  (editable a mano cuando se compra más o se gasta/rompe algo; se marca "Sin stock"
  en 0). Al crear/editar/duplicar una tarea se elige con checkboxes qué herramientas
  hay que llevar. Cuando el operario marca la tarea como hecha, el material que
  indica como gastado se **descuenta automáticamente del almacén**.
- **Configuración → Mapa del almacén**: representación visual tipo **estantería**,
  con las filas como "baldas" (etiquetadas A, B, C... a la izquierda) y las columnas
  como el hueco dentro de cada balda (Hueco 1, 2, 3... arriba). Se amplía con
  "+ Añadir fila" / "+ Añadir columna"; al hacer clic en un hueco (A1, B2...) se
  marca con checkboxes qué herramientas hay guardadas ahí (varias por hueco), y
  desde ahí mismo también se puede **eliminar ese hueco**. También se puede borrar
  una **fila o columna entera** (botones junto a la letra de fila / bajo el número
  de columna). En todos los casos las herramientas no se borran, solo quedan sin
  ubicación; si un hueco queda vacío a media cuadrícula, la celda se puede volver a
  crear con un clic sin tener que añadir toda una fila o columna nueva. La ubicación
  también se puede asignar desde el propio formulario de la herramienta, y se ve en
  el listado de Herramientas.
- **Menú contextual**: clic derecho (escritorio) o pulsación larga (móvil) en
  cualquier fila de las tablas de Clientes/Operarios/Tareas, con las mismas acciones
  que los enlaces de la fila.
- **Scroll horizontal** automático en las tablas cuando el contenido no cabe.
- **Modo oscuro**: interruptor en la barra de navegación, se recuerda entre sesiones.

### App del operario (PWA)

- **Mis tareas de hoy**: hora, cliente (con foto si tiene), enlace "Cómo llegar"
  (Google Maps), teléfono del cliente (`tel:` directo), descripción, **herramientas
  a llevar** (si el admin las asignó) y botón **Empezar** (fichaje por tarea, para
  medir el tiempo real de limpieza). Al **marcar como hecha** se abre un modal para
  indicar **cuánto material se ha gastado** de cada herramienta que llevaba (se
  descuenta del almacén al confirmar), con nota de texto opcional —también para
  apuntar material usado que no estaba en la lista— y foto de evidencia; una vez
  hecha se muestra la duración real (fin − inicio).
- **Historial**: tareas de días anteriores más las de hoy ya completadas.
- **Fichar**: entrada / inicio y fin de descanso (opcional) / salida, con las horas
  de hoy y de la semana, y el mismo **gráfico de horas por día**.
- **Funciona sin conexión**: si se marca una tarea como hecha sin cobertura, se
  guarda en el dispositivo (IndexedDB) y se envía sola en cuanto vuelve la conexión.
- **Instalable**: prompt de instalación nativo en Android/desktop; instrucciones
  manuales en iOS (Safari no expone la API de instalación).

## Instalación local

```bash
composer install
npm install
cp .env.example .env   # configurar DB_* con tu MySQL/MariaDB local
php artisan key:generate
php artisan migrate --seed
npm run build           # o `npm run dev` en desarrollo
php artisan serve
```

El seeder crea un admin (`admin@pwa-limp.test` / `password`) y 3 operarios con datos
de ejemplo (clientes, tareas). Los emails de los operarios se generan con Faker y
cambian cada vez que se resiembra — consulta `App\Models\User::where('role','operario')->get()`
si los necesitas.

## Estructura relevante

| Carpeta/archivo | Contenido |
|---|---|
| `app/Models/` | `User` (admin/operario), `Cliente`, `Tarea`, `Fichaje`, `Ausencia`, `Herramienta`, `Hueco` |
| `app/Support/Jornada.php` | Cálculo de horas trabajadas y máquina de estados del fichaje |
| `app/Http/Controllers/Admin/` | Controladores del panel de administración |
| `app/Http/Controllers/Operario/` | Controladores de la app del operario |
| `resources/views/admin/` | Vistas del panel |
| `resources/views/operario/` | Vistas de la app del operario |
| `resources/js/` | Turbo, menú contextual, autocompletado de direcciones, cola offline, prompt de instalación |
| `public/sw.js`, `public/manifest.json` | Service Worker y manifest de la PWA |

## Convenciones del proyecto

- Respuestas y comentarios de código en español.
- No inventar rutas, métodos ni variables; respetar la estructura existente.
- Preferir soluciones robustas y mantenibles sobre dependencias de pago.

## Pendiente / ideas futuras

- Notificación diaria por email al operario con sus tareas del día.
- Fotos "antes/después" en la evidencia de la tarea.
