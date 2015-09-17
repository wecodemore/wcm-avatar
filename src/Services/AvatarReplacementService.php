<?php
/**
 * This file is part of the WCM Avatar package.
 *
 * © wecodemore / Franz Josef Kaiser
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCM\User\Avatar\Services;

/**
 * Class AvatarReplacementService
 *
 * @package WCM\User\Avatar\Services
 * @author  Franz Josef Kaiser <wecodemore@gmail.com>
 */
class AvatarReplacementService implements ServiceInterface
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
	 * @param string $html
	 * @param int    $user_id
	 * @param array  $args
	 * @return null|string Return NULL to abort and replace nothing
	 */
	public function setup( $html = '', $user_id = -1, Array $args = [ ] )
	{
		$att_id = absint( get_user_meta(
			$user_id,
			$this->key,
			TRUE
		) );
		if ( ! is_int( $att_id ) )
			return NULL;

		// Additional argument to pass the attachment image
		// size name to the filter
		$args['size_name']    = 'thumbnail';
		// High-Res Image size name
		$args['size_name_2x'] = 'medium';

		// Use this filter to customize the passed arguments
		$args = apply_filters( 'wcm.avatar.args', $args );

		$img = wp_get_attachment_image(
			$att_id,
			$args['size_name'],
			FALSE,
			[ $this->key => TRUE, ]
		);
		if ( empty( $img ) )
			return NULL;

		// High-Res 2× image Url
		$srcset = $this->getUrlHighres( $att_id, $args );

		$dom = new \DOMDocument;
		$dom->loadHTML( $img );

		/** @var \DOMNodeList $images */
		$images = $dom->getElementsByTagName( 'img' );
		/** @var \DOMElement $tag */
		$tag    = $images->item(0);

		// HTML img `alt`-attribute
		if ( ! empty( $alt ) )
		{
			$tag->setAttribute( 'alt', $args['alt'] );
		}

		// Highres Avatars
		if ( ! empty( $url2x ) )
		{
			$tag->setAttribute( 'srcset', esc_attr( "{$srcset} 2x" ) );
		}

		$tag->setAttribute(
			'class',
			$this->getClasses( $args )
		);

		// Assign extra attributes
		if (
			isset( $args['extra_attr'] )
			and ! empty( $args['extra_attr'] )
			)
		{
			$extra = $args['extra_attr'];
			if ( ! is_array( $extra ) )
				$extra = [ $extra, ];

			foreach ( $extra as $attr => $value )
			{
				$tag->setAttribute(
					$attr,
					esc_attr( $value )
				);
			}
		}

		$tag->setAttribute( 'width', $args['width'] );
		$tag->setAttribute( 'height', $args['height'] );

		$html = $dom->saveHTML( $tag );

		return $this->getDefaultMarkUp()
			? $html
			: sprintf(
				'<a href="%s">%s</a>',
				get_edit_post_link( $att_id ),
				$html
			);
	}


	/**
	 * @param int   $att_id
	 * @param array $args
	 * @return string
	 */
	protected function getUrlHighres( $att_id, Array $args = [ ] )
	{
		$src = wp_get_attachment_image_src(
			$att_id,
			$args['size_name_2x']
		);

		if ( ! $src )
			return '';

		list ( $url, $height, $width, $crop ) = $src;

		$url = filter_var( $url, FILTER_SANITIZE_URL );
		if ( ! $url )
			return '';

		// Only use the size in case it's really min. 2× the original size
		return (
			$height > ( $args['height'] *2 )
			and $width > ( $args['width'] *2 )
		)
			? $url
			: '';
	}


	/**
	 * @param array $args
	 * @return string
	 */
	protected function getClasses( Array $args = [ ] )
	{
		if (
			! isset( $args['class'] )
			or empty( $args['class'] )
		)
			return '';

		$size = isset( $args['size'] )
			? 'avatar-'.absint( filter_var(
				$args['size'],
				FILTER_SANITIZE_NUMBER_INT
			) )
			: '';

		$classes = array_merge( $args['class'], [
			'photo',
			'avatar',
		    $size
		] );

		return join(
			"  ",
			array_filter( $classes )
		);
	}


	/**
	 * Get the default <img> tag?
	 * @return bool
	 */
	private function getDefaultMarkUp()
	{
		// Preserve the Admin Bar Avatar Layout
		if (
			is_admin_bar_showing()
			&& ! did_action( 'wp_after_admin_bar_render' )
		)
			return true;

		if ( is_admin() )
			return false;

		return true;
	}
}