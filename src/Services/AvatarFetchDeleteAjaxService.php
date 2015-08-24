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

use WCM\User\Avatar\Models\AjaxAwareInterface;

/**
 * Class AvatarFetchDeleteAjaxService
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarFetchDeleteAjaxService implements ServiceInterface
{

	/** @var AjaxAwareInterface */
	private $data;


	/**
	 * @param AjaxAwareInterface $data
	 */
	public function __construct( AjaxAwareInterface $data )
	{
		$this->data = $data;
	}


	/**
	 * Register the AJAX handler/callback to fetch/delete Attachments
	 * using the "Multi-File Uploader"
	 */
	public function setup()
	{
		check_ajax_referer( $this->data->getNonce() );
		if ( ! isset( $_POST['task'] ) )
			wp_send_json_error( [ 'error' => 'notask', ] );

		$task = filter_var( $_POST['task'] );

		$att_id = absint( filter_var(
			$_POST['att_id'],
			FILTER_VALIDATE_INT,
			FILTER_NULL_ON_FAILURE
		) );

		if ( ! $att_id )
			wp_send_json_error( [ 'error' => $att_id ] );

		switch ( $task )
		{
			case 'fetch' :
				list( $src, $width, $height ) = wp_get_attachment_image_src( $att_id, 'full' );
				wp_send_json_success( [
					'width'  => $width,
					'height' => $height,
					'thumb'  => wp_get_attachment_image( $att_id ),
					'name'   => wp_basename( $src ),
					'link'   => esc_url( get_edit_post_link( $att_id ) ),
				] );
				break;

			case 'destroy' :
				/** @var bool|\WP_Post $result */
				$result = wp_delete_attachment( absint( $att_id ), TRUE );
				$result = $result instanceof \WP_Post
					? $result->ID
					: $result;
				wp_send_json_success( [ 'deleted' => $result, ] );
				break;
		}

		wp_send_json_error( [ 'view' => 'something went wrong' ] );
	}
}