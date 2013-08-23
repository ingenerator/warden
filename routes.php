<?php
/**
 * Defines routes for the standard controllers and actions in the warden module. To include these routes, add them to
 * your applicaton bootstrap in a suitable location.
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 */

Route::set('authentication', '<action>', array('action' => 'login'))
   ->defaults(array(
		'controller' => 'Authentication'
   ));
