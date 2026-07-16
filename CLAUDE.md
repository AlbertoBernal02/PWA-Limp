# Proyecto — App de tareas para empresa de limpieza

## Contexto
App web para una empresa de limpieza. Cada operario, a primera hora,
consulta el listado de tareas que tiene asignadas para el día.
El objetivo es que sea accesible desde el móvil sin pasar por App Store.

## Decisiones tomadas
- **Stack**: Laravel (PHP) + MySQL/MariaDB.
- **Formato**: PWA (instalable en móvil, funciona en iPhone y Android
  sin store). Accesible desde la calle vía hosting con dominio + HTTPS,
  no por IP interna ni VPN.
- **Hosting**: compartido barato (3-8 €/mes) con soporte de cron.
- **Aviso diario a las 6:00**: cron con el Scheduler de Laravel.
  Empezar con push de PWA o email (gratis). WhatsApp (Business API)
  como opción futura, enchufable como canal de Notifications.

## Roles
- **Admin** (gestión): crea operarios, clientes y asigna tareas del día.
- **Operario**: login, ve su listado del día, marca tareas como hechas.

## Modelo de datos (borrador)
- operarios (usuarios con rol)
- clientes
- tareas / asignaciones diarias por operario

## Pendiente de decidir
- Dimensionar nº de operarios y clientes.
- Canal definitivo de notificación.

## Convenciones
- Respuestas y comentarios de código en español.
- No inventar rutas, métodos ni variables; respetar la estructura existente.
- Preferir soluciones robustas y mantenibles.