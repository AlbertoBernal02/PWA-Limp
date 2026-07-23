<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Herramienta;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Catálogo de "tools" (function calling) que el chat de IA puede invocar, y su
 * ejecución real contra Eloquent. El modelo de IA NUNCA decide qué puede leer:
 * las tools "solo admin" ni siquiera se anuncian a un operario, y ejecutar()
 * vuelve a comprobar el rol antes de despachar, por si el modelo alucinara una
 * llamada que no se le anunció. Las consultas que podrían filtrar datos de otro
 * operario siempre fuerzan operario_id = $usuario->id en PHP, ignorando
 * cualquier identificador que el modelo intente pasar como argumento.
 */
class HerramientasIaDispatcher
{
    private const SOLO_ADMIN = ['tareas_de_operario', 'fichajes_de_operario', 'listar_operarios_activos'];

    /** @return array<int, array<string, mixed>> declaraciones visibles para este usuario */
    public function declaraciones(User $usuario): array
    {
        $comunes = [
            $this->declarar('listar_inventario_herramientas', 'Lista todas las herramientas del almacén con su cantidad en stock.'),
            $this->declarar('consultar_stock_herramienta', 'Consulta el stock de una herramienta concreta por nombre (o parte del nombre).', [
                'nombre' => ['type' => 'STRING', 'description' => 'Nombre o parte del nombre de la herramienta.'],
            ], ['nombre']),
            $this->declarar('contar_tareas_pendientes', 'Cuenta tareas pendientes para una fecha. Si el usuario es operario, cuenta solo las suyas.', [
                'fecha' => ['type' => 'STRING', 'description' => 'Fecha en formato YYYY-MM-DD. Si se omite, se usa hoy.'],
            ]),
            $this->declarar('mis_tareas', 'Lista las tareas del propio usuario que pregunta, para una fecha dada.', [
                'fecha' => ['type' => 'STRING', 'description' => 'Fecha en formato YYYY-MM-DD. Si se omite, se usa hoy.'],
            ]),
            $this->declarar('info_cliente', 'Busca datos de un cliente por nombre. Si el usuario es operario, solo puede consultar clientes con los que ha trabajado.', [
                'nombre' => ['type' => 'STRING', 'description' => 'Nombre o parte del nombre del cliente.'],
            ], ['nombre']),
            $this->declarar('mis_fichajes_recientes', 'Lista los últimos fichajes (entrada/salida/descanso) del propio usuario que pregunta.', [
                'dias' => ['type' => 'INTEGER', 'description' => 'Cuántos días hacia atrás mirar. Por defecto 7.'],
            ]),
        ];

        if (! $usuario->esAdmin()) {
            return $comunes;
        }

        return [
            ...$comunes,
            $this->declarar('tareas_de_operario', 'Lista las tareas de un operario concreto, buscado por nombre.', [
                'operario_nombre' => ['type' => 'STRING', 'description' => 'Nombre (o parte) del operario.'],
                'fecha' => ['type' => 'STRING', 'description' => 'Fecha en formato YYYY-MM-DD. Si se omite, se usa hoy.'],
            ], ['operario_nombre']),
            $this->declarar('fichajes_de_operario', 'Lista los fichajes recientes de un operario concreto, buscado por nombre.', [
                'operario_nombre' => ['type' => 'STRING', 'description' => 'Nombre (o parte) del operario.'],
                'dias' => ['type' => 'INTEGER', 'description' => 'Cuántos días hacia atrás mirar. Por defecto 7.'],
            ], ['operario_nombre']),
            $this->declarar('listar_operarios_activos', 'Lista los operarios activos de la empresa.'),
        ];
    }

    /** Ejecuta la función pedida por el modelo, forzando siempre la restricción de rol aquí. */
    public function ejecutar(string $nombre, array $args, User $usuario): array
    {
        if (in_array($nombre, self::SOLO_ADMIN, true) && ! $usuario->esAdmin()) {
            return ['error' => 'No tienes permiso para consultar ese dato.'];
        }

        return match ($nombre) {
            'listar_inventario_herramientas' => $this->listarInventario(),
            'consultar_stock_herramienta' => $this->consultarStock((string) ($args['nombre'] ?? '')),
            'contar_tareas_pendientes' => $this->contarTareasPendientes($args['fecha'] ?? null, $usuario),
            'mis_tareas' => $this->misTareas($args['fecha'] ?? null, $usuario),
            'info_cliente' => $this->infoCliente((string) ($args['nombre'] ?? ''), $usuario),
            'mis_fichajes_recientes' => $this->misFichajes((int) ($args['dias'] ?? 7), $usuario),
            'tareas_de_operario' => $this->tareasDeOperario((string) ($args['operario_nombre'] ?? ''), $args['fecha'] ?? null),
            'fichajes_de_operario' => $this->fichajesDeOperario((string) ($args['operario_nombre'] ?? ''), (int) ($args['dias'] ?? 7)),
            'listar_operarios_activos' => $this->listarOperariosActivos(),
            default => ['error' => 'Función no reconocida.'],
        };
    }

    private function declarar(string $nombre, string $descripcion, array $propiedades = [], array $requeridos = []): array
    {
        $declaracion = [
            'name' => $nombre,
            'description' => $descripcion,
        ];

        if ($propiedades) {
            $declaracion['parameters'] = [
                'type' => 'OBJECT',
                'properties' => $propiedades,
                'required' => $requeridos,
            ];
        }

        return $declaracion;
    }

    private function fecha(?string $fecha): Carbon
    {
        try {
            return $fecha ? Carbon::parse($fecha) : now();
        } catch (\Throwable) {
            return now();
        }
    }

    private function listarInventario(): array
    {
        return [
            'herramientas' => Herramienta::orderBy('nombre')->get(['nombre', 'cantidad'])
                ->map(fn (Herramienta $h) => ['nombre' => $h->nombre, 'cantidad' => $h->cantidad])
                ->all(),
        ];
    }

    private function consultarStock(string $nombre): array
    {
        $herramientas = Herramienta::where('nombre', 'like', "%{$nombre}%")->get(['nombre', 'cantidad']);

        if ($herramientas->isEmpty()) {
            return ['error' => "No se ha encontrado ninguna herramienta que coincida con \"{$nombre}\"."];
        }

        return ['herramientas' => $herramientas->map(fn (Herramienta $h) => ['nombre' => $h->nombre, 'cantidad' => $h->cantidad])->all()];
    }

    private function contarTareasPendientes(?string $fecha, User $usuario): array
    {
        $query = Tarea::where('estado', 'pendiente')->whereDate('fecha', $this->fecha($fecha));

        if (! $usuario->esAdmin()) {
            $query->where('operario_id', $usuario->id);
        }

        return ['tareas_pendientes' => $query->count()];
    }

    private function misTareas(?string $fecha, User $usuario): array
    {
        $tareas = $usuario->tareas()
            ->with('cliente')
            ->whereDate('fecha', $this->fecha($fecha))
            ->orderBy('orden_ruta')
            ->get();

        return [
            'tareas' => $tareas->map(fn (Tarea $t) => [
                'cliente' => $t->cliente?->nombre,
                'hora_estimada' => $t->hora_estimada,
                'estado' => $t->estado,
                'descripcion' => $t->descripcion,
            ])->all(),
        ];
    }

    private function infoCliente(string $nombre, User $usuario): array
    {
        $query = Cliente::where('nombre', 'like', "%{$nombre}%");

        if (! $usuario->esAdmin()) {
            $query->whereHas('tareas', fn ($q) => $q->where('operario_id', $usuario->id));
        }

        $clientes = $query->get(['nombre', 'direccion', 'telefono']);

        if ($clientes->isEmpty()) {
            return ['error' => "No se ha encontrado ningún cliente accesible que coincida con \"{$nombre}\"."];
        }

        return ['clientes' => $clientes->map(fn (Cliente $c) => ['nombre' => $c->nombre, 'direccion' => $c->direccion, 'telefono' => $c->telefono])->all()];
    }

    private function misFichajes(int $dias, User $usuario): array
    {
        $fichajes = $usuario->fichajes()
            ->where('fichado_en', '>=', now()->subDays(max(1, $dias)))
            ->orderByDesc('fichado_en')
            ->get(['tipo', 'fichado_en']);

        return [
            'fichajes' => $fichajes->map(fn ($f) => ['tipo' => $f->tipo, 'fichado_en' => $f->fichado_en->toDateTimeString()])->all(),
        ];
    }

    private function tareasDeOperario(string $operarioNombre, ?string $fecha): array
    {
        $operario = User::where('role', 'operario')->where('name', 'like', "%{$operarioNombre}%")->first();

        if (! $operario) {
            return ['error' => "No se ha encontrado ningún operario que coincida con \"{$operarioNombre}\"."];
        }

        $tareas = $operario->tareas()
            ->with('cliente')
            ->whereDate('fecha', $this->fecha($fecha))
            ->orderBy('orden_ruta')
            ->get();

        return [
            'operario' => $operario->name,
            'tareas' => $tareas->map(fn (Tarea $t) => [
                'cliente' => $t->cliente?->nombre,
                'hora_estimada' => $t->hora_estimada,
                'estado' => $t->estado,
                'descripcion' => $t->descripcion,
            ])->all(),
        ];
    }

    private function fichajesDeOperario(string $operarioNombre, int $dias): array
    {
        $operario = User::where('role', 'operario')->where('name', 'like', "%{$operarioNombre}%")->first();

        if (! $operario) {
            return ['error' => "No se ha encontrado ningún operario que coincida con \"{$operarioNombre}\"."];
        }

        $fichajes = $operario->fichajes()
            ->where('fichado_en', '>=', now()->subDays(max(1, $dias)))
            ->orderByDesc('fichado_en')
            ->get(['tipo', 'fichado_en']);

        return [
            'operario' => $operario->name,
            'fichajes' => $fichajes->map(fn ($f) => ['tipo' => $f->tipo, 'fichado_en' => $f->fichado_en->toDateTimeString()])->all(),
        ];
    }

    private function listarOperariosActivos(): array
    {
        return [
            'operarios' => User::where('role', 'operario')->where('activo', true)->orderBy('name')->pluck('name')->all(),
        ];
    }
}
