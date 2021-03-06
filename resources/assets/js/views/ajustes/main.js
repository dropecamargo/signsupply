/**
* Class MainAjustesView
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MainAjustesView = Backbone.View.extend({

        el: '#ajustes-main',

        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'click .btn-import-modal': 'import'
        },

        /**
        * Constructor Method
        */
        initialize : function() {
            var _this = this;

            // Rerefences
            this.$ajustesSearchTable = this.$('#ajustes-search-table');
            this.$searchFormAjuste = this.$('#form-koi-search-ajuste-component');
            this.$searchAjusteTipo = this.$('#searchajuste_ajuste_tipo');
            this.$searchAjusteSucursal = this.$('#searchajuste_ajuste_sucursal');
            this.$searchAjusteFecha = this.$('#searchajuste_ajuste_fecha');

            this.ajustesSearchTable = this.$ajustesSearchTable.DataTable({
                dom:"<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('ajustes.index') ),
                    data: function( data ) {
                        data.persistent = true;
                        data.tipo = _this.$searchAjusteTipo.val();
                        data.sucursal = _this.$searchAjusteSucursal.val();
                        data.fecha = _this.$searchAjusteFecha.val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id'},
                    { data: 'tipoajuste_nombre', name: 'tipoajuste_nombre' },
                    { data: 'sucursal_nombre', name: 'sucursal_nombre' },
                    { data: 'ajuste1_fecha', name: 'ajuste1_fecha' },
                ],
                columnDefs: [
                    {
                        targets: 0,
                        searchable: false,
                        width: '10%',
                        render: function ( data, type, full, row ) {
                           return '<a href="'+ window.Misc.urlFull( Route.route('ajustes.show', {ajustes: full.id }) )  +'">' + data + '</a>';
                        },

                    },
                ]
            });
        },

        search: function(e) {
            e.preventDefault();

            this.ajustesSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchAjusteTipo.val('').trigger('change');
            this.$searchAjusteSucursal.val('').trigger('change');
            this.$searchAjusteFecha.val('');
            this.ajustesSearchTable.ajax.reload();
        },
        /*
        * Import data of Excel
        */
        import: function(e) {
            var _this = this;

            e.preventDefault();

            // ImportActionView undelegateEvents
            if ( this.importActionView instanceof Backbone.View ){
                this.importActionView.stopListening();
                this.importActionView.undelegateEvents();
            }
            this.importActionView = new app.ImportDataActionView({
                parameters: {
                    title: 'ajustes',
                    url: window.Misc.urlFull( Route.route('ajustes.import') ),
                    datatable: _this.asientosSearchTable
                }
            });
            this.importActionView.render();
        },
    });
})(jQuery, this, this.document);
