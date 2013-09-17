<?php namespace Greggilbert\Redoubt\Facades;

/**
 * Facade for Redoubt
 */

use Illuminate\Support\Facades\Facade;

class Redoubt extends Facade
{
    /**
	 * Get the registered name of the component.
	 * 
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'Redoubt'; }

}