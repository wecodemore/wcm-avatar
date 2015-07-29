/** @namespace logoUploader */
( function( window, $, _, plugin, models, views ) {
	'use strict';

	var logo = new models.Logo(),
		controller = {};

	// Flatten 'data', remove sub array
	logo.set( _.extend( plugin.data, {
		container : plugin.container,
		templates : plugin.templates,
		l10n      : plugin.l10n
	} ) );
	// Update model
	logo.on( 'change', logo.fetch );

	/** @namespace plugin.container.main */
	controller.Preview = new views.Preview( {
		el    : $( '#' + plugin.container.main ),
		model : logo
	} );

	/** @namespace plugin.container.thumb */
	controller.Thumb = new views.Thumb( {
		el    : $( '#' + plugin.container.thumb ),
		model : logo
	} );

	/** @namespace plugin.container.delete */
	controller.Delete = new views.Delete( {
		el    : $( '#' + plugin.container.delete ),
		model : logo
	} );

	/** @namespace plugin.container.caption */
	controller.Caption = new views.Caption( {
		el    : $( '#' + plugin.container.caption ),
		model : logo
	} );

	// Attach event handler
	$( document ).ready( function() {
		'use strict';

		// Bind event handler to file upload / drag&drop
		uploader.bind( 'FileUploaded', function( up, file, response ) {

			if ( 200 !== response.status ) {
				return;
			}

			// Triggers Model:fetch( att_id ) & event listeners in the views
			logo.set( { 'att_id' : response.response } );
		} );
	} );
} )( window, jQuery, _, logoUploader, window.wcm.logo.models, window.wcm.logo.views );