<?php
/**
 * Wrap the password_hash functions
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 */

namespace Warden;

/**
 * A simple wrapper for the \password_hash functions (available natively in PHP5, or implemented with the
 * ircmaxwell\password_compat library. We wrap into an object to make it easier to spec behaviour of classes
 * (eg Model\User) that use this behaviour.
 *
 * @package Warden
 */
class Hasher
{
	/**
	 * @var int The algorithm to use
	 */
	protected $algorithm = \PASSWORD_DEFAULT;

	/**
	 * @var array Options to pass to the hashing library
	 */
	protected $options = array();

	/**
	 * Generate and return a secure hash of the cleartext value. The hash includes a random salt and the information
	 * required to identify the salt, algorithm and options in future so that it can always be revalidated even if the
	 * hashing configuration changes.
	 *
	 * @param string $cleartext the value to hash
	 *
	 * @see password_hash
	 * @return string the hash
	 */
	public function hash($cleartext)
    {
        return \password_hash($cleartext, $this->algorithm, $this->options);
    }

	/**
	 * Verify whether a provided cleartext value matches an existing hash.
	 *
	 * @param string $cleartext cleartext to verify
	 * @param string $hash      existing hash
	 *
	 * @see password_verify
	 * @return bool TRUE if the hash matches, FALSE if not
	 */
	public function verify($cleartext, $hash)
    {
        return \password_verify($cleartext, $hash);
    }

	/**
	 * Configure the hashing options - algorithm and algorithm-specific options. The configuration must be passed as
	 * an array with an (int) algorithm and options key.
	 *
	 *     $hasher->configure('algorithm' => PASSWORD_BCRYPT, 'options' => array('cost' => 15'))
	 *
	 * @param array $config configuration to set, with 'algorithm' and 'options' keys
	 *
	 * @throws \InvalidArgumentException if the configuration passed is not valid
	 * @return void
	 */
	public function configure(array $config)
    {
        if ( ! isset($config['algorithm']))
        {
	        throw new \InvalidArgumentException("\\Warden\\Hasher::configure requires an algorithm option");
        }

	    if ( ! isset($config['options']))
	    {
		    throw new \InvalidArgumentException("\\Warden\\Hasher::configure requires an options option");
	    }

	    $this->algorithm = $config['algorithm'];
	    $this->options = $config['options'];
    }

	/**
	 * Checks if a given hash needs to be upgraded to meet the current hashing configuration. You can use this when
	 * you have the cleartext available to allow automatic upgrading of hashes over time. For example:
	 *
	 *     // In a user model
	 *     public function check_password($password)
	 *     {
	 *          $verified = $this->hasher->verify($password, $this->hash);
	 *          if ($verified AND $this->hasher->needs_rehash($this->hash))
	 *          {
	 *              $this->hash = $this->hasher->hash($password);
	 *              $this->save();
	 *          }
	 *     }
	 *
	 * @param string $hash existing hash to check
	 *
	 * @return bool TRUE if the hash should be regenerated to meet most recent standards
	 * @see password_needs_rehash
	 */
	public function needs_rehash($hash)
    {
		return \password_needs_rehash($hash, $this->algorithm, $this->options);
    }
}