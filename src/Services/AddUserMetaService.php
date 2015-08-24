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
 * Class AddUserMetaService
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AddUserMetaService implements ServiceInterface
{
	/** @var string */
	private $key;

	/** @var Callable */
	private $sanitizer;

	/** @var string */
	private $error;


	public function __construct( $key, Callable $sanitizer, $error = NULL )
	{
		$this->key       = $key;
		$this->sanitizer = $sanitizer;
		$this->error     = $error;
	}


	/**
	 * Adds/ Deletes user meta data
	 * Sanitizes the provided data
	 *
	 * Important: The $result values are completely useless for
	 * custom error messages as the return value is mostly 'false'
	 *
	 * @param int $user_id
	 */
	public function setup( $user_id = 0 )
	{
		if ( ! isset( $_POST[ $this->key ] ) )
			return;

		$data = $_POST[ $this->key ];

		// Delete meta if deliberately emptied (deleted)
		if ( empty( $data ) )
		{
			$result = delete_user_meta( $user_id, $this->key );

			return;
		}

		// Sanitize data with a user defined & provided callback
		add_filter( "sanitize_user_meta_{$this->key}", $this->sanitizer, 10, 3 );

		$result = ! metadata_exists( 'user', $user_id, $this->key )
			? add_user_meta( $user_id, $this->key, $data, TRUE )
			: update_user_meta( $user_id, $this->key, $data );

		if ( ! is_int( $result ) )
			add_action( 'user_profile_update_errors', [ $this, 'addErrors' ], 10, 3 );
	}


	/**
	 * Add an error notice if something went really wrong.
	 * In nearly every case: This won't happen as our
	 * Callable/callback hopefully cleans everything up.
	 *
	 * @param \WP_Error $errors
	 * @param bool      $update
	 * @param \stdClass $user The \WP_User object + User contact methods
	 * @return \WP_Error
	 */
	public function addErrors( \WP_Error &$errors, $update, &$user )
	{
		$errors->add( 'invalid_meta', $this->error );

		return $errors;
	}
}