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
 * Class AvatarDisplayTemplate
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class AvatarDisplayTemplate
	implements TemplateInterface,
	           PrintableInterface,
	           DataAwareInterface
{

	/** @var int */
	private $att_id;


	public function setData( $att_id )
	{
		$this->att_id = $att_id;
	}


	public function __toString()
	{
		return $this->display();
	}


	public function display()
	{
		$name  = wp_basename( wp_get_attachment_url( $this->att_id ) );
		$thumb = wp_get_attachment_image( $this->att_id );
		$meta  = wp_get_attachment_metadata( $this->att_id );

		$hidden = ( empty( $name ) or ! $meta )
			? 'hidden'
			: '';

		$color = get_user_meta( get_current_user_id(), 'admin_color', TRUE );
		! $color and $color = 'fresh';
		$scheme = $GLOBALS['_wp_admin_css_colors'][$color]->colors;

		$edit = _x( 'Edit', 'media item' );
		$link = esc_url( get_edit_post_link( $this->att_id ) );

		# @TODO Dynamically inject `tmpl-___--container` IDs
		return <<<HTML
<div id="tmpl-main--container">
	<div class="wrap  {$hidden}">
		<div class="drag-drop">
			<div id="drag-drop-area">
				<div id="tmpl-thumb--container">
					<div id="{$this->att_id}" class="attachment-container">
						{$thumb}
					</div>
					<div id="tmpl-delete--container">
						<span class="icon  dashicons  dashicons-no-alt"
							style="background-color: {$scheme[1]}"
							onMouseOver="this.style.color='{$scheme[2]}'"
							onMouseOut="this.style.color='#fff'"></span>
					</div>
					<div id="tmpl-caption--container">
						<p class="logo--caption" style="background-color: {$scheme[1]}">
							{$name} | Size: {$meta['width']}px &times; {$meta['height']}px | <a class="edit-attachment" href="{$link}" target="_blank">{$edit}</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
HTML;
	}
}