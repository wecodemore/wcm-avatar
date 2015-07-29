<?php

namespace WCM\User\Avatar\Models;

/**
 * Interface MetaAwareInterface
 * @package WCM\User\Avatar\Models
 */
interface MetaAwareInterface
{
	/**
	 * @param $uid
	 */
	public function setID( $uid );

	public function getID();

	public function setKey( $key );

	public function getKey();

	public function setDefault( $default );

	public function getDefault();

	public function getValue();

	public function setLabel( $label );

	public function getLabel();

	public function setIcon( $icon );

	public function getIcon();

	public function setDescription( $desc );

	public function getDescription();
}