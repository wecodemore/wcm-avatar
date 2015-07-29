<?php

namespace WCM\User\Avatar\Models;

/**
 * Interface AjaxAwareInterface
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
interface AjaxAwareInterface
{

	public function getAction();


	public function getNonce();
}