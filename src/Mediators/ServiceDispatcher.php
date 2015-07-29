<?php
/**
 * This file is part of the WCM Avatar package.
 *
 * © Franz Josef Kaiser / wecodemore
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCM\User\Avatar\Mediators;

use WCM\User\Avatar\Services\ServiceInterface;

/**
 * Class ServiceDispatcher
 * Dispatcher: Attach Services, dispatches them on a hook
 *
 * @package WCM\User\Avatar
 * @author  Franz Josef Kaiser <franzjosef.kaiser@nzz.at>
 */
class ServiceDispatcher
{

	/** @var \SplObjectStorage */
	private $services;


	/**
	 * @param string $filter
	 */
	public function __construct( $filter = '' )
	{
		$this->services = new \SplObjectStorage;

		// Automatically dispatches all registered services on the current or a custom filter
		empty( $filter ) and $filter = current_filter();
		add_filter( $filter, [
			$this,
			'dispatch'
		], 20, PHP_INT_MAX - 1 );
	}


	/**
	 * @param ServiceInterface $service
	 * @param string           $filter
	 */
	public function register( ServiceInterface $service, $filter )
	{
		$this->services->attach( $service, $filter );
	}


	/**
	 * @param \SplObserver $service
	 */
	public function detach( \SplObserver $service )
	{
		$this->services->detach( $service );
	}


	/**
	 * Dispatch Services on filters
	 * All registered Services need to have the ServiceInterface
	 * and therefore have a `setup()` method
	 */
	public function dispatch()
	{
		$dispatcher = $this;
		foreach ( $this->services as $s )
		{
			$service = $this->services->current();

			// Allow skipping a service
			if ( apply_filters( get_class( $service ), FALSE ) )
				continue;

			$context = $this->services->getInfo();
			! is_array( $context )
			and $context = [ $this->services->getInfo(), ];

			foreach ( $context as $filter )
			{
				add_filter( $filter, function () use ( $dispatcher, $service )
				{
					return call_user_func_array( [
						$service,
						'setup'
					], func_get_args() );
				}, 10, PHP_INT_MAX - 1 );
			}
		}
	}
}