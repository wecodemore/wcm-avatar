<?php

namespace WCM\User\Avatar\Templates;

use WCM\User\Avatar\Models\AffixAwareInterface;

/**
 * Class AffixTextTemplate
 * @package WCM\User\Avatar
 */
class AffixTextTemplate implements TemplateInterface
{
	/** @var AffixAwareInterface */
	private $data;

	public function __construct( AffixAwareInterface $data )
	{
		$this->data = $data;
	}

	public function display()
	{
		$label = $this->data->getLabel();
		$value = $this->data->getValue();
		! $value and $value = '';

		$key   = $this->data->getKey();
		$desc  = $this->data->getDescription();

		$prefix = $this->data->getPrefix();
		if ( ! empty( $prefix ) )
			$prefix = sprintf(
				'<span class="profile-field--prefix">%s</span>',
				$prefix
			);

		$suffix = $this->data->getSuffix();
		if ( ! empty( $suffix ) )
			$suffix = sprintf(
				'<span class="profile-field--suffix">%s</span>',
				$suffix
			);

		$classes[] = ! empty( $prefix ) ? 'has-prefix' : '';
		$classes[] = ! empty( $prefix ) ? 'has-suffix' : '';
		$classes = join( "  ", $classes );

		return <<<HTML
</table>
<table class="form-table">
	<tbody>
		<tr class="user-{$key}-wrap">
			<th>
				<label for="{$key}">{$label}</label>
			</th>
			<td class="{$classes}">
				{$prefix}<input type="text"
					name="{$key}"
					id="{$key}"
					value="{$value}"
					class="small-text  boxed-left  boxed-right">{$suffix}
				<p class="description">{$desc}</p>
			</td>
		</tr>
	</tbody>
</table>
HTML;
	}
}