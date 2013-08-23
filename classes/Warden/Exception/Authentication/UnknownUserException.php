<?php
/**
 * Thrown when attempting to authenticate a user who is not registered
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 */

namespace Warden\Exception\Authentication;
use Warden\Exception\AuthenticationException;

/**
 * Thrown when attempting to authenticate a user who is not registered
 *
 * @package Warden\Exception\Authentication
 */
class UnknownUserException extends AuthenticationException {}