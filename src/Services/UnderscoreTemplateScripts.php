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
 * Class UnderscoreTemplateScripts
 * Allow registering Underscore Templates using the WP Dependency API
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class UnderscoreTemplateScripts implements ServiceInterface
{
	/**
	 * Allows registering Backbone templates as scripts
	 * Add type="text/template" and id="{handle}" for Backbones .tmpl <script>s
	 * @param string $html
	 * @param string $handle
	 * @param string $src
	 * @return string
	 */
	public function setup( $html = '', $handle = '', $src = '' )
	{
		if (
			empty( $src )
			or ! strstr( $src, '.tmpl' )
		)
			return $html;

		$dom = new \DOMDocument;
		$dom->loadHTML( $html );

		/** @var \DOMElement $tag */
		foreach ( $dom->getElementsByTagName( 'script' ) as $tag )
		{
			if ( $tag->hasAttribute( 'type' ) )
			{
				$tag->setAttribute( 'type', 'text/template' );
				$tag->setAttribute( 'id', $handle );
				$tag->appendChild( $dom->createTextNode( file_get_contents( $src ) ) );
				# new node: 25% faster than
				# @link http://chat.stackexchange.com/transcript/message/19567599#19567599
				//$tag->nodeValue = esc_html( file_get_contents( $src ) );
				$tag->removeAttribute( 'src' );
				$html = $dom->saveHTML( $tag );
			}
		}

		return $html;
	}
}