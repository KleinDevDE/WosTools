import './bootstrap';

import $ from 'jquery';
window.$ = window.jQuery = $;

import sha256 from 'js-sha256';
window.sha256 = sha256;

import 'flowbite';

console.log("Starting Livewire")
import {Alpine, Livewire} from '../../vendor/livewire/livewire/dist/livewire.esm';
import ToastComponent from '../../vendor/usernotnull/tall-toasts/resources/js/tall-toasts'
// import '../../vendor/rappasoft/laravel-livewire-tables/resources/imports/laravel-livewire-tables.js';
// import '../../vendor/rappasoft/laravel-livewire-tables/resources/imports/laravel-livewire-tables-thirdparty.js';
Alpine.plugin(ToastComponent)
// FIXME: Calling this makes Livewire start twice, but assets injection got disabled in the livewire config
Livewire.start()
console.log("Started Livewire")

window.copyToClipboard = async function (text) {
    if (navigator.clipboard) {
        await navigator.clipboard.writeText(text);
    } else {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        textarea.style.pointerEvents = 'none';
        document.body.appendChild(textarea);
        textarea.select();

        const result = document.execCommand('copy');
        document.body.removeChild(textarea);

        if (!result) {
            throw new Error('Failed to copy text to clipboard');
        }
    }
};
