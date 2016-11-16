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
 * Class UnderscoreTemplate
 *
 * @package WCM\User\Avatar
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 */
class UnderscoreTemplateEnqueuer
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $path;

    /**
     * @param string $url
     * @param string $path
     */
    public function __construct( $url, $path )
    {
        $this->url = trailingslashit( $url );
        $this->path = trailingslashit( $path );
    }

    /**
     * @return string
     */
    public function baseUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function basePath()
    {
        return $this->path;
    }

    /**
     * @param string $template_name
     * @return bool
     */
    public function enqueue( $template_name )
    {
        $path = $this->templatePath( $template_name );
        if ( !$path || !is_readable( $path ) ) {
			return false;
        }

        $filter = function ( $script_tag_markup = '', $handle = '' ) use ( $template_name ) {

	        if( $handle !== "tmpl-{$template_name}" ){
		        return $script_tag_markup;
	        }

            return $this->makeTemplateInline( $script_tag_markup, $handle, $template_name );
        };

        add_filter( 'script_loader_tag', $filter, 20, 2 );

        wp_enqueue_script(
            "tmpl-{$template_name}",
            $this->templateUrl( $template_name ),
            [],
            @filemtime($path) ?: null,
            true
        );

        return true;

    }

    /**
     * @param $template
     * @return string
     */
    private function templatePath( $template )
    {
        return $this->path . 'assets/templates/' . $template . '.tmpl';
    }

	/**
	 * @param $template
	 * @return string
	 */
	private function templateUrl( $template ){
		return $this->url. 'assets/templates/' . $template . '.tmpl';
	}

    /**
     * @param $template_path
     * @return string
     */
    private function loadTemplateContent( $template_path )
    {
        if ( is_readable( $template_path ) ) {
            return file_get_contents( $template_path );
        }

        return '';
    }

    /**
     * @param string $script_tag_markup
     * @param string $handle
     * @param string $template_name
     * @return string
     */
    private function makeTemplateInline( $script_tag_markup = '', $handle = '', $template_name = '' ) {
        $dom = new \DOMDocument;
        $dom->loadHTML( $script_tag_markup );
        $tags = $dom->getElementsByTagName( 'script' );
        $tag = $tags->length ? $tags->item(0) : null;

        if ( !$tag || !$template_name ) {
            return $script_tag_markup;
        }

        $template_path = $this->templatePath( $template_name );
        $template_content = $this->loadTemplateContent( $template_path );

        if ( !$template_content ) {
            return $script_tag_markup;
        }

        /** @var \DOMElement $tag */
        $tag->setAttribute( 'type', 'text/template' );
        $tag->setAttribute( 'id', $handle );
        $tag->appendChild( $dom->createTextNode( $template_content ) );
        # new node: 25% faster than
        # @link http://chat.stackexchange.com/transcript/message/19567599#19567599
        //$tag->nodeValue = esc_html( file_get_contents( $src ) );
        $tag->removeAttribute( 'src' );

        return $dom->saveHTML( $tag );
    }
}