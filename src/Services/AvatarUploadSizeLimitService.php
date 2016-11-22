<?php


namespace WCM\User\Avatar\Services;


class AvatarUploadSizeLimitService implements ServiceInterface {

	/** @var int */
	private $limit;

	/**
	 * @param int    $limit
	 */
	public function __construct( $limit = 0 )
	{
		$this->limit = absint( $limit );
	}

	/**
	 * @param array $file
	 * @return array
	 */
	public function setup( Array $file = [ ] )
	{

		$screen = filter_input( INPUT_POST, 'screen_id', FILTER_SANITIZE_STRING );
		if (
			! in_array( $screen, [ 'profile', 'user-edit' ], TRUE )
			&&
			! in_array( get_current_screen()->base, [ 'profile', 'user-edit' ], TRUE )
		)
		{
			return $file;
		}

		if ( $file['size'] > $this->limit )
		{
			$file['error'] = sprintf( __('%s exceeds the maximum upload size for this site.'), esc_html( $file['name'] ) );
		}

		return $file;
	}

}