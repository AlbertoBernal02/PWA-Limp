<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Str;

class Documentacion
{
    /** @return array<string, string> slug => título, en el orden de config, filtrado por rol */
    public static function indice(?User $usuario = null): array
    {
        return collect(self::seccionesVisibles($usuario))
            ->map(fn (array $seccion) => $seccion['titulo'])
            ->all();
    }

    public static function existe(string $slug, ?User $usuario = null): bool
    {
        return array_key_exists($slug, self::seccionesVisibles($usuario));
    }

    /** Markdown crudo de una sección (para el contexto de la IA). */
    public static function contenidoCrudo(string $slug): string
    {
        $ruta = resource_path("docs/{$slug}.md");

        return is_file($ruta) ? file_get_contents($ruta) : '';
    }

    public static function contenidoHtml(string $slug): string
    {
        return Str::markdown(self::contenidoCrudo($slug));
    }

    /**
     * Todo el contenido visible para este usuario, concatenado, para inyectar
     * como contexto fijo al chat de IA (un operario nunca recibe como contexto
     * apartados de administración que no puede ver en la propia pestaña).
     */
    public static function contenidoCompletoParaIa(?User $usuario = null): string
    {
        return collect(self::seccionesVisibles($usuario))
            ->map(fn (array $seccion, string $slug) => "## {$seccion['titulo']}\n\n".self::contenidoCrudo($slug))
            ->implode("\n\n---\n\n");
    }

    /** @return array<string, array{titulo: string, roles: array<int, string>}> */
    private static function seccionesVisibles(?User $usuario): array
    {
        $secciones = config('documentacion.secciones', []);

        if (! $usuario || $usuario->esAdmin()) {
            return $secciones;
        }

        return array_filter($secciones, fn (array $seccion) => in_array($usuario->role, $seccion['roles'] ?? [], true));
    }
}
