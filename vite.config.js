import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

import collectModuleAssetsPaths from "./vite-module-loader";
import path from 'path'
import tailwindcss from "@tailwindcss/vite";

const corePaths = [
    //APP
    'resources/css/app.css',
    'resources/js/app.js',
];
const allPaths = await collectModuleAssetsPaths(corePaths, 'Modules');


export default defineConfig({
    server: {
        https: true,
        host: 'wostools.test',
    },
    plugins: [
        laravel({
            input: allPaths,
            refresh: true,
            // refresh: [
            //     "./src/**/*.{php,html,js,jsx,ts,tsx,vue}",
            //     "./resources/**/*.{php,html,js,jsx,ts,tsx,vue}",
            //     "./Modules/*/resources/**/*.{php,html,js,jsx,ts,tsx,vue}",
            //     "./storage/framework/views/*.php",
            // ]
        }),
        tailwindcss(),
        {
            name: 'livewire-reloader',
            handleHotUpdate({ file, server }) {
                if(file.includes('framework/views')) {
                    return;
                }
                if(file.includes('storage/logs')) {
                    return;
                }

                //if contains livewire
                if (!file.includes('livewire')) {
                    server.ws.send({ type: "full-reload" })
                    //Do default behavior
                    console.log('Default full reload for:', file);
                    return;
                }

                // Modul und Komponenteninformationen extrahieren
                const moduleMatch = file.match(/Modules\/([^\/]*)/);
                //Get filename. Example: resources/views/livewire/a/b.blade.php -> a.b

                //Get the path after "views/livewire" and remove .blade.php from the and replace / with dots
                const filenameMatch = file.match(/views\/livewire\/(.*)\.blade\.php/);
                filenameMatch[1] = filenameMatch[1].replace(/\//g, '.');

                if (!filenameMatch) {
                    console.error('Could not extract filename for:', file);
                    server.ws.send({ type: 'full-reload' });
                    return;
                }

                let component = filenameMatch[1];
                if (moduleMatch) {
                    console.log("Module: ", moduleMatch[1]);
                    component = `${moduleMatch[1].toLowerCase()}::${filenameMatch[1]}`;
                }

                //Send event to Livewire
                console.log('Hot reloading Livewire component:', component);
                server.ws.send({
                    type: 'custom',
                    event: 'vite:livewireModuleLoaded', // Sende eigenes Event
                    data: {
                        component: component
                    },
                });
            },
        },
    ],
    // Hide console.log after run `npm run build`
    // Source: https://github.com/vitejs/vite/discussions/7920
    esbuild: {
        drop: ['console', 'debugger'],
    },
    resolve: {
        alias: {
            '@core': path.resolve(__dirname, 'resources/js/core'),
        },
    },
});
