/**
* Class MainSubCategoriasView
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MainSubCategoriasView = Backbone.View.extend({

        el: '#subcategorias-main',

        /**
        * Constructor Method
        */
        initialize : function() {

            this.$marcasSearchTable = this.$('#subcategorias-search-table');
            this.$marcasSearchTable.DataTable({
                dom: "<'row'<'col-sm-4'B><'col-sm-4 text-center'l><'col-sm-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('subcategorias.index') ),
                    data: function(data){
                        data.datatables = true;
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'subcategoria_nombre', name: 'subcategoria_nombre' },
                    { data: 'subcategoria_activo', name: 'subcategoria_activo'}
                ],
                buttons: [
                    {
                        text: '<i class="fa fa-plus"></i> Nueva subcategoria',
                        className: 'btn-sm',
                        action: function ( e, dt, node, config ) {
                            window.Misc.redirect( window.Misc.urlFull( Route.route('subcategorias.create') ) )
                        }
                    }
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%',
                        render: function ( data, type, full, row ) {
                            return '<a href="'+ window.Misc.urlFull( Route.route('subcategorias.show', {subcategorias: full.id }) )  +'">' + data + '</a>';
                        }
                    },
                    {
                        targets: 2,
                        width: '10%',
                        render: function ( data, type, full, row ) {
                            return parseInt(data) ? 'Si' : 'No';
                        },
                    }
                ]
            });
        }
    });

})(jQuery, this, this.document);
