<?php

namespace WCM\User\Avatar\Models;

/**
 * Interface AffixAwareInterface
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
interface AffixAwareInterface extends MetaAwareInterface
{
	/**
	 * @param string $prefix
	 */
	public function setPrefix( $prefix );


	public function getPrefix();


	/**
	 * @param string $suffix
	 */
	public function setSuffix( $suffix );


	public function getSuffix();
}