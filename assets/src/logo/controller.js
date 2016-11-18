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

            // Start C&P from handlers.js
            // https://github.com/WordPress/WordPress/blob/32be6f7bb73b5c8e4bdd90179aa85b275606d982/wp-includes/js/plupload/handlers.js#L83-L87

            var serverData = response.response;

            // on success serverData should be numeric, fix bug in html4 runtime returning the serverData wrapped in a <pre> tag
            serverData = serverData.replace(/^<pre>(\d+)<\/pre>$/, '$1');

            // if async-upload returned an error message, place it in the media item div and return
            if ( serverData.match(/media-upload-error|error-div/) ) {
                return;
            }
            // End C&P from handlers.js

			// Triggers Model:fetch( att_id ) & event listeners in the views
			logo.set( { 'att_id' : response.response } );
		} );
	} );
} )( window, jQuery, _, logoUploader, window.wcm.logo.models, window.wcm.logo.views );