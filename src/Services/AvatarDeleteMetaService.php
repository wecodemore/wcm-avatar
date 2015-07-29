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
 * Class AvatarDeleteMetaService
 * Remove the user meta data when the attachment gets deleted
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarDeleteMetaService implements ServiceInterface
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
	 * Remove the User Meta Data when an Attachment gets removed
	 *
	 * @param int $att_id Attachment ID
	 */
	public function setup( $att_id = 0 )
	{
		$query = new \WP_User_Query( [
			'fields'     => 'ID',
			'meta_query' => [
				[
					'key'   => $this->key,
					'value' => $att_id,
				],
			],
		] );
		$query->query();
		$query->get_results();

		// Remove from user meta data
		foreach ( $query->get_results() as $user_id )
			delete_user_meta( $user_id, $this->key );
	}
}