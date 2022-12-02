import '../js/common/list.js';

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content'];
    static values = {
        url: String,
        locale: String,
        filters: Array,
    }

    page = null;
    pageSize = null;
    sortName = null;
    sortOrder = null;
    params =  new URLSearchParams({ajax: true});

    async refreshContent(event) {
        this.params.set('page', this.page);
        this.params.set('pageSize', this.pageSize);
        if ( this.sortName != null && this.sortOrder != null ) {
            this.params.set('sortName', this.sortName);
            this.params.set('sortOrder', this.sortOrder);
        }
        if (this.filtersValue) {
            this.params.set('roles',this.filtersValue.join(','));
        }

        const target = this.hasContentTarget ? this.contentTarget : this.element;
        target.style.opacity = .5;
        if (event.type === 'entity:success') {
            const response = await fetch(this.urlValue+ '?' + this.params.toString());
            target.innerHTML = await response.text();
        }
        target.style.opacity = 1;
    }

    onPageChange(e) {
        this.page = e.detail.page;
        this.pageSize = e.detail.pageSize;
    }

    onOrderChange(e) {
        this.sortName = e.detail.sortName;
        this.sortOrder = e.detail.sortOrder;
    }
}