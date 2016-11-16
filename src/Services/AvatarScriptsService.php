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

use WCM\User\Avatar\Models;

/**
 * Class AvatarScriptsService
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarScriptsService implements ServiceInterface
{

	/** @var Models\AjaxAwareInterface */
	private $ajax;

	/** @var string */
	private $key;

    /** @var \WCM\User\Avatar\Models\UnderscoreTemplateEnqueuer */
	private $templateEnqueuer;


	/**
	 * @param \WCM\User\Avatar\Models\AjaxAwareInterface $ajax
	 * @param string                                     $key
	 * @param Models\UnderscoreTemplateEnqueuer                  $enqueuer
	 */
	public function __construct(
	    Models\AjaxAwareInterface $ajax,
        $key,
        Models\UnderscoreTemplateEnqueuer $enqueuer)
	{
		$this->ajax = $ajax;
		$this->key  = $key;
		$this->templateEnqueuer = $enqueuer;
	}


	public function setup( $screen = '' )
	{
		if ( ! in_array( $screen, [
			'profile.php',
			'user-edit.php',
		] ) )
			return;

		$ext = $this->getExtension();

		$this->addTemplates();

		$url  = $this->templateEnqueuer->baseUrl().'assets/src/logo';
		$path = $this->templateEnqueuer->basePath().'assets/src/logo';

		( defined( 'CONCATENATE_SCRIPTS' ) and CONCATENATE_SCRIPTS )
			? $this->addScriptsConcat( $url, $path, $ext )
			: $this->addScripts( $url, $path, $ext );

		$att_id = $this->getAttachmentID();

		$colors = $this->getColors();
		$meta   = $this->getMeta( $att_id );

		$name = ( defined( 'CONCATENATE_SCRIPTS' ) and CONCATENATE_SCRIPTS )
			? 'logo-upload'
			: 'logo-models';

		wp_localize_script( $name, 'logoUploader', [
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
			'action'      => $this->ajax->getAction(),
			'_ajax_nonce' => wp_create_nonce( $this->ajax->getNonce() ),
			'templates'   => [
				'logo'    => '#tmpl-logo',
				'thumb'   => '#tmpl-thumb',
				'delete'  => '#tmpl-delete',
				'caption' => '#tmpl-caption',
			],
			'container'   => [
				'main'     => 'tmpl-main--container',
				'thumb'    => 'tmpl-thumb--container',
				'delete'   => 'tmpl-delete--container',
				'caption'  => 'tmpl-caption--container',
				'uploader' => 'tmpl-uploader--container',
			],
			'data'        => $this->getData( $att_id, $colors, $meta ),
			'l10n'        => $this->getl10n( $att_id ),
		] );
	}


	public function addTemplates()
	{
		$templates = [
			'logo',
			'thumb',
			'delete',
			'caption',
		];

        array_walk( $templates, [ $this->templateEnqueuer, 'enqueue' ] );
	}


	/**
	 * @param string $url
	 * @param string $path
	 * @param string $ext
	 */
	public function addScriptsConcat( $url, $path, $ext )
	{
		wp_enqueue_script(
			'logo-upload',
			"{$url}/all{$ext}.js",
			[
				'jquery',
				'plupload-handlers',
				'underscore',
				'backbone',
			],
			filemtime( "{$path}/all.min.js" ),
			TRUE
		);
	}


	/**
	 * @param string $url
	 * @param string $path
	 * @param string $ext
	 */
	public function addScripts( $url, $path, $ext )
	{
		wp_enqueue_script(
			'logo-models',
			"{$url}/models.js",
			[
				'jquery',
				'plupload-handlers',
				'underscore',
				'backbone',
			],
			filemtime( "{$path}/models{$ext}.js" ),
			TRUE
		);
		wp_enqueue_script(
			'logo-views',
			"{$url}/views.js",
			[ 'logo-models', ],
			filemtime( "{$path}/views{$ext}.js" ),
			TRUE
		);
		wp_enqueue_script(
			'logo-controller',
			"{$url}/controller.js",
			[ 'logo-views', ],
			filemtime( "{$path}/controller{$ext}.js" ),
			TRUE
		);
	}


	/**
	 * @return string
	 */
	public function getExtension()
	{
		return (
			! defined( 'SCRIPT_DEBUG' )
			xor ( defined( 'SCRIPT_DEBUG' ) and ! SCRIPT_DEBUG )
			&& ( defined( 'COMPRESS_SCRIPTS' ) and COMPRESS_SCRIPTS )
		)
			? '.min'
			: '';
	}


	/**
	 * @return int
	 */
	public function getAttachmentID()
	{
		$att_id = get_user_meta( $GLOBALS['user_id'], $this->key, TRUE );
		FALSE !== $att_id and $att_id = absint( $att_id );

		return $att_id;
	}


	public function getMeta( $att_id )
	{
		return is_int( $att_id )
			? wp_get_attachment_metadata( $att_id )
			: [ ];
	}


	/**
	 * Adjust the Color scheme
	 * @return array
	 */
	public function getColors()
	{
		$scheme = get_user_meta( get_current_user_id(), 'admin_color', TRUE );
		! $scheme and $scheme = 'fresh';

		return $GLOBALS['_wp_admin_css_colors'][$scheme]->colors;
	}


	/**
	 * @param int $att_id
	 * @return array
	 */
	public function getl10n( $att_id )
	{
		return [
			'edit'    => is_int( $att_id )
				? _x( 'Edit', 'media item' )
				: '',
			'nothumb' => ! is_null( $att_id )
				? _x( 'No Logo', 'media item' )
				: '',
		];
	}


	/**
	 * @param int $att_id
	 * @param     $colors
	 * @param     $meta
	 * @return array
	 */
	public function getData( $att_id, $colors, $meta )
	{
		return [
			'att_id'  => $att_id,
			'name'    => is_int( $att_id )
				? wp_basename( wp_get_attachment_url( $att_id ) )
				: '',
			'thumb'   => is_int( $att_id )
				? wp_get_attachment_image( $att_id )
				: '',
			'width'   => isset( $meta['width'] )
				? $meta['width']
				: 0,
			'height'  => isset( $meta['height'] )
				? $meta['height']
				: 0,
			'link'    => is_int( $att_id )
				? esc_url( get_edit_post_link( $att_id ) )
				: '#',
			'color1'  => $colors[1],
			'color2'  => $colors[3],
			'loading' => site_url( 'wp-includes/images/spinner-2x.gif' ),
		];
	}
}