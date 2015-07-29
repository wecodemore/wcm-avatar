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
 * Class AvatarDeleteService
 * Remove the attachment post when the user gets deleted
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarDeleteService implements ServiceInterface
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
	 * Delete the attachment when the user gets deleted
	 *
	 * @param int      $user_id
	 * @param int|null $reassign
	 */
	public function setup( $user_id = 0, $reassign = NULL )
	{
		// Debugging
		# $user = get_user_by( 'id', absint( $id ) );
		$att_id = get_user_meta( absint( $user_id ), $this->key, TRUE );
		$result = wp_delete_attachment( absint( $att_id ), TRUE );
		# var_dump( $result );
	}
}