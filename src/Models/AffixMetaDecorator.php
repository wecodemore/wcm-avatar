<?php

namespace WCM\User\Avatar\Models;

/**
 * Class AffixMetaDecorator
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AffixMetaDecorator extends Meta implements AffixAwareInterface
{

	/** @var string */
	private $prefix = '';

	/** @var string */
	private $suffix = '';


	/**
	 * @param $prefix
	 */
	public function setPrefix( $prefix )
	{
		$this->prefix = $prefix;
	}


	/**
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}


	/**
	 * @param string $suffix
	 */
	public function setSuffix( $suffix )
	{
		$this->suffix = $suffix;
	}


	/**
	 * @return string
	 */
	public function getSuffix()
	{
		return $this->suffix;
	}
}