<?php
/**
 * Handles HTTP requests related to user authentication
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 */
namespace Controller;

/**
 * Handles HTTP requests related to user authentication
 *
 * @package Controller
 */
class Authentication extends \Warden\Controller\Authentication {}

// Define the underscored alias for now, pending Kohana supporting namespaced controllers
class_alias('\Controller\Authentication', 'Controller_Authentication');
