<?php
/**
 * This file is part of the WCM Avatar package.
 *
 * © Franz Josef Kaiser / wecodemore
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCM\User\Avatar\Models;

/**
 * Class AjaxAware
 * Helper Object to share an action and a nonce name with Services
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AjaxAware implements AjaxAwareInterface
{

	/** @var string */
	private $action, $nonce;


	/**
	 * @param string $action
	 * @param string $nonce
	 */
	public function __construct( $action, $nonce )
	{
		$this->action = $action;
		$this->nonce  = $nonce;
	}


	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}


	/**
	 * @return string
	 */
	public function getNonce()
	{
		return $this->nonce;
	}
}