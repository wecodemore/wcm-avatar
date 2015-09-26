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
 * Class AvatarDimensionLimitService
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarDimensionLimitService implements ServiceInterface
{

	/**
	 * @param array $file
	 * @return string
	 */
	public function setup( Array $file = [ ] )
	{
		// do not affect other screens
		$screen = filter_input( INPUT_POST, 'screen_id', FILTER_SANITIZE_STRING );
		if ( ! in_array( $screen, [ 'profile', 'user-edit' ], TRUE ) )
		{
			return $file;
		}

		$data = getimagesize( $file['tmp_name'] );
		// "Handle" cases where we can't get any info:
		if ( ! $data )
			return $file;

		list( $width, $height, $type, $hwstring ) = $data;
		$mime = image_type_to_mime_type( $type );

		// Flash uploader
		/*if (
			'application/octet-stream' === $file['type']
			and isset( $file['tmp_name'] )
			)
		{

		}*/

		// Maximum
		$max = absint( filter_var( apply_filters(
			'wcm.avatar.size.max',
			1024
		), FILTER_SANITIZE_NUMBER_INT ) );

		// allow no limit by passing 0 as limit
		if ( $max < 1 )
		{
			return $file;
		}

		if ( $max < max( $width, $height ) )
		{
			$file['error'] = sprintf( _x(
				'Image exceeds maximum size of %spx × %spx',
				'%s are the maximum pixels',
				'clients_domain'
			), number_format_i18n( $max ), number_format_i18n( $max ) );
		}

		// Minimum
		$min = absint( filter_var( apply_filters(
			'wcm.avatar.size.min',
			32
		), FILTER_SANITIZE_NUMBER_INT ) );

		// The larger size should not be below the requested min size
		if ( $min > max( $width, $height ) )
		{
			$file['error'] = sprintf( _x(
				'Image does not reach the needed minimum size of %spx × %spx',
				'%s are the minimum pixels',
				'clients_domain'
			), number_format_i18n( $min ), number_format_i18n( $min ) );
		}

		/*$magick = new \Imagick( $file['tmp_name'] );
		$mdata = $magick->identifyImage();
		$ppi = $mdata['resolution'];
		$file['error'] = join( ' | ', array_keys( $ppi ) );*/

		return $file;
	}
}