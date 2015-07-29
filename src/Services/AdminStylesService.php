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
 * Class AdminStylesService
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AdminStylesService implements ServiceInterface
{

	/** @var string */
	private $root;


	public function __construct( $root )
	{
		$this->root = $root;
		add_action( 'admin_head', [
			$this,
			'dnsPrefetch'
		] );
	}


	/**
	 * @param string $screen
	 */
	public function setup( $screen = '' )
	{
		if ( ! $this->isAllowed( $screen ) )
			return;

		$file = sprintf( 'assets/style%s.css', $this->getExtension() );
		wp_enqueue_style(
			'company',
			plugin_dir_url( $this->root ) . $file,
			[ ],
			@filemtime( plugin_dir_path( $this->root ) . $file )
		);
		wp_enqueue_style(
			'devicons',
			'//cdn.jsdelivr.net/devicons/1.7.0/css/devicons.min.css',
			[ ],
			'1.7.0'
		);
	}


	/**
	 * @param $screen
	 * @return bool
	 */
	private function isAllowed( $screen )
	{
		return in_array( $screen, [
			'profile.php',
			'user-edit.php',
		] );
	}


	/**
	 * DNS Prefetch for faster lookups
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Controlling_DNS_prefetching
	 */
	public function dnsPrefetch()
	{
		?><link rel="dns-prefetch" href="//cdn.jsdelivr.net"><?php
	}


	/**
	 * @return string
	 */
	private function getExtension()
	{
		return ( defined( 'COMPRESS_CSS' ) and COMPRESS_CSS )
			? '.min'
			: '';
	}
}