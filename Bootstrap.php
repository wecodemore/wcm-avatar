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

use WCM\User\Avatar\Controllers;
use WCM\User\Avatar\Services;
use WCM\User\Avatar\Views;
use WCM\User\Avatar\Models;
use WCM\User\Avatar\Templates;

add_action( 'plugins_loaded', function()
{
	// Handles the drag & drop uploader logic for the user avatar
	// Use this filter to adjust the meta key with which a theme author
	// can fetch the attachment ID to load the avatar
	$key = apply_filters( 'wcm_user_avatar_meta_key', 'user_avatar' );

	// Register the 'user_id' and 'screen_id'
	// as additional data for attachment uploading
	add_filter( 'upload_post_params', [
		new Services\AvatarRegisterMetaService,
	    'setup'
	] );

	// Connect user and attachment via their IDs as meta data
	add_filter( 'wp_generate_attachment_metadata', [
		new Services\AvatarAddMetaService( $key ),
	    'setup'
	] );

	// Remove the user meta data when the attachment gets deleted
	add_filter( 'delete_attachment', [
		new Services\AvatarDeleteMetaService( $key ),
	    'setup'
	] );

	// Remove the attachment post when the user gets deleted
	add_filter( 'delete_user', [
		new Services\AvatarDeleteService( $key ),
	    'setup'
	] );

	// Save files when using the "Browser Uploader"
	foreach ( [ 'load-profile.php', 'load-user-edit.php', ] as $filter )
	{
		add_filter( $filter, [
			new Services\AvatarBrowserUploaderSaveService( $key ),
		    'setup'
		] );
	}

	// Allow registering Underscore Templates using the WP Dependency API
	add_filter( 'script_loader_tag', [
		new Services\UnderscoreTemplateScripts,
	    'setup'
	] );

	// Ajax Model
	$ajax = new Models\AjaxAware(
		$key,
		"{$key}_nonce"
	);

	// Register the AJAX handling scripts, localize the needed data
	add_filter( 'admin_enqueue_scripts', [
		new Services\AvatarScriptsService(
			$ajax,
			$key,
			plugin_dir_url( __FILE__ ),
			plugin_dir_path( __FILE__ )
		),
	    'setup'
	] );

	// Register the AJAX handler/callback to fetch/delete Attachments
	// using the "Multi-File Uploader"
	add_filter( "wp_ajax_{$ajax->getAction()}", [
		new Services\AvatarFetchDeleteAjaxService( $ajax ),
	    'setup'
	] );

	// Adds either the drag & drop uploader for the user avatar or the avatar itself
	add_filter( 'all_admin_notices', [
		new Views\UploadView(
			new Templates\AvatarUploadTemplate( $key ),
			new Templates\AvatarDisplayTemplate,
			$key
		),
	    'setup'
	] );

	// Target for the Avatar Backbone template
	add_action( 'all_admin_notices', function() use ( $key )
	{
		if ( in_array(
			get_current_screen()->base,
			[ 'profile', 'user-edit', ]
		) )
			echo '<div id="tmpl-main--container"></div>';
	}, 20 );


	// Limit upload size of image to 256 * 512 = 128 kB
	add_filter( 'upload_size_limit', [
		new Services\AvatarUploadSizeLimit(
			'manage_options',
			256 * 512,
			[ 'profile', 'user-edit', 'media', 'upload', ]
		),
	    'setup'
	] );


	// Limit allowed MIME types for image uploads
	add_filter( 'upload_mimes', [
		new Services\AvatarMIMELimitService(
			'manage_options',
			[ 'jpg|jpeg|jpe', 'gif', 'png', 'webp', ],
			[ 'profile', 'user-edit', 'media', 'upload', ]
		),
		'setup'
	] );


# Sizes
# 32 Admin User List Table
	// Limit width/height of uploaded images
	add_filter( 'wp_handle_upload_prefilter', [
		new Services\AvatarDimensionLimitService(),
	    'setup'
	] );


	// Add Image to User Columns
	// Use the filter to enable this feature
	if ( apply_filters( 'wcm.avatar.enable_custom_column', NULL ) )
	{
		add_filter( 'manage_users_columns', [
			new Services\AvatarUserColumnService( $key ),
			'setup'
		] );
	}

	// Replace "Gravatar"-Avatar with custom attachment in User Column
	// Use the filter to disable this feature
	if ( apply_filters( 'wcm.avatar.enable_custom_avatar', TRUE ) )
	{
		add_filter( 'pre_get_avatar', [
			new Services\AvatarReplacementService( $key ),
			'setup'
		], 20, 3 );
	}


	// Register stylesheet for Icons and other UI integration
	add_filter( 'admin_enqueue_scripts', [
		new Services\AdminStylesService( __FILE__ ),
	    'setup'
	] );
} );