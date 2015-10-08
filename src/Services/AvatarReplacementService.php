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
	 * @param string     $html
	 * @param int|string $id_or_email
	 * @param array      $args
	 * @return null|string Return NULL to abort and replace nothing
	 */
	public function setup( $html = '', $id_or_email = -1, Array $args = [ ] )
	{
		$default = get_option( 'avatar_default', 'mystery' );

		// Don't force display
		if ( ! $args['force_display'] && ! get_option( 'show_avatars' ) )
			return $default;

		/** @var \WP_User|bool $user */
		$user = is_numeric( $id_or_email )
			? get_userdata( $id_or_email )
			: get_user_by( 'email', $id_or_email );

		if ( ! $user instanceof \WP_User )
			return $default;

		$att_id = get_user_meta(
			$id_or_email,
			$this->key,
			TRUE
		);

		if ( ! $att_id )
			return $default;

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
			FALSE
		);
		if ( empty( $img ) )
			return $default;

		$dom = new \DOMDocument;
		$dom->loadHTML( $img );

		/** @var \DOMNodeList $images */
		$images = $dom->getElementsByTagName( 'img' );
		/** @var \DOMElement $tag */
		$tag    = $images->item(0);

		// HTML img `alt`-attribute
		if ( isset( $args['alt'] ) )
			$tag->setAttribute( 'alt', esc_attr( $args['alt'] ) );

		// High-res avatar image
		if ( ( $srcset = $this->getUrlHighres( $att_id, $args ) ) )
			$tag->setAttribute( 'srcset', esc_attr( "{$srcset} 2x" ) );

		// HTML classes
		$classes = explode( ' ', $tag->getAttribute('class') );
		array_walk( $classes, function(&$class) {
			$class = trim( str_replace( 'attachment-', 'avatar-', $class ) );
		});
		$tag->setAttribute( 'class', $this->getClasses( $args, $classes ) );

		// Extra attributes
		$extra = isset( $args['extra_attr'] )
			? (array) $args['extra_attr']
			: [];

		foreach ( (array) $extra as $attr => $value ) {
			if ( is_string( $attr ) && is_string( $value ) ) {
				$tag->setAttribute( $attr, esc_attr( $value ) );
			}
		}

		$tag->setAttribute( 'width', $args['width'] );
		$tag->setAttribute( 'height', $args['height'] );

		return $dom->saveHTML( $tag );
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
			$height >= ( $args['height'] * 2 )
			and $width >= ( $args['width'] * 2 )
		)
			? $url
			: '';
	}

	/**
	 * @param array $args
	 * @param array $imgClasses
	 * @return string
	 */
	protected function getClasses( Array $args = [ ], Array $imgClasses = [] )
	{
		$baseClasses = [
			'photo',
			'avatar',
		];

		empty( $args['size'] ) || $baseClasses[] = 'avatar-' . $args['size'];

		$classArg = isset( $args['class'] ) ? (array) $args['class'] : [];

		$classes = array_merge( $classArg, $imgClasses, $baseClasses );

		// Use this filter to customize the avatar classes
		$classes = apply_filters( 'wcm.avatar.classes', $classes );

		$class = join( ' ', array_unique( $classes ) );

		return trim( str_replace( '  ', ' ', $class ) );
	}
}