//noinspection ThisExpressionReferencesGlobalObjectJS,JSUnresolvedVariable
( function( window, $, Backbone, plugin ) {
	'use strict';

	var models = {};

	models.Logo = Backbone.Model.extend( {

		urlRoot : ajaxurl,

		defaults : {
			container : {},
			templates : {},
			l10n      : {},
			att_id    : 0,
			width     : 0,
			height    : 0,
			thumb     : '',
			name      : '',
			link      : ''
		},

		initialize : function() {
			// this.queue = $.Deferred();
			// this.queue.resolve();
		},

		fetch : function() {

			var self = this;

			$.when(
				$.post( this.urlRoot, {
					action      : plugin.action,
					_ajax_nonce : plugin._ajax_nonce,
					att_id      : self.get('att_id'),
					task        : 'fetch'
				} )
			)
				.done( function( response ) {
					self.set( 'thumb',  response.data.thumb );
					self.set( 'name',   response.data.name );
					self.set( 'width',  response.data.width );
					self.set( 'height', response.data.height );
					self.set( 'link',   response.data.link );
				} )
				.fail( function( reason ) {
					//console.log( 'Model:fetch Error',reason );
				} );

			/*wp.ajax.post( plugin.action, {
				_ajax_nonce : plugin._ajax_nonce,
				att_id      : self.get('att_id'),
				task        : 'fetch'
			} )
				.done( function( response ) {
					 self.set( {
						 att_id : self.get('att_id'),
						 width  : response.data.width,
						 height : response.data.height,
						 thumb  : response.data.thumb,
						 name   : response.data.name,
						 link   : response.data.link
					 } );
				} );*/
		},

		save : function( att_id ) {},

		// Triggered by: views.Delete
		destroy : function( att_id ) {
			var self = this;
			$.when(
				$.post( this.urlRoot, {
					action      : plugin.action,
					_ajax_nonce : plugin._ajax_nonce,
					att_id      : self.get('att_id'),
					task        : 'destroy'
				} )
			)
				.then(
					// success
					function( response ) {
						//console.log( 'Model:destroy Success', response );
					},
					// error
					function( reason ) {
						//console.log( 'Model:destroy Error', reason );
					}
				);
			return {};
		}
	} );

	window.wcm = window.wcm || {};
	window.wcm.logo = window.wcm.logo || {};
	window.wcm.logo.models = models;

} )( this, jQuery, Backbone, logoUploader );