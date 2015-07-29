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