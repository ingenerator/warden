<?php
/**
 * Handles HTTP requests related to user authentication
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 */
namespace Warden\Controller;
use Warden\Exception\Authentication\InvalidPasswordException;
use Warden\Exception\Authentication\UnknownUserException;
use Warden\Warden;

/**
 * Handles HTTP requests related to user authentication
 *
 * @package Controller
 */
class Authentication extends \Controller {

	/**
	 * @var \Warden the warden instance managing authentication
	 */
	protected $warden = NULL;

	/**
	 * @var \ViewFactory the view factory for creating views
	 */
	protected $view_factory = NULL;

	/**
	 * Get the current Warden instance, optionally injecting a new one. If nothing is injected, will create an instance
	 * as required.
	 *
	 * @param \Warden $warden a warden instance (or stub) to inject
	 *
	 * @return \Warden
	 */
	public function warden($warden = NULL)
	{
		if ($warden)
		{
			$this->warden = $warden;
		}

		if ( ! $this->warden)
		{
			$factory = new \Doctrine_EMFactory();
			$this->warden = new Warden(
				$factory->entity_manager(),
				\Session::instance()
			);
		}

		return $this->warden;
	}

	/**
	 * Get the current ViewFactory instance, optionally injecting a new one. If nothing is injected, will create an
	 * instance as required.
	 *
	 * @param \ViewFactory $view_factory a view factory instance (or stub) to inject
	 *
	 * @return \ViewFactory
	 */
	public function view_factory($view_factory = NULL)
	{
		if ($view_factory)
		{
			$this->view_factory = $view_factory;
		}

		if ( ! $this->view_factory)
		{
			$this->view_factory = new \ViewFactory;
		}

		return $this->view_factory;
	}

	/**
	 * Display the login page or handle a user login request. Redirects on successful login, otherwise displays the form
	 * with errors highlighted.
	 *
	 * @return void
	 */
	public function action_login()
	{
		// If the user is already authenticated, go straight to login
		$warden = $this->warden();

		if ($warden->current_user())
		{
			$this->redirect('/', 302);
		}

		$errors = array();
		$post_data = array('email' => NULL, 'password' => NULL);

		if ($this->request->method() === \Request::POST)
		{
			$post_data = $this->request->post();

			$validation = \Validation::factory($post_data)
				->rule('email', 'not_empty')
				->rule('password', 'not_empty');

			if ($validation->check())
			{
				try
				{
					$user = $this->warden()->authenticate($post_data['email'], $post_data['password']);
					$warden->login($user);
					$this->redirect('/', 302);
				}
				catch (UnknownUserException $e)
				{
					$errors['email'] = \Kohana::message('forms/authentication/login', 'email.not_registered');
				}
				catch (InvalidPasswordException $e)
				{
					$errors['password'] = \Kohana::message('forms/authentication/login', 'password.incorrect');
				}
			}
			else
			{
				$errors = $validation->errors('forms/authentication/login');
			}
		}

		// Either they attempted to login and failed, or they have not yet tried to login
		$view = $this->view_factory()->create('\View\Authentication\Login')
		     ->set('errors', $errors)
		     ->set('post_data', \Arr::extract($post_data, array('email','password')));

		$this->response->body($view->render());
	}

	/**
	 * Log out the active user (if any) and redirect to the login page
	 *
	 * @return void
	 */
	public function action_logout()
	{
		$warden = $this->warden();
		if ($warden->current_user())
		{
			$warden->logout();
		}
		$this->redirect('/login', 302);
	}

}
