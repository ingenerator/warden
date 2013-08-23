<?php
/**
 * Thrown when attempting to authenticate a user with an incorrect password
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 */

namespace Warden\Exception\Authentication;
use Warden\Exception\AuthenticationException;

/**
 * Thrown when attempting to authenticate a user with an incorrect password
 *
 * @package Warden\Exception\Authentication
 */
class InvalidPasswordException extends AuthenticationException {}