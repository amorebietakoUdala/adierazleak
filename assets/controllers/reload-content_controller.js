import '../js/common/list.js';

import { Controller } from '@hotwired/stimulus';
import $ from 'jquery';

export default class extends Controller {
    static targets = ['content'];
    static values = {
        url: String,
        locale: String,
    }

    async refreshContent(event) {
        const target = this.hasContentTarget ? this.contentTarget : this.element;
        target.style.opacity = .5;
        if (event.type === 'entity:success') {
            const response = await fetch(this.urlValue);
            target.innerHTML = await response.text();
        }
        $('#taula').bootstrapTable({
            cache: false,
            showExport: true,
            iconsPrefix: 'fa',
            icons: {
                export: 'fa-download',
            },
            exportTypes: ['excel'],
            exportDataType: 'all',
            exportOptions: {
                fileName: this.entityValue,
                ignoreColumn: ['options']
            },
            showColumns: false,
            pagination: true,
            search: true,
            striped: true,
            sortStable: true,
            pageSize: 10,
            pageList: [10, 25, 50, 100],
            sortable: true,
            locale: this.localeValue + '-' + this.localeValue.toUpperCase(),
        });
        target.style.opacity = 1;

        //let $div = $('div.bootstrap-table.bootstrap5').removeClass('bootstrap5').addClass('bootstrap4');
        // To fix an issue with columns toggle list
        // $('btn btn-secondary dropdown-toggle').data( "toggle", "dropdown" );        
        // $('.page-list').find('button').attr('data-bs-toggle','dropdown');
        var $table = $(this.element);
        if ( this.pageValue !== null && $table.bootstrapTable("getOptions").totalPages >= this.pageValue ) {
            console.log(this.pageValue);
            $table.bootstrapTable('selectPage', this.pageValue);
        }        
    }
}