<?php
/**
 * Plugin Name: (WCM) Avatar
 * Plugin URI:  https://github.com/wecodemore/wcm-avatar
 * Description: Allows uploading an attachment in a users profile to be used instead of an avatar
 * Author:      Franz Josef Kaiser <wecodemore@gmail.com>
 * Author URl:  http://unserkaiser.com
 * Text Domain: wcmavatar
 * Domain Path: /lang
 */

/**
 * This file is part of the WCM Avatar package.
 *
 * © Franz Josef Kaiser / wecodemore
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( file_exists( __DIR__.'/vendor/autoload.php' ) )
	require __DIR__.'/vendor/autoload.php';

use WCM\User\Avatar\Mediators\ServiceDispatcher;
use WCM\User\Avatar\Controllers;
use WCM\User\Avatar\Services;
use WCM\User\Avatar\Views;
use WCM\User\Avatar\Models;
use WCM\User\Avatar\Templates;

add_action( 'plugins_loaded', function()
{
	// Dispatcher: Attach Services, dispatches them on a hook
	$dispatcher = new ServiceDispatcher;


	// Handles the drag & drop uploader logic for the user avatar
	// Use this filter to adjust the meta key with which a theme author
	// can fetch the attachment ID to load the avatar
	$key = apply_filters( 'wcm_user_avatar_meta_key', 'user_avatar' );

	// Register the 'user_id' and 'screen_id' as additional data for attachment uploading
	$dispatcher->register(
		new Services\AvatarRegisterMetaService,
		'upload_post_params'
	);

	// Connect user and attachment via their IDs as meta data
	$dispatcher->register(
		new Services\AvatarAddMetaService( $key ),
		'wp_generate_attachment_metadata'
	);

	// Remove the user meta data when the attachment gets deleted
	$dispatcher->register(
		new Services\AvatarDeleteMetaService( $key ),
		'delete_attachment'
	);

	// Remove the attachment post when the user gets deleted
	$dispatcher->register(
		new Services\AvatarDeleteService( $key ),
		'delete_user'
	);

	// Save files when using the "Browser Uploader"
	$dispatcher->register(
		new Services\AvatarBrowserUploaderSaveService( $key ),
		[ 'load-profile.php', 'load-user-edit.php', ]
	);

	// Ajax Model
	$ajax = new Models\AjaxAware(
		$key,
		"{$key}_nonce"
	);

	// Allow registering Underscore Templates using the WP Dependency API
	$dispatcher->register(
		new Services\UnderscoreTemplateScripts,
		'script_loader_tag'
	);

	// Register the AJAX handling scripts, localize the needed data
	$dispatcher->register(
		new Services\AvatarScriptsService(
			$ajax,
			$key,
			plugin_dir_url( __FILE__ ),
			plugin_dir_path( __FILE__ )
		),
		'admin_enqueue_scripts'
	);

	// Register the AJAX handler/callback to fetch/delete Attachments
	// using the "Multi-File Uploader"
	$dispatcher->register(
		new Services\AvatarFetchDeleteAjaxService( $ajax ),
		"wp_ajax_{$ajax->getAction()}"
	);

	// Adds either the drag & drop uploader for the user avatar or the avatar itself
	$dispatcher->register(
		new Views\UploadView(
			new Templates\AvatarUploadTemplate( $key ),
			new Templates\AvatarDisplayTemplate,
			$key
		),
		'all_admin_notices'
	);

	// Target for the Avatar Backbone template
	add_action( 'all_admin_notices', function() use ( $key )
	{
		echo '<div id="tmpl-main--container"></div>';
	}, 20 );


	// Limit upload size of image to 256 * 512 = 128 kB
	$dispatcher->register(
		new Services\AvatarUploadSizeLimit(
			'manage_options',
			256 * 512,
			[ 'profile', 'user-edit', 'media', 'upload', ]
		),
		'upload_size_limit'
	);


	// Limit allowed MIME types for image uploads
	$dispatcher->register(
		new Services\AvatarMIMELimitService(
			'manage_options',
			[ 'jpg|jpeg|jpe', 'gif', 'png', 'webp', ],
			[ 'profile', 'user-edit', 'media', 'upload', ]
		),
		'upload_mimes'
	);


	// Limit width/height of uploaded images
	$dispatcher->register(
		new Services\AvatarDimensionLimitService(),
		'wp_handle_upload_prefilter'
	);


	// Add Image to User Columns
	$dispatcher->register(
		new Services\AvatarUserColumnService( $key ),
		'manage_users_columns'
	);


	// Register stylesheet for Icons and other UI integration
	$dispatcher->register(
		new Services\AdminStylesService( __FILE__ ),
		'admin_enqueue_scripts'
	);
} );