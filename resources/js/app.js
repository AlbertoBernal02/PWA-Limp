import '@hotwired/turbo';

import Alpine from 'alpinejs';
import { initContextMenus } from './context-menu';
import { initAddressAutocomplete } from './address-autocomplete';

window.Alpine = Alpine;

Alpine.start();
initContextMenus();
initAddressAutocomplete();
