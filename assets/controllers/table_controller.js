import { Controller } from '@hotwired/stimulus';

import '../js/common/list';
import { createConfirmationAlert } from '../js/common/alert';

export default class extends Controller {
    static targets = [
        'table'
    ];
    static values = {
        url: String,
        exportName: String,
        filters: Object,
    }

    params = new URLSearchParams(this.filtersValue);

    getOptions() {
        return {
            cache: false,
            showExport: true,
            iconsPrefix: 'fa',
            icons: {
                export: 'fa-download',
            },
            exportTypes: ['excel'],
            exportDataType: 'all',
            exportOptions: {
                fileName: this.exportNameValue,
                ignoreColumn: ['options']
            },
            showColumns: false,
            pagination: true,
            search: true,
            striped: true,
            sortStable: true,
            pageList: [10, 25, 50, 100],
            sortable: true,
            locale: $('html').attr('lang') + '-' + $('html').attr('lang').toUpperCase(),
        };
    }

    connect() {
        let controller = this;
        $(this.tableTarget).bootstrapTable(this.getOptions());
        var $table = $(this.tableTarget);
        $(function() {
            $('#toolbar').find('select').change(function() {
                $table.bootstrapTable('destroy').bootstrapTable({
                    exportDataType: $(this).val(),
                });
            });
        });
        $table.on('page-change.bs.table',function(e, page, pageSize) {
            controller.params.set('page', page);
            controller.params.set('pageSize', pageSize);
        }); 
        $table.on('sort.bs.table',function(e, sortName, sortOrder) {
            controller.params.set('sortName', sortName);
            controller.params.set('sortOrder', sortOrder);
        }); 
        $('.page-list').find('button').attr('data-bs-toggle','dropdown');
        if ( this.pageValue !== null && $table.bootstrapTable("getOptions").totalPages >= this.pageValue ) {
            $table.bootstrapTable('selectPage', this.pageValue);
        }
    }

    onClick(event) {
        event.preventDefault();
        const destination = event.currentTarget.href;
        const confirm = event.currentTarget.dataset.confirm;
        this.createQueryParameters(event);
        if ( confirm == "true") {
            createConfirmationAlert(destination + '?' + this.params.toString()); 
        } else {
            document.location.href= destination + '?' + this.params.toString(); 
        }
    }

    createQueryParameters(event) {
        if ( this.hasTableTarget) {
            this.setPaginationParameters();
        }
        const ajax = event.currentTarget.dataset.ajax;
        if ( ajax != null && ajax == "false" ) {
            this.params.delete('ajax');
        }
        this.setFilterParameters();
        if (    event.currentTarget.dataset.returnUrl != null ) {
            this.createReturnUrlParameter(event);
        }
        if (event.currentTarget.dataset.pagination == "false") {
            this.deletePaginationParameters();
            this.deleteFilterParameters();
        }
    }

    setFilterParameters() {
        if ( this.hasFiltersValue ) {
            for (const property in this.filtersValue) {
                this.params.set(property,this.filtersValue[property]);
            }            
        }
    }

    setPaginationParameters() {
        const page = $(this.tableTarget).bootstrapTable('getOptions').pageNumber != null ? $(this.tableTarget).bootstrapTable('getOptions').pageNumber : 1;
        const pageSize = $(this.tableTarget).bootstrapTable('getOptions').pageSize != null ? $(this.tableTarget).bootstrapTable('getOptions').pageSize : 10;
        const sortName = $(this.tableTarget).bootstrapTable('getOptions').sortName != null ? $(this.tableTarget).bootstrapTable('getOptions').sortName : 0;
        const sortOrder = $(this.tableTarget).bootstrapTable('getOptions').sortOrder != null ? $(this.tableTarget).bootstrapTable('getOptions').sortOrder : 'asc';
        this.params.set('page', page);
        this.params.set('pageSize', pageSize);
        this.params.set('sortName', sortName);
        this.params.set('sortOrder', sortOrder);
    }

    deleteFilterParameters() {
        if ( this.hasFiltersValue ) {
            for (const property in this.filtersValue) {
                this.params.delete(property);
            }            
        }
    }

    deletePaginationParameters() {
        this.params.delete('page');
        this.params.delete('pageSize');
        this.params.delete('sortName');
        this.params.delete('sortOrder');
    }

    createReturnUrlParameter(event) {
        let returnUrl = new URL(event.currentTarget.dataset.returnUrl);
        const urlParams = new URLSearchParams(returnUrl.search);
        if (returnUrl != null) {
            let entries = this.params.entries();
            for (let [key, value] of entries) {
                urlParams.append(key, value);
            }
            returnUrl = `${returnUrl.origin}${returnUrl.pathname}?`+urlParams.toString();
            this.params.set('returnUrl', returnUrl);
        }
    }

    async refreshContent(event) {
        this.params.set('ajax', true);
        this.setPaginationParameters();
        const target = this.hasContentTarget ? this.contentTarget : this.element;
        target.style.opacity = .5;
        if (event.type === 'entity:success') {
            const response = await fetch(this.urlValue+ '?' + this.params.toString());
            target.innerHTML = await response.text();
        }
        target.style.opacity = 1;
        $(this.tableTarget).bootstrapTable(this.getOptions());
    }
}
     
