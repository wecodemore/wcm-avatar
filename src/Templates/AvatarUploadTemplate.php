<?php
/**
 * This file is part of the WCM Avatar package.
 *
 * © Franz Josef Kaiser / wecodemore
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCM\User\Avatar\Templates;

/**
 * Class AvatarUploadTemplate
 *
 * @package WCM\User\Avatar\Templates
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarUploadTemplate implements TemplateInterface
{

	/** @var string key */
	private $key;


	/**
	 * @param $key
	 */
	public function __construct( $key )
	{
		$this->key = $key;
	}


	/**
	 * Important: The nonce name has to be 'media-form', else async-upload.php will throw.
	 */
	public function display()
	{
		$form_class = $this->getFormClass();

		$att_id = get_user_meta( $GLOBALS['user_id'], $this->key, TRUE );
		FALSE !== $att_id
			and $att_id = absint( $att_id );

		$hidden = is_int( $att_id ) and 0 !== $att_id ? 'hidden' : '';

		$action = $this->getAction();

		?>
<div class="wrap">
	<h2><!--
		Placeholder so the f***ing WP JavaScript error/success msg
		gets inserted here and not *below* the title.
	--></h2>
	<h2><?php echo esc_html( __( 'Logo', 'company_domain' ) ); ?></h2>
	<form enctype="multipart/form-data"
		  method="post"
		  action="<?php echo $action; ?>"
		  class="<?php echo esc_attr( $form_class ); ?>"
		  id="file-form">

		<div id="tmpl-uploader--container" class="<?php echo $hidden; ?>">
			<?php media_upload_form( apply_filters( "{$this->key}_upload_errors", [] ) ); ?>

			<script type="text/javascript">
				var post_id = -1, shortform = 3;
			</script>
			<?php wp_nonce_field( 'media-form' ); ?>
			<table class="form-table" id="company-upload-status">
				<tbody>
					<tr class="company-logo-upload-wrap">
						<th scope="row">
							<div id="media-items" class="hide-if-no-js"></div>
						</th>
					</tr>
				</tbody>
			</table>
		</div>
	</form>
</div>
<?php
	}


	public function getFormClass()
	{
		$form_class = 'media-upload-form type-form validate';
		if (
			get_user_setting( 'uploader' )
			or isset( $_GET['browser-uploader'] )
		)
			$form_class .= ' html-uploader';

		return $form_class;
	}


	public function getAction()
	{
		$action = admin_url( IS_PROFILE_PAGE ? 'profile.php' : 'user-edit.php' );
		if (
			! IS_PROFILE_PAGE
			and isset( $GLOBALS['user_id'] )
		)
			$action = add_query_arg( 'user_id', $GLOBALS['user_id'], $action );

		return $action;
	}
}