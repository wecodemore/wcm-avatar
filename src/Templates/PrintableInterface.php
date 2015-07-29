<?php

namespace WCM\User\Avatar\Templates;

/**
 * Interface PrintableInterface
 * @package WCM\User\Avatar\Templates
 */
interface PrintableInterface extends TemplateInterface
{
	public function __toString();
}