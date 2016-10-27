<?php


namespace WCM\User\Avatar\Services;


class AvatarExistsLimitService implements ServiceInterface {

	/** @var string */
	private $key;


	/**
	 * @param string $key
	 */
	public function __construct( $key )
	{
		$this->key = $key;
	}

	/**
	 * @param array $file
	 * @return string
	 */
	public function setup( Array $file = [ ] )
	{

		$screen = filter_input( INPUT_POST, 'screen_id', FILTER_SANITIZE_STRING );
		if ( ! in_array( $screen, [ 'profile', 'user-edit' ], TRUE ) )
		{
			return $file;
		}

		$user_id = filter_input( INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT );

		if( false === get_userdata($user_id) ){
			$file['error'] = __( 'Cannot find user.', 'wcmavatar' );
			return $file;
		}

		if( $this->userHasAvatar( $user_id ) ){
			$file['error'] = __( 'This user already has an avatar set. To upload a new avatar delete the old one first.', 'wcmavatar' );
			return $file;
		}

		return $file;
	}

	/**
	 * @param int $user_id
	 *
	 * @return bool
	 */
	protected function userHasAvatar( $user_id ){

		$avatar = get_user_meta( $user_id, $this->key, true );

		return !!$avatar;

	}

}