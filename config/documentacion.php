<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Apartados de la documentación
    |--------------------------------------------------------------------------
    |
    | Slug => [titulo, roles]. "roles" indica qué operarios pueden ver ese
    | apartado (y su contenido se incluye en el contexto del chat de IA para
    | ellos). El admin siempre ve todos los apartados, sin filtrar.
    | El contenido real de cada apartado vive en resources/docs/{slug}.md
    |
    */

    'secciones' => [
        'picar' => ['titulo' => 'Cómo fichar (picar entrada/salida)', 'roles' => ['operario']],
        'empezar-tarea' => ['titulo' => 'Cómo empezar una tarea', 'roles' => ['operario']],
        'marcar-tarea-hecha' => ['titulo' => 'Cómo marcar una tarea como hecha (foto y material)', 'roles' => ['operario']],
        'optimizar-ruta' => ['titulo' => 'Cómo optimizar la ruta del día', 'roles' => ['operario']],
        'crear-tarea' => ['titulo' => 'Cómo crear una tarea', 'roles' => ['admin']],
        'mapa-almacen' => ['titulo' => 'Cómo usar el mapa de almacén', 'roles' => ['admin']],
    ],

];
