import '@hotwired/turbo';

import Alpine from 'alpinejs';
import { initContextMenus } from './context-menu';
import { initAddressAutocomplete } from './address-autocomplete';
import { initMarcarHechaOffline } from './marcar-hecha-offline';
import { initOfflineSync } from './offline-queue';
import { initInstallPrompt } from './install-prompt';
import { initPushNotifications } from './push-notifications';
import { initEmpezarTarea } from './empezar-tarea';
import { initAlmacenDrag } from './almacen-drag';
import { initTrabajoSugerencias } from './trabajo-sugerencias';
import { initChatFlotante } from './chat-flotante';

window.Alpine = Alpine;

// Turbo sustituye el <body> en cada navegación; sin este re-init, Alpine
// se queda con el árbol que recorrió al arrancar y no procesa directivas
// (@click, x-data, etc.) del contenido de la página siguiente.
document.addEventListener('turbo:load', () => Alpine.initTree(document.body));

// initChatFlotante() registra window.chatFlotante, que x-data="chatFlotante()"
// necesita resolver. Tiene que ir ANTES de Alpine.start(): si no, en una recarga
// completa de una página que ya trae ese x-data en el HTML inicial (no llegada
// por Turbo), Alpine intenta evaluarlo antes de que la función exista.
initChatFlotante();

Alpine.start();
initContextMenus();
initAddressAutocomplete();
initMarcarHechaOffline();
initOfflineSync();
initInstallPrompt();
initPushNotifications();
initEmpezarTarea();
initAlmacenDrag();
initTrabajoSugerencias();
