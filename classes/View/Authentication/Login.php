<?php
/**
 * Manages the view data and context for the login form
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 */

namespace View\Authentication;

use View;

/**
 * Manages the view data and context for the login form
 *
 * @package View\Authentication
 */
class Login extends \View_Layout {

	/**
	 * @var array errors to display on the form, keyed by field
	 */
	protected $errors = array();

	/**
	 * @var array post data to display as default values for the fields
	 */
	protected $post_data = array();

	/**
	 * Get array of error messages, to display, keyed by fieldname
	 *
	 * @return array error message
	 */
	protected function var_errors()
	{
		return $this->errors;
	}

	/**
	 * Get current post_data array from when the form was last submitted
	 *
	 * @return mixed
	 */
	protected function var_post_data()
	{
		return $this->post_data;
	}


}