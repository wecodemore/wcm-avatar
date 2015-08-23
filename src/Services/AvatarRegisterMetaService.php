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
 * Class AvatarRegisterMetaService
 * Register the 'user_id' and 'screen_id'
 * as additional data for attachment uploading
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarRegisterMetaService implements ServiceInterface
{
	/**
	 * Attach the 'user_id' and the current 'screen_id' as params
	 * Allows easy identification of the current screen and user
	 *
	 * @param array $params
	 * @return array
	 */
	public function setup( Array $params = [ ] )
	{
		# If this produces errors, narrow it down:
		# if (
		#   'media' === get_current_screen()->base
		#    and 'add' === get_current_screen()->action
		# )
		#   return $params;

		$user_id = get_current_user_id();

		$params['screen_id'] = get_current_screen()->id;
		$params['user_id']   = isset( $user_id )
			? $user_id
			: $GLOBALS['user_id'];

		return $params;
	}
}