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
 * Interface MetaAwareInterface
 *
 * @package WCM\User\Avatar\Models
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
interface MetaAwareInterface
{

	/**
	 * @param string $uid
	 */
	public function setID( $uid );


	public function getID();


	/**
	 * @param string $key
	 */
	public function setKey( $key );


	public function getKey();


	/**
	 * @param mixed $default
	 */
	public function setDefault( $default );


	public function getDefault();


	public function getValue();


	/**
	 * @param string $label
	 */
	public function setLabel( $label );


	public function getLabel();


	/**
	 * @param stream $icon
	 */
	public function setIcon( $icon );


	public function getIcon();


	/**
	 * @param string $desc
	 */
	public function setDescription( $desc );


	public function getDescription();
}