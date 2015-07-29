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
 * Class AvatarUserColumnService
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarUserColumnService implements ServiceInterface
{

	/** @type string key */
	private $key;


	/**
	 * @param string $key
	 */
	public function __construct( $key )
	{
		$this->key = $key;
	}


	/**
	 * Add the attachment col right after the "Username" col in the `users.php` WP list table
	 *
	 * @param array $columns
	 * @return array
	 */
	public function setup( Array $columns = [ ] )
	{
		$end = array_slice( $columns, 2 );
		$start = array_diff( $columns, $end );

		add_action( 'manage_users_custom_column', [
			$this,
			'getContent'
		], 10, 3 );

		return $start
			+ [ $this->key => __( 'Logo', 'company_domain' ), ]
			+ $end;
	}


	/**
	 * Add data to our user column,
	 * replace width/height to match "pinkynail" Gravatar size
	 * Link image to media/attachment edit screen
	 *
	 * @param string $value
	 * @param string $col_name
	 * @param int    $user_id
	 * @return string
	 */
	public function getContent( $value, $col_name, $user_id )
	{
		if ( $this->key !== $col_name )
			return $value;

		$att_id = absint( get_user_meta( $user_id, $this->key, TRUE ) );
		if ( ! is_int( $att_id ) )
			return $value;

		$img = wp_get_attachment_image(
			$att_id,
			'thumbnail',
			FALSE,
			[ $this->key => TRUE, ]
		);
		if ( empty( $img ) )
			return $value;

		$dom = new \DOMDocument;
		$dom->loadHTML( $img );

		/** @var \DOMElement $tag */
		$images = $dom->getElementsByTagName( 'img' );
		foreach ( $images as $tag )
		{
			$tag->setAttribute( 'width', 32 );
			$tag->setAttribute( 'height', 32 );

			return sprintf(
				'<a href="%s">%s</a>',
				get_edit_post_link( $att_id ),
				$dom->saveHTML( $tag )
			);
		}

		return $value;
	}
}