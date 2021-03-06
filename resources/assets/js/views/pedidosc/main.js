/**
* Class MainPedidoscView
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MainPedidoscView = Backbone.View.extend({

        el: '#pedidosc-main',
        events: {
        },

        /**
        * Constructor Method
        */
        initialize : function() {
            var _this = this;

            // Rerefences
            this.$ajustesSearchTable = this.$('#pedidosc-search-table');
            
            this.$ajustesSearchTable.DataTable({
                dom: "<'row'<'col-sm-4'B><'col-sm-4 text-center'l><'col-sm-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: window.Misc.urlFull( Route.route('pedidosc.index') ),
                columns: [ 
                    { data: 'id', name: 'id' },
                    { data: 'tercero_nombre', name: 'tercero_nombre' },
                    { data: 'sucursal_nombre', name: 'sucursal_nombre' },
                    { data: 'pedidoc1_fecha', name: 'pedidoc1_fecha' },
                ],
                buttons: [
                    {
                        text: '<i class="fa fa-plus"></i> Nuevo pedido',
                        className: 'btn-sm',
                        action: function ( e, dt, node, config ) {
                            window.Misc.redirect( window.Misc.urlFull( Route.route('pedidosc.create') ) )
                        }
                    }
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '15%',
                        render: function ( data, type, full, row ) {
                           return '<a href="'+ window.Misc.urlFull( Route.route('pedidosc.show', {pedidosc: full.id }) )  +'">' + data + '</a>';
                        },
                    }, 
                ],
                fnRowCallback: function( row, data ) {
                    if ( data.pedidoc1_anular == 1 ) {
                        $(row).css( {"color":"red"} );
                    }else{
                        $(row).css( {"color":"green"} );
                    }
                }
            });
        }
    });
})(jQuery, this, this.document);
