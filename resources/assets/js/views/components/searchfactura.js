/**
* Class ComponentSearchFacturaView of Backbone
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ComponentSearchFacturaView = Backbone.View.extend({

        el: 'body',
        template: _.template( ($('#koi-search-factura-component-tpl').html() || '') ),

        events: {
            'change input.factura-koi-component': 'facturaChanged',
            'click .btn-koi-search-factura-component': 'searchFactura',
            'click .btn-search-koi-search-factura-component': 'search',
            'click .btn-clear-koi-search-factura-component': 'clear',
            'click .a-koi-search-factura-component-table': 'setFactura'
        },

        /**
        * Constructor Method
        */
        initialize: function() {
            // Initialize
            this.$modalComponent = this.$('#modal-search-factura-component');
        },

        searchFactura: function (e) {
            e.preventDefault();
            var _this = this;

            // Render template
            this.$modalComponent.find('.content-modal').html( this.template({ }) );

            // References
            this.$searchfacturaNumero = this.$('#searchfactura_numero');
            this.$searchfacturaTercero = this.$('#searchfactura_tercero');
            this.$searchfacturaTerceroNombre = this.$('#searchfactura_tercero_nombre');

            this.$facturaSearchTable = this.$modalComponent.find('#koi-search-factura-component-table');
            this.$inputContent = this.$("#"+$(e.currentTarget).attr("data-field"));
            this.$inputName = this.$("#"+this.$inputContent.attr("data-name"));
            this.$inputNit = this.$("#"+this.$inputContent.attr("data-nit"));
            this.$inputSucursal = this.$("#"+this.$inputContent.attr("data-sucursal"));
            this.devueltas = this.$inputContent.attr("data-devueltas");

            if (this.$inputSucursal.val() == '') 
                return alertify.error('Por favor ingrese sucursal');

            this.facturaSearchTable = this.$facturaSearchTable.DataTable({
                dom: "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('facturas.index') ),
                    data: function( data ) {
                        data.numero = _this.$searchfacturaNumero.val();
                        data.tercero_nit = _this.$searchfacturaTercero.val();
                        data.sucursal = _this.$inputSucursal.val();
                        data.devueltas = _this.devueltas;
                    }
                },
                columns: [
                    { data: 'factura1_numero', name: 'factura1_numero' },
                    { data: 'tercero_nombre', name: 'tercero_nombre' },
                    { data: 'sucursal_nombre', name: 'sucursal_nombre' },
                    { data: 'puntoventa_prefijo', name: 'puntoventa_prefijo' },
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%',
                        searchable: false,
                        render: function ( data, type, full, row ) {
                            return '<a href="#" class="a-koi-search-factura-component-table">' + data + '</a>';
                        }
                    }
                ]
            });
            // Modal show
            this.ready();
            this.$modalComponent.modal('show');
        },
        setFactura: function(e) {
            e.preventDefault();
            var data = this.facturaSearchTable.row( $(e.currentTarget).parents('tr') ).data();

            this.$inputContent.val( data.factura1_numero );
            this.$inputName.val( data.tercero_nombre );
            this.$inputNit.val( data.tercero_nit );

            if (this.$inputNit.length)
               this.$inputNit.trigger('change',[data.id]);

            this.$modalComponent.modal('hide');
        },

        search: function(e) {
            e.preventDefault();

            this.facturaSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchfacturaNumero.val('');
            this.$searchfacturaTercero.val('');
            this.$searchfacturaTerceroNombre.val('');

            this.facturaSearchTable.ajax.reload();
        },
        facturaChanged: function(e) {
            var _this = this;
            this.$inputContent = $(e.currentTarget);
            this.$wraperConten = this.$("#"+$(e.currentTarget).attr("data-wrapper"));
            this.$inputSucursal = this.$("#"+$(e.currentTarget).attr("data-sucursal"));       
            this.$inputName = this.$("#"+$(e.currentTarget).attr("data-name"));       
            this.$inputNit = this.$("#"+$(e.currentTarget).attr("data-nit"));       
   
            var factura = this.$inputContent.val();
                sucursal = this.$inputSucursal.val();

            if ( sucursal == '' ) {
                this.$inputContent.val('');
                alertify.error('Por favor seleccione sucursal antes de buscar una factura');
                return;
            }
            if(!_.isUndefined(factura) && !_.isNull(factura) && factura != '') {
                // Get Producto
                $.ajax({
                    url: window.Misc.urlFull(Route.route('facturas.search')),
                    type: 'GET',
                    data: { factura_numero: factura,
                            factura_sucursal:sucursal },
                    beforeSend: function() {
                        window.Misc.setSpinner( _this.$wraperConten );
                    }
                })
                .done(function(resp) {
                    window.Misc.removeSpinner( _this.$wraperConten );
                    
                    if(resp.success) {
                        if(!_.isUndefined(resp.cliente) && !_.isNull(resp.cliente)){
                            _this.$inputName.val(resp.cliente);
                        }   
                        if(!_.isUndefined(resp.nit) && !_.isNull(resp.nit)){
                            _this.$inputNit.val(resp.nit);
                        }
                        if (_this.$inputNit.length)
                        _this.$inputNit.trigger('change',[resp.id]);
                    }
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    window.Misc.removeSpinner( _this.$wraperConten );
                    alertify.error(thrownError);
                });
            }
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // to fire plugins
            if( typeof window.initComponent.initToUpper == 'function' )
                window.initComponent.initToUpper();
        }
    });


})(jQuery, this, this.document);
