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
		if (
			// "Multi-File Uploader"
			(
				isset( $_REQUEST['user_id'] )
				and isset( $_REQUEST['screen_id'] )
				and in_array( $_REQUEST['screen_id'], [
					'profile',
					'user-edit',
				] )
			)
			// "Browser Uploader"
			or (
				isset( $_REQUEST['html-upload'] )
				and 'Upload' === $_POST['html-upload']
				and in_array( get_current_screen()->base, [
					'profile',
					'user-edit',
				] )
				and isset( $_REQUEST['user_id'] )
			)
		)
		{
			$user_id = absint( filter_var( $_REQUEST['user_id'], FILTER_VALIDATE_INT ) );

			// Attach attachment ID to user meta as single entry (querying allowed)
			update_user_meta( $user_id, $this->key, $att_id );

			// Attach user to attachment (single meta entry to allow querying)
			add_post_meta( $att_id, 'user_id', $user_id, TRUE );
			// Or: Attach user to attachment meta data array
			$meta['user_id'] = $user_id;
		}

		return $meta;
	}
}