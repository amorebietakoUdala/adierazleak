import { Controller } from '@hotwired/stimulus';

import '../js/common/list';
import {createConfirmationAlert} from '../js/common/alert';

export default class extends Controller {
    static targets = [];
    static values = {
        exportName: String,
        pageSize: Number,
        page: Number,
        sortName: String,
        sortOrder: String,
        roles: Array,
    }
    params = new URLSearchParams();

    connect() {
        let controller = this;
        $(this.element).bootstrapTable({
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
            sortName: this.sortNameValue,
            sortOrder: this.sortOrderValue,
            pageSize: this.hasPageSizeValue ? this.pageSizeValue : 10,
            pageList: [10, 25, 50, 100],
            sortable: true,
            locale: $('html').attr('lang') + '-' + $('html').attr('lang').toUpperCase(),
        });
        var $table = $(this.element);
        $(function() {
            $('#toolbar').find('select').change(function() {
                $table.bootstrapTable('destroy').bootstrapTable({
                    exportDataType: $(this).val(),
                });
            });
        });
        $table.on('page-change.bs.table',function(e, page, pageSize) {
            controller.dispatch('pageChange', { detail: { 'page': page, 'pageSize' : pageSize }});
        }); 
        $table.on('sort.bs.table',function(e, sortName, sortOrder) {
            controller.dispatch('orderChange', { detail: { 'sortName': sortName, 'sortOrder' : sortOrder }});
        }); 
        $('.page-list').find('button').attr('data-bs-toggle','dropdown');
        if ( this.pageValue !== null && $table.bootstrapTable("getOptions").totalPages >= this.pageValue ) {
            $table.bootstrapTable('selectPage', this.pageValue);
        }
    }

    onClick(event) {
        event.preventDefault();
        const destination = event.currentTarget.href;
        this.createQueryParameters(event);
        document.location.href= destination + '?' + this.params.toString(); 
    }

    onDelete(event) {
        event.preventDefault();
        this.createQueryParameters(event);
        var href = event.currentTarget.href;
        createConfirmationAlert(href + '?' + this.params.toString());
    }

    createQueryParameters(event) {
        const page = $(this.element).bootstrapTable('getOptions').pageNumber != null ? $(this.element).bootstrapTable('getOptions').pageNumber : 1;
        const pageSize = $(this.element).bootstrapTable('getOptions').pageSize != null ? $(this.element).bootstrapTable('getOptions').pageSize : 10;
        const sortName = $(this.element).bootstrapTable('getOptions').sortName != null ? $(this.element).bootstrapTable('getOptions').sortName : 0;
        const sortOrder = $(this.element).bootstrapTable('getOptions').sortOrder != null ? $(this.element).bootstrapTable('getOptions').sortOrder : 'asc';
        this.params.set('page', page);
        this.params.set('pageSize', pageSize);
        this.params.set('sortName', sortName);
        this.params.set('sortOrder', sortOrder);
        this.createReturnUrlParameter(event);
        if (event.currentTarget.dataset.pagination == "false") {
            this.params.delete('page');
            this.params.delete('pageSize');
            this.params.delete('sortName');
            this.params.delete('sortOrder');
            this.params.delete('roles');
        }
    }

    createReturnUrlParameter(event) {
        let returnUrl = new URL(event.currentTarget.dataset.returnUrl);
        const urlParams = new URLSearchParams(returnUrl.search);
        if (returnUrl != null) {
            if ( this.hasRolesValue() ) {
                this.params.set('roles',this.rolesValue.join(','));
            }
            let entries = this.params.entries();
            for (let [key, value] of entries) {
                urlParams.append(key, value);
            }
            returnUrl = `${returnUrl.origin}${returnUrl.pathname}?`+urlParams.toString();
            this.params.set('returnUrl', returnUrl);
        }
    }
}
     
