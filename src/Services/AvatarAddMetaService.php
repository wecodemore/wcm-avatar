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
 * Class AvatarAddMetaService
 * Connect user and attachment via their IDs as meta data
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarAddMetaService implements ServiceInterface
{

	/** @var string */
	private $key;


	/**
	 * @param string $key
	 */
	public function __construct( $key )
	{
		$this->key = $key;
	}


	/**
	 * @param array $meta
	 * @param int   $att_id
	 * @return array
	 */
	public function setup( Array $meta = [ ], $att_id = 0 )
	{
		if ( $this->isValidMultiUpload() or $this->isValidBrowserUpload() ) {

			$user_id = $this->getUserID();

			// Attach attachment ID to user meta as single entry (querying allowed)
			update_user_meta(
				$user_id,
				$this->key,
				$att_id
			);

			// Attach user to attachment (single meta entry to allow querying)
			add_post_meta(
				$att_id,
				'user_id',
				$user_id,
				TRUE
			);

			// Or: Attach user to the attachment meta data array
			# $meta['user_id'] = $user_id;
		}

		return $meta;
	}

	/**
	 * Returns the User ID, either from the $_REQUEST (on other peoples Profile) or from logged in user (on own profile)
	 * @return int
	 */
	protected function getUserID(){

		if( isset( $_REQUEST['user_id'] ) ){
			$user_id = absint( filter_var(
				$_REQUEST['user_id'],
				FILTER_VALIDATE_INT
			) );
		}else{
			$user_id = get_current_user_id();
		}

		return $user_id;

	}

	/**
	 * @return bool
	 */
	protected function isValidMultiUpload(){
		return  isset( $_REQUEST['user_id'] )
		        and isset( $_REQUEST['screen_id'] )
				and in_array( $_REQUEST['screen_id'], [
					'profile',
					'user-edit',
				]);
	}

	/**
	 * @return bool
	 */
	protected function isValidBrowserUpload(){
		return  isset( $_REQUEST['html-upload'] )
				and __( 'Upload' ) === $_POST['html-upload']
				and (
						(
						'user-edit' === get_current_screen()->base
						&&
						isset( $_REQUEST['user_id'] )
						)
						or
						'profile' === get_current_screen()->base
				);
	}

}