/**
* Class MainConceptosCajaMenorView
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MainConceptosCajaMenorView = Backbone.View.extend({

        el: '#conceptoscajamenor-main',

        /**
        * Constructor Method
        */
        initialize : function() {

            this.$conceptoscajamenorSearchTable = this.$('#conceptoscajamenor-search-table');
            this.$conceptoscajamenorSearchTable.DataTable({
                dom: "<'row'<'col-sm-4'B><'col-sm-4 text-center'l><'col-sm-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: window.Misc.urlFull( Route.route('conceptoscajamenor.index') ),
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'conceptocajamenor_nombre', name: 'conceptocajamenor_nombre' },
                    { data: 'conceptocajamenor_activo', name: 'conceptocajamenor_activo'}
                ],
                buttons: [
                    {
                        text: '<i class="fa fa-plus"></i> Nuevo concepto',
                        className: 'btn-sm',
                        action: function ( e, dt, node, config ) {
                            window.Misc.redirect( window.Misc.urlFull( Route.route('conceptoscajamenor.create') ) )
                        }
                    }
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%',
                        render: function ( data, type, full, row ) {
                            return '<a href="'+ window.Misc.urlFull( Route.route('conceptoscajamenor.show', {conceptoscajamenor: full.id }) )  +'">' + data + '</a>';
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
