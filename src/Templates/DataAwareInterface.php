<?php

namespace WCM\User\Avatar\Templates;

/**
 * Interface DataAwareInterface
 * @package WCM\User\Avatar
 */
interface DataAwareInterface extends TemplateInterface
{
	public function setData( $data );
}