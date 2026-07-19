<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Proxies de confianza
    |--------------------------------------------------------------------------
    |
    | IPs (separadas por comas en TRUSTED_PROXIES) desde las que se acepta la
    | cabecera X-Forwarded-Proto/For/Host — típicamente el proxy inverso o CDN
    | que hay delante de la app (Cloudflare Tunnel en local, un balanceador en
    | producción...). Vacío por defecto: sin uno delante, no hay que confiar en
    | nadie, o cualquiera podría falsificar su IP/protocolo con esas cabeceras.
    |
    */

    'proxies' => env('TRUSTED_PROXIES') ? explode(',', env('TRUSTED_PROXIES')) : null,

];
