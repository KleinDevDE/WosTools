import {Alpine, Livewire} from '../../vendor/livewire/livewire/dist/livewire.esm';

// AlpineJS Plugins
// import persist from "@alpinejs/persist"; // @see https://alpinejs.dev/plugins/persist
import collapse from "@alpinejs/collapse"; // @see https://alpinejs.dev/plugins/collapse
import intersect from "@alpinejs/intersect"; // @see https://alpinejs.dev/plugins/intersect

// Third Party Libraries


/*
    Flowbite
    @see https://flowbite.com/
 */
import "flowbite";

// Import Fortawesome icons
import "@fortawesome/fontawesome-free/css/all.css";

// Helper Functions
import {EventHelper, dispatchCustomEvent} from "./helpers/EventHelper";

// Alpine Magic Functions
import {copyToClipboard} from "./alpine/magics/clipboard";

// SHA256 Library for hashing
import sha256 from 'js-sha256';
window.sha256 = sha256;

window.Alpine = Alpine;
window.EventHelper = EventHelper;
window.dispatchCustomEvent = dispatchCustomEvent;

import $ from "jquery";
window.$ = window.jQuery = $;

document.addEventListener('alpine:init', () => {
    console.log('alpine:init')
});
// Alpine.plugin(persist);
Alpine.plugin(collapse);
Alpine.plugin(intersect);

// Alpine.magic("notification", () => notification);
// window.notification = notification;
Alpine.magic("clipboard", () => copyToClipboard);
// Alpine.start()
Livewire.start();

// import.meta.glob([
//     "../images/**",
// ]);

if (import.meta.hot) {
    // import.meta.hot.on('vite:livewireModuleLoaded', (data) => {
    //     console.log('Hot reloading...', data);
    //
    //     //Reload livewire component by looping over all found by name
    //     Livewire.getByName(data.component).forEach((component) => {
    //         console.log(component.$el);
    //         console.log(component.$refresh());
    //         // component.$refresh();
    //     });
    // })

    import.meta.hot.on('vite:livewireModuleLoaded', (data) => {
        console.log('Hot reloading Livewire component...', data);

        // Suche die betroffenen Livewire-Komponenten und aktualisiere sie gezielt
        const components = Livewire.getByName(data.component);
        if (components.length > 0) {
            components.forEach((component) => {
                console.log('Refreshing component:', component.name);
                component.$refresh();
            });
        } else {
            console.log('No Livewire components found for:', data.component);
        }
    });
}

window.dispatchCustomEvent = function (name, data) {
    //CustomEvent constructor: Value can't be converted to a dictionary.
    dispatchEvent(new CustomEvent(name, {detail: data}));
}
