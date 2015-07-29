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
 * Class AvatarUploadSizeLimit
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarUploadSizeLimit implements ServiceInterface
{

	/** @var string */
	private $cap;

	/** @var int */
	private $limit;

	/** @var Array */
	private $bases;


	/**
	 * @param string $cap
	 * @param int    $limit
	 * @param array  $bases
	 */
	public function __construct( $cap = '', $limit = 0, Array $bases = [ ] )
	{
		$this->cap   = $cap;
		$this->limit = absint( $limit );
		$this->bases = $bases;
	}


	/**
	 * @param int $limit
	 * @param int $u_bytes
	 * @param int $p_bytes
	 * @return int
	 */
	public function setup( $limit = 0, $u_bytes = 0, $p_bytes = 0 )
	{
		return (
			in_array( get_current_screen()->base, $this->bases )
			and ! current_user_can( $this->cap )
		)
			? $this->limit
			: $limit;
	}
}