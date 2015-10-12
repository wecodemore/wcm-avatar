<?php
/**
 * This file is part of the WCM Avatar package.
 *
 * © Franz Josef Kaiser / wecodemore
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCM\User\Avatar\Views;

use WCM\User\Avatar\Services\ServiceInterface;
use WCM\User\Avatar\Templates\DataAwareInterface;
use WCM\User\Avatar\Templates\PrintableInterface;
use WCM\User\Avatar\Templates\TemplateInterface;

/**
 * Class UploadView
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class UploadView implements ServiceInterface
{

	/** @type TemplateInterface | DataAwareInterface */
	private $uploader;

	/** @type PrintableInterface | DataAwareInterface */
	private $image;

	/** @var string */
	private $key;


	/**
	 * @param TemplateInterface $uploader
	 * @param DataAwareInterface $image
	 * @param string $key
	 */
	public function __construct(
		TemplateInterface $uploader,
		DataAwareInterface $image,
		$key
	) {
		$this->uploader = $uploader;
		$this->image    = $image;
		$this->key      = $key;
	}


	public function setup()
	{
		if (
			is_admin()
			&& (
				! current_user_can( 'upload_files' )
				or ( ! IS_PROFILE_PAGE && ! current_user_can( 'edit_users' ) )
				or ! in_array( get_current_screen()->base, [
					'profile',
					'user-edit',
				] )
			)
		)
			return;

		wp_enqueue_script( 'plupload-handlers' );

		add_action( 'pre-plupload-upload-ui', [
			$this,
			'attachURlCb'
		] );

		$this->uploader->display();

		if (
			isset( $_POST['html-upload'] )
			or isset( $_FILES['async-upload'] )
			or (
				isset( $_POST['html-upload'] )
				and 'Upload' === $_POST['html-upload']
				)
			)
		{
			check_admin_referer( 'media-form' );

			$att_id = get_user_meta( $GLOBALS['user_id'], $this->key, TRUE );

			$this->image->setData( absint( $att_id ) );
			echo $this->image;
		}
	}


	/**
	 * Wrapper to attach the needed admin_url filter as late as possible.
	 * Removes itself on the first run.
	 */
	public function attachURlCb()
	{
		remove_filter( current_filter(), [
			$this,
			__FUNCTION__
		] );
		add_filter( 'admin_url', [
			$this,
			'filterBrowserUploadLink'
		], 5, 3 );
	}


	/**
	 * Fix the "Try the browser-uploader"-link to point to profile.php.
	 * Removes itself on the first run to not crash other URls.
	 *
	 * @param string $url
	 * @param string $path
	 * @param int    $blog_id
	 * @return string
	 */
	public function filterBrowserUploadLink( $url, $path, $blog_id )
	{
		remove_filter( current_filter(), [
			$this,
			__FUNCTION__
		] );

		return str_replace(
			'media-new',
			IS_PROFILE_PAGE ? 'profile' : 'user-edit',
			$url
		);
	}
}