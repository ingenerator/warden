<?php
/**
 * The Warden class is the entry point for all Warden functionality
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 */
namespace Warden;

use Doctrine\ORM\EntityManager;
use Model\User;
use Warden\Exception\Authentication\InvalidPasswordException;
use Warden\Exception\Authentication\UnknownUserException;

/**
 * The Warden class is the entry point for all Warden functionality. It implements top-level factory and use case
 * methods, managing dependencies for created classes as required.
 *
 * @package Warden
 */
class Warden {

	/**
	 * @var \Doctrine\ORM\EntityManager entity manager for loading and saving user entities
	 */
	protected $entity_manager = NULL;

	/**
	 * @var \Session session instance for storing logged in user state
	 */
	protected $session = NULL;

	/**
	 * Create an instance of the class
	 *
	 * @param EntityManager $entity_manager for loading and saving user entities
	 * @param \Session      $session        for storing logged in user state across requests
	 *
	 * @return \Warden\Warden
	 */
	public function __construct(EntityManager $entity_manager, \Session $session)
	{
		$this->entity_manager = $entity_manager;
		$this->session = $session;
	}

	/**
	 * Authenticate a user by email and password, returning the authenticated user if matched.
	 *
	 * [!!] This does not log the user in, you must pass the received entity to the Warden::login method.
	 *
	 * @param string $email    email address of the user to locate
	 * @param string $password password of the user to check
	 *
	 * @return Model\User if the email and password match a known user
	 * @throws Exception\Authentication\UnknownUserException if the email address is not registered
	 * @throws Exception\Authentication\InvalidPasswordException if the email is registered but the password does not match
	 * @throws \UnexpectedValueException if the user model verify method returns an unexpected value
	 */
	public function authenticate($email, $password)
	{
		$user = $this->entity_manager->getRepository('Model\User')->findOneBy(array('email' => $email));

		if ( ! $user)
		{
			throw new UnknownUserException("There is no registered user with email address $email");
		}

		/** @var Model\User $user */
		$verified = $user->verify_password($password);

		if ($verified === Model\User::PASSWORD_OK_UPGRADED)
		{
			$this->entity_manager->persist($user);
			return $user;
		}
		else if ($verified === TRUE)
		{
			return $user;
		}
		else if ($verified === FALSE)
		{
			throw new InvalidPasswordException("Incorrect password given for user $email");
		}
		else
		{
            throw new \UnexpectedValueException(
					"Warden::authenticate did not expect Model\\User::authenticate to return '".$verified."'"
			);
		}
	}

	/**
	 * Log in the provided user. This will store their ID to the session, update their last login time and persist their
	 * record back to the database. A user entity may be found by calling Warden::authenticate() with a username and
	 * password, or by loading from a token, user registration or other source.
	 *
	 * @param User $user the user to login.
	 *
	 * @return void
	 */
	public function login(User $user)
	{
		// Update the last login time and flush the entity
		$user->update_last_login();
		$this->entity_manager->flush($user);

		// Regenerate the session ID and set the current user ID in the session
		$this->session->regenerate();
		$this->session->set('warden.current_user', $user->get_id());
	}

	/**
	 * Return the current user entity, reloading it from the database as required. If the user ID does not match a valid
	 * user model then an exception is thrown and the session user id is cleared.
	 *
	 * @return Model\User
	 * @throws \UnexpectedValueException
	 */
	public function current_user()
	{
		// Locate the current user and return NULL if nobody is logged in
		$user_id = $this->session->get('warden.current_user');
		if ($user_id === NULL)
		{
			return NULL;
		}

		// Find the user entity
		// Note that the Doctrine repository internally tracks the entity so this call will only hit the DB once per request
		$user = $this->entity_manager->getRepository('Model\User')->find($user_id);
		if ( ! $user)
		{
			$this->session->delete('warden.current_user');
			throw new \UnexpectedValueException("The user id $user_id did not match a valid existing user entity");
		}
		return $user;
	}

	/**
	 * Logs the current user out by completely destroying their session
	 *
	 * @return void
	 */
	public function logout()
	{
		$this->session->destroy();
	}

}