import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [];

    changeLocale(event) {
        event.preventDefault();
        if ( global.locale === event.currentTarget.innerHTML) {
            return;
        } else {
            let location = window.location.href;
            let location_new = location.replace("/" + global.locale + "/", "/" + event.currentTarget.innerHTML + "/");
            window.location.href = location_new;
        }
    }
}