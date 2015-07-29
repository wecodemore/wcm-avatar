<?php

namespace WCM\User\Avatar\Models;

/**
 * Class Meta
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class Meta implements MetaAwareInterface
{

	/** @var int */
	private $uid;

	/** @var mixed */
	private $default;

	/** @var string */
	private $key, $label = '', $icon = '', $desc = '';


	public function setID( $uid )
	{
		$this->uid = $uid;

		return $this;
	}


	public function getID()
	{
		return $this->uid;
	}


	public function setKey( $key )
	{
		$this->key = $key;

		return $this;
	}


	public function getKey()
	{
		return $this->key;
	}


	public function setDefault( $default )
	{
		$this->default = $default;

		return $this;
	}


	public function getDefault()
	{
		return $this->default;
	}


	public function getValue()
	{
		return get_user_meta( $this->uid, $this->getKey(), TRUE );
	}


	public function setLabel( $label )
	{
		$this->label = $label;

		return $this;
	}


	public function getLabel()
	{
		return $this->label;
	}


	public function setIcon( $icon )
	{
		$this->icon = $icon;

		return $this;
	}


	public function getIcon()
	{
		return $this->icon;
	}


	public function setDescription( $desc )
	{
		$this->desc = $desc;

		return $this;
	}


	public function getDescription()
	{
		return $this->desc;
	}
}