# Sensilimp — Gestión de tareas para empresa de limpieza

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
  marca con checkboxes qué herramientas hay guardadas ahí, y desde ahí mismo también
  se puede **eliminar ese hueco**. También se puede borrar una **fila o columna
  entera**. En todos los casos las herramientas no se borran, solo quedan sin ese
  hueco; si queda vacío a media cuadrícula, la celda se puede volver a crear con un
  clic sin añadir toda una fila o columna nueva. **La misma herramienta —o el mismo
  trabajo— puede estar repartido en varios huecos a la vez** (ej. medio stock de
  detergente en A1 y el resto en B3, o "todo lo de Diógenes" marcado en varios
  huecos). El modal de cada hueco (funciona igual en **móvil y escritorio**) tiene
  dos checklists: **Trabajos** y **Herramientas** — así puedes marcar, por ejemplo,
  que el hueco A1 es el del "Síndrome de Diógenes" además de qué herramientas
  sueltas hay guardadas ahí. En **escritorio** hay además dos paneles a la derecha
  del mapa (Trabajos y Herramientas, en ese orden) con tarjetas arrastrables: soltar
  una tarjeta sobre un hueco lo marca al instante (arrastrar y soltar no existe en
  móvil, así que ahí solo está el modal). La ubicación también se puede asignar
  desde el propio formulario de la herramienta, y se ve en el listado de Herramientas.
- **Configuración → Trabajos**: tipos de trabajo (limpieza de oficinas, síndrome de
  Diógenes, placas solares...) para organizar el almacén por servicio. Cada trabajo
  tiene sus **herramientas típicas**; al crear/editar/duplicar una tarea y elegir su
  tipo de trabajo, esas herramientas se marcan solas en el checklist (el admin puede
  añadir o quitar libremente). El listado de Tareas se puede filtrar por trabajo, y
  el de Herramientas también. En el **Mapa del almacén** hay una fila de trabajos
  tocables (igual en móvil y escritorio): al tocar uno se abre qué herramientas
  suele llevar, con enlace directo para cambiarlas.
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
  hecha se muestra la duración real (fin − inicio). Al pulsar **Empezar** o
  **Confirmar**, si el operario da permiso de ubicación, se guarda su posición GPS
  de ese momento (nunca bloquea la acción: sin permiso, sin GPS o si tarda
  demasiado, se ficha igual sin ubicación). El admin ve enlaces "Inicio"/"Fin" a
  Google Maps en el listado de tareas — prueba de que el operario estuvo
  realmente en el sitio.
- **Optimizar ruta de hoy por cercanía**: con 3 o más tareas pendientes el mismo
  día aparece este botón; reordena la lista por cercanía real entre clientes
  (empezando por la primera tarea) en vez de por hora fija, y marca cada una con
  "Parada 1, 2, 3...". Las direcciones se geocodifican solas (Photon/OSM, gratis)
  la primera vez que hacen falta y se guardan en el cliente para no repetir la
  llamada. Si hay una clave de OpenRouteService configurada (`ORS_API_KEY`, ver
  `.env.example`) usa distancia real por carretera; si no, o si esa API falla,
  usa distancia en línea recta — nunca depende del todo de un servicio externo.
- **Historial**: tareas de días anteriores más las de hoy ya completadas.
- **Fichar**: entrada / inicio y fin de descanso (opcional) / salida, con las horas
  de hoy y de la semana, y el mismo **gráfico de horas por día**.
- **Funciona sin conexión**: si se marca una tarea como hecha sin cobertura, se
  guarda en el dispositivo (IndexedDB) y se envía sola en cuanto vuelve la conexión.
- **Instalable**: prompt de instalación nativo en Android/desktop; instrucciones
  manuales en iOS (Safari no expone la API de instalación).

### Aviso diario de tareas (6:00)

Cada día a las 6:00 (Scheduler de Laravel, comando `app:avisar-tareas-del-dia`) se
avisa a cada operario activo con tareas pendientes ese día, por dos canales:

- **Email** (siempre) — usa el `MAIL_MAILER` configurado; en local con `log` el
  correo se escribe en `storage/logs/laravel.log`.
- **Notificación push del navegador** (si el operario la activó) — icono de
  campana en la barra de navegación (admin y operario). Al pulsarlo pide permiso
  de notificaciones y suscribe el dispositivo; un segundo clic desactiva el aviso
  en ese dispositivo. Requiere HTTPS en producción (en local funciona por
  `localhost`) y, en iOS, tener la PWA instalada.

Una suscripción push corrupta o caducada no bloquea el aviso a los demás
operarios — se registra el error y se continúa (`AvisarTareasDelDia`).

WhatsApp Business API queda pendiente como tercer canal futuro; con el sistema de
`Notification` de Laravel ya montado, añadirlo es solo sumar un canal más en
`AvisoTareasDelDia::via()`, sin tocar el resto.

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

**Windows**: si al geocodificar una dirección o llamar a una API externa desde PHP
(`Http::get(...)`) sale `cURL error 60: SSL certificate ... unable to get local
issuer certificate`, es que PHP no tiene un certificado raíz configurado. Se
arregla una vez por instalación: descarga `https://curl.se/ca/cacert.pem` y en tu
`php.ini` pon `curl.cainfo` y `openssl.cafile` apuntando a esa ruta.

## Guía de despliegue a producción (primera vez)

Pensada para alguien que nunca ha subido una web con base de datos a un hosting.
Sigue el orden: cada paso da por hecho que el anterior ya está hecho.

### 0. Qué necesita el hosting

Antes de contratar nada, comprueba que el hosting ofrece:

- PHP 8.2 o superior, con las extensiones habituales (mbstring, pdo_mysql, gd, curl,
  zip) — casi todos los hostings las traen activadas por defecto.
- Base de datos MySQL o MariaDB.
- **Cron Jobs** desde el panel (imprescindible para el aviso diario de las 6:00).
- Certificado SSL/HTTPS gratis (Let's Encrypt). Sin HTTPS no funcionan las
  notificaciones push ni se instala bien la PWA.
- Idealmente **acceso SSH** (terminal), además del panel tipo cPanel — hace todo
  mucho más fácil. Si no lo tiene, se puede hacer todo desde el Administrador de
  Archivos y phpMyAdmin del panel; es más manual, pero cada paso de abajo indica
  también esa alternativa.

Esto es justo lo que cubre un hosting compartido barato (3-8 €/mes), que es el tipo
de hosting decidido para este proyecto.

### 1. Dominio y HTTPS

1. Compra el dominio (o usa uno que ya tengas) y apúntalo al hosting — normalmente
   cambiando los "nameservers" desde donde compraste el dominio a los que te da el
   hosting al contratarlo.
2. Cuando el dominio ya resuelva al hosting, activa el SSL gratuito desde el panel
   (suele llamarse "SSL/TLS Status" o "Let's Encrypt" en cPanel). Puede tardar
   desde minutos hasta unas horas en propagarse.

### 2. Subir el código

**Con SSH** (recomendado si lo tienes):

```bash
ssh usuario@tuservidor
cd ruta/al/dominio
git clone <url-de-tu-repositorio> .
```

**Sin SSH** (Administrador de Archivos del panel):

1. En tu ordenador, comprime la carpeta del proyecto en un `.zip` (sin las carpetas
   `node_modules` ni `vendor`, no hacen falta y pesan mucho).
2. Sube ese `.zip` por el Administrador de Archivos y descomprímelo en la carpeta
   de tu dominio.

### 3. Crear la base de datos

Desde la sección "Bases de datos MySQL" (o similar) del panel del hosting:

1. Crea una base de datos (por ejemplo `usuario_sensilimp`).
2. Crea un usuario de base de datos con una contraseña fuerte.
3. Asocia ese usuario a la base de datos dándole **todos los permisos**.
4. Anota los tres datos — nombre de la base de datos, usuario y contraseña — los
   necesitas en el paso siguiente.

### 4. Configurar el `.env` de producción

Copia `.env.example` a `.env` en el servidor y completa al menos esto:

```env
APP_NAME=Sensilimp
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=usuario_sensilimp
DB_USERNAME=el_usuario_que_creaste
DB_PASSWORD=la_contraseña_que_creaste
```

- **`APP_DEBUG=false` es el punto más importante de toda esta guía.** Con `true`,
  cualquier error de la web le muestra a quien lo provoque rutas del servidor,
  variables de entorno (incluida la contraseña de la base de datos) y las
  consultas SQL. Compruébalo dos veces antes de abrir la web al público.
- La cookie de sesión se marca `secure` sola al poner `APP_ENV=production` (ver
  `config/session.php`); no hace falta tocar nada más ahí.
- `TRUSTED_PROXIES` solo si el hosting pone un proxy/CDN (Cloudflare, balanceador...)
  delante de la app; si no, déjalo vacío.
- Las claves `VAPID_*` se generan en el paso 9, más abajo.

### 5. Instalar dependencias y compilar

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate          # genera APP_KEY — solo la primera vez, no la pierdas
npm install && npm run build      # solo si el hosting tiene Node.js instalado
```

Muchos hostings compartidos **no tienen Node.js**. Si es tu caso, ejecuta
`npm run build` en tu ordenador local y sube después la carpeta `public/build/`
ya generada por FTP/Administrador de Archivos.

### 6. Migrar la base de datos

```bash
php artisan migrate --force
```

No uses `--seed` en producción — ese seeder crea datos de ejemplo con Faker, no
los quieres en la web real. Crea el primer administrador a mano:

```bash
php artisan tinker
>>> App\Models\User::create(['name' => 'Admin', 'email' => 'tu@correo.com', 'password' => bcrypt('una-contraseña-fuerte'), 'role' => 'admin']);
```

### 7. Apuntar el dominio a `public/`

Laravel espera servirse desde la carpeta `public/` del proyecto, no desde su raíz.

- Si el panel te deja elegir el "Document Root" del dominio, apúntalo directamente
  a `.../tu-proyecto/public`. Es la opción más simple y la recomendada.
- Si no puedes cambiarlo (dominio principal fijo a `public_html`), sube el proyecto
  **fuera** de `public_html` y copia dentro solo el contenido de `public/`, ajustando
  las rutas de `require` en su `index.php` para que sigan encontrando
  `vendor/autoload.php` y `bootstrap/app.php` en la carpeta del proyecto real. Es
  más delicado, así que evita esta opción si el hosting te permite la anterior.

### 8. Cron (imprescindible para el aviso de las 6:00)

En la sección "Cron Jobs" del panel, añade una tarea que se ejecute **cada minuto**:

```
* * * * * php /ruta/completa/al/proyecto/artisan schedule:run >> /dev/null 2>&1
```

Sin esto, el Scheduler de Laravel nunca dispara el aviso diario de las 6:00 ni
ninguna otra tarea programada futura.

### 9. Notificaciones push (VAPID)

Genera un juego de claves **propio para producción** (no reutilices el de
desarrollo):

```bash
php artisan webpush:vapid
```

Copia las claves que te da al `.env` de producción: `VAPID_SUBJECT`,
`VAPID_PUBLIC_KEY`, `VAPID_PRIVATE_KEY`.

### 10. Cachear para rendimiento (recomendable)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Si después cambias algo en `.env` o en las rutas, recuerda limpiar la caché
correspondiente (`php artisan config:clear`, etc.) o el cambio no se notará.

### 11. Comprobación final

- Entra en `https://tudominio.com` y haz login con el admin que creaste.
- Comprueba que aparece el candado HTTPS — sin él, ni el push ni la instalación
  de la PWA funcionan bien.
- Crea una tarea de prueba, márcala como hecha, comprueba que aparece en el
  historial y que el almacén descuenta el material indicado.
- Si algo falla, `storage/logs/laravel.log` es el primer sitio donde mirar.
- Instala la PWA en tu móvil (Android/desktop: banner automático; iOS: botón
  Compartir → "Añadir a pantalla de inicio") y comprueba que carga con el icono
  y el nombre correctos.

### Actualizar la app en el futuro

Cada vez que subas cambios de código a producción:

```bash
git pull                              # o volver a subir el .zip actualizado
composer install --no-dev --optimize-autoloader
npm run build                         # solo si tocaste CSS/JS
php artisan migrate --force           # solo si hay migraciones nuevas
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

### Copias de seguridad

Antes de cualquier despliegue importante, haz una copia de la base de datos.
Desde phpMyAdmin: "Exportar" → formato SQL. Por SSH:

```bash
mysqldump -u usuario -p nombre_base_datos > backup-$(date +%Y%m%d).sql
```

Muchos hostings compartidos ofrecen copias automáticas diarias/semanales desde el
panel — si está disponible, actívala: es la red de seguridad más sencilla de todas.

## Estructura relevante

| Carpeta/archivo | Contenido |
|---|---|
| `app/Models/` | `User` (admin/operario), `Cliente`, `Tarea`, `Fichaje`, `Ausencia`, `Herramienta`, `Hueco`, `Trabajo` |
| `app/Support/Jornada.php` | Cálculo de horas trabajadas y máquina de estados del fichaje |
| `app/Http/Controllers/Admin/` | Controladores del panel de administración |
| `app/Http/Controllers/Operario/` | Controladores de la app del operario |
| `resources/views/admin/` | Vistas del panel |
| `resources/views/operario/` | Vistas de la app del operario |
| `resources/js/` | Turbo, menú contextual, autocompletado de direcciones, cola offline, prompt de instalación, notificaciones push |
| `public/sw.js`, `public/manifest.json` | Service Worker y manifest de la PWA |
| `app/Console/Commands/AvisarTareasDelDia.php` | Aviso diario (email/push) de tareas pendientes; programado en `routes/console.php` |
| `app/Notifications/AvisoTareasDelDia.php` | Contenido del aviso por canal (`mail`, `WebPushChannel`) |
| `app/Services/Geocodificador.php` | Dirección de texto → lat/lng (Photon/OSM); cachea el resultado en el cliente |
| `app/Services/OptimizadorRuta.php` | Orden de tareas por cercanía (OpenRouteService o línea recta) para "Optimizar ruta" |
| `resources/js/almacen-drag.js` | Arrastrar y soltar (solo escritorio) de herramientas a huecos en el mapa del almacén |
| `resources/js/trabajo-sugerencias.js` | Sugiere herramientas típicas al elegir el tipo de trabajo de una tarea |

## Convenciones del proyecto

- Respuestas y comentarios de código en español.
- No inventar rutas, métodos ni variables; respetar la estructura existente.
- Preferir soluciones robustas y mantenibles sobre dependencias de pago.

## Pendiente / ideas futuras

- WhatsApp Business API como canal adicional del aviso diario (ver sección de
  arriba — solo hay que añadir un canal a `AvisoTareasDelDia::via()`).
- Fotos "antes/después" en la evidencia de la tarea.
