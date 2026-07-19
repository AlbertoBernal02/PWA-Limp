import '@hotwired/turbo';

import Alpine from 'alpinejs';
import { initContextMenus } from './context-menu';
import { initAddressAutocomplete } from './address-autocomplete';
import { initMarcarHechaOffline } from './marcar-hecha-offline';
import { initOfflineSync } from './offline-queue';
import { initInstallPrompt } from './install-prompt';
import { initPushNotifications } from './push-notifications';
import { initEmpezarTarea } from './empezar-tarea';

window.Alpine = Alpine;

// Turbo sustituye el <body> en cada navegación; sin este re-init, Alpine
// se queda con el árbol que recorrió al arrancar y no procesa directivas
// (@click, x-data, etc.) del contenido de la página siguiente.
document.addEventListener('turbo:load', () => Alpine.initTree(document.body));

Alpine.start();
initContextMenus();
initAddressAutocomplete();
initMarcarHechaOffline();
initOfflineSync();
initInstallPrompt();
initPushNotifications();
initEmpezarTarea();
