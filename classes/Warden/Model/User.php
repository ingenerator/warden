<?php
/**
 * Model an application user
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 */

namespace Warden\Model;

use Warden\Hasher;

/**
 * Model an application user
 *
 * @package Warden\Model
 */
class User {

	/**
	 * Returned from verify_password if the password was rehashed as part of verification
	 */
	const PASSWORD_OK_UPGRADED = 1;

	/**
	 * @var int the user ID
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id = NULL;

	/**
	 * @var string user's email address
	 * @Column(unique=true, nullable=false)
	 */
	protected $email = NULL;

	/**
	 * @var string user's hashed password
	 * @Column(nullable=false)
	 */
	protected $password_hash = NULL;

	/**
	 * @var \DateTime time the user last logged in
	 * @Column(type="datetime")
	 */
	protected $last_login = NULL;

	/**
	 * @var \Warden\Hasher the hasher to use for managing password hashes
	 */
	protected $hasher = NULL;

	/**
	 * Get the hasher instance to use for managing password hashes and tokens. Pass in a hasher if you want to override
	 * the default implementation - otherwise a new instance of Warden\Hasher will be created and configured based on
	 * the warden.hash Kohana config.
	 *
	 * @param Hasher $hasher a hasher to use instead of the default
	 *
	 * @return Hasher
	 */
	public function hasher(Hasher $hasher = NULL)
	{
		if ($hasher)
		{
			$this->hasher = $hasher;
		}

		if ( ! $this->hasher)
		{
			$this->hasher = new Hasher;
			$this->hasher->configure(\Kohana::$config->load('warden.hash'));
		}

		return $this->hasher;
	}

	/**
	 * Get the user ID
	 *
	 * @return int
	 */
	public function get_id()
	{
		return $this->id;
	}

	/**
	 * Set the user's email address
	 *
	 * @param string $email the new email address to set
	 *
	 * @return $this
	 */
	public function set_email($email)
	{
		$this->email = $email;
		return $this;
	}

	/**
	 * Get the user's email address
	 *
	 * @return string
	 */
	public function get_email()
	{
		return $this->email;
	}

	/**
	 * Set the user's password, hashing it for storage
	 *
	 * @param string $password The new password
	 *
	 * @return $this
	 */
	public function set_password($password)
	{
		$this->password_hash = $this->hasher()->hash($password);
		return $this;
	}

	/**
	 * Verify the user's password against the stored hash. If the password is valid and the hash does not meet current
	 * standards, it will be upgraded. Calling code is of course responsible for persisting and flushing the user entity
	 * if required.
	 *
	 * @param string $password the password to check
	 *
	 * @return int TRUE if valid password, FALSE if invalid, User::PASSWORD_OK_UPGRADED if password was rehashed and should be saved
	 */
	public function verify_password($password)
	{
		$hasher = $this->hasher();

		$verified = $hasher->verify($password, $this->password_hash);
		if ($verified AND $hasher->needs_rehash($this->password_hash))
		{
			$this->password_hash = $hasher->hash($password);
			return self::PASSWORD_OK_UPGRADED;
		}

		return $verified;
	}

	/**
	 * Update the last_login time to the current timestamp
	 *
	 * @return $this
	 */
	public function update_last_login()
	{
		$this->last_login = new \DateTime();
		return $this;
	}

	/**
	 * Get the time this user last logged in
	 *
	 * @return \DateTime
	 */
	public function get_last_login()
	{
		return $this->last_login;
	}

}