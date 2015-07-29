<?php
/**
 * This file is part of the WCM Avatar package.
 *
 * © Franz Josef Kaiser / wecodemore
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCM\User\Avatar\Services;

/**
 * Class AvatarBrowserUploaderSaveService
 * Save files when using the "Browser Uploader"
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarBrowserUploaderSaveService implements ServiceInterface
{

	/** @var string */
	private $key;

	/** @var \WP_Error */
	private $error = [ ];


	/**
	 * @param string $key
	 */
	public function __construct( $key )
	{
		$this->key = $key;
	}


	/**
	 * @param int $user_id
	 */
	public function setup( $user_id = 0 )
	{
		if (
			! isset( $_POST['html-upload'] )
			or ! isset( $_FILES['async-upload'] )
			or (
				isset( $_POST['html-upload'] )
				and 'Upload' !== $_POST['html-upload']
			)
		)
			return;

		check_admin_referer( 'media-form' );

		$file = $_FILES['async-upload'];
		if ( ! is_array( $file ) )
			return;

		/*$location = admin_url( sprintf(
			'%s.php',
			get_current_screen()->base
		) );

		if (
			( defined( 'IS_PROFILE_PAGE' ) and ! IS_PROFILE_PAGE )
			or 'user-edit' === get_current_screen()->base
		)
			$location = add_query_arg(
				'user_id',
				absint( $_REQUEST['user_id'] ),
				$location
			);*/

		$result = media_handle_upload( 'async-upload', $post_id = - 1 );
		if ( is_wp_error( $result ) )
		{
			$this->error[] = $result;
			add_filter( "{$this->key}_upload_errors", [
				$this,
				'addError',
			], 10, 1 );
		}

		# Custom Errors: {$action}_prefilter > $file > return $file['error'] = ''; > returns [ 'error' => $message, ]
		#if ( is_array( $result ) )
		#	var_dump( $result['file'], $result['url'], $result['type'] );
		# > Filter: inside wp_insert_attachment() > wp_insert_post() > apply_filter( 'wp_insert_post_parent', ...
		# check_upload_size( $file ) > hooked to the 'wp_handle_upload_prefilter' filter (where is that?)
	}


	/**
	 * Uses a custom filter as argument for media_upload_form( $errors );
	 * to pass the errors to the template.
	 * Most elegant way I could figure out.
	 *
	 * @param array $errors
	 * @return string
	 */
	public function addError( Array $errors )
	{
		if ( ! empty( $this->error ) )
			$errors['upload_error'] = $this->error;

		return $errors;
	}
}