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
 * Class AvatarMIMELimitService
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarMIMELimitService implements ServiceInterface
{
	/** @var string */
	private $cap;

	/** @var array */
	private $allowed;

	/** @var array */
	private $where = [ ];


	/**
	 * @param string $cap     Required capability to bypass the restriction
	 * @param array  $allowed Allowed MIME Types
	 * @param array  $where   Screens where to apply the limit; get_current_screen()->base
	 */
	public function __construct( $cap = '', Array $allowed = [ ], Array $where = [ ] )
	{
		$this->cap     = $cap;
		$this->allowed = $allowed;
		$this->where   = $where;
	}


	/**
	 * Filters the list of allowed MIME types
	 * IANA is still missing the 'webp' format, so we add the unofficial type here.
	 *
	 * @link http://www.iana.org/assignments/media-types/media-types.xhtml
	 *       To implement WebP, use 'webp'
	 * @param array $mimes
	 * @param int|\WP_User|null $user User ID, User object or null
	 *                                if not provided (indicates current user).
	 * @return array
	 */
	public function setup( Array $mimes = [ ], $user = null )
	{
		$mimes['webp'] = 'image/webp';

		if (
			empty( $this->where )
			or current_user_can( $this->cap )
			or (
				is_admin()
				&& ! in_array( get_current_screen()->base, $this->where )
			)
		)
			return $mimes;

		$allowed = [ ];
		foreach ( $this->allowed as $key )
			$allowed[$key] = $mimes[$key];

		return ! empty( $this->allowed )
			? $allowed
			: $mimes;
	}
}