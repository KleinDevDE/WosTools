'use strict'

export class EventHelper extends Function {
    static dispatch(event, data) {
        window.dispatchEvent(new CustomEvent(event, { detail: data }));
    }

    static listen(event, callback) {
        window.addEventListener(event, callback);
    }
}
export function dispatchCustomEvent(name, data) {
    //CustomEvent constructor: Value can't be converted to a dictionary.
    dispatchEvent(new CustomEvent(name, {detail: data}));
}
