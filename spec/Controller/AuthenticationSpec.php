<?php
/**
 * Specify the behaviour of Controller\Authentication
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Controller;

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Specifies the behaviour of Controller\Authentication
 *
 * @package spec\Controller
 * @see \Controller\Authentication
 * @see \Controller\Warden\Authentication
 */
class AuthenticationSpec extends ObjectBehavior
{
	/**
	 * @param \Request     $request
	 * @param \Response    $response
	 * @param \Warden      $warden
	 * @param \ViewFactory $view_factory
	 */
	public function let($request, $response, $warden, $view_factory)
	{
		$this->beConstructedWith($request, $response);

		$this->warden($warden);
		$warden->current_user()->willReturn(NULL);
		
		$this->view_factory($view_factory);
	}

    public function it_is_initializable()
    {
        $this->shouldHaveType('Controller\Authentication');
    }

	/**
	 * @param \Request                   $request
	 * @param \Response                  $response
 	 * @param \ViewFactory               $view_factory
	 * @param \View\Authentication\Login $view
	 */
	public function its_login_action_renders_a_login_view_for_get_requests($request, $response, $view_factory, $view)
	{
		$request->method()->willReturn(\Request::GET);

		$view->set('errors', array())->willReturn($view);
		$view->set('post_data', array('email' => NULL, 'password' => NULL))->willReturn($view);

		$this->shouldSendViewResponse('\View\Authentication\Login', $view_factory, $view, $response);

		$this->action_login();
	}

	/**
	 * @param \Request    $request
	 * @param \Warden     $warden
	 * @param \Model\User $user
	 */
	public function its_login_action_redirects_to_homepage_if_user_already_authed($request, $warden, $user)
	{
		$warden->current_user()->willReturn($user);

		$this->shouldThrow('\HTTP_Exception_Redirect')
		->during('action_login');
	}

	/**
	 * @param \Request    $request
	 * @param \Warden     $warden
	 * @param \Model\User $user
	 */
	public function its_login_action_logs_in_and_redirects_when_user_provides_credentials($request, $warden, $user)
	{
		$request->method()->willReturn(\Request::POST);
		$request->post()->willReturn(array('email' => 'me@foo.com', 'password' => '12345678', 'login'    => 'login'));

		$warden->authenticate('me@foo.com', '12345678')->willReturn($user);
		$warden->login($user)->shouldBeCalled();

		$this->shouldThrow('\HTTP_Exception_Redirect')
	        ->during('action_login');
	}

	/**
	 * @param \Request                   $request
	 * @param \Response                  $response
	 * @param \Warden                    $warden
  	 * @param \ViewFactory               $view_factory
	 * @param \View\Authentication\Login $view
	 */
	public function its_login_action_displays_the_form_with_errors_when_user_leaves_a_field_blank($request, $response, $warden, $view_factory, $view)
	{
		$request->method()->willReturn(\Request::POST);
		$request->post()->willReturn(array('email' => '', 'password' => '12345678', 'login' => 'login'));

		$warden->authenticate(Argument::any(), Argument::any())->shouldNotBeCalled();

		$view->set('errors', array('email' => \Kohana::message('forms/authentication/login', 'email.not_empty')))->willReturn($view);
		$view->set('post_data', array('email' => '', 'password' => '12345678'))->willReturn($view);

		$this->shouldSendViewResponse('\View\Authentication\Login', $view_factory, $view, $response);

		$this->action_login();
	}

	/**
	 * @param \Request    $request
	 * @param \Response   $response
	 * @param \Warden     $warden
	 * @param \ViewFactory               $view_factory
	 * @param \View\Authentication\Login $view
	 */
	public function its_login_action_displays_the_form_with_errors_when_user_gives_wrong_password($request, $response, $warden, $view_factory, $view)
	{
		$request->method()->willReturn(\Request::POST);
		$request->post()->willReturn(array('email' => 'me@foo.com', 'password' => 'wrong', 'login' => 'login'));

		$warden->authenticate('me@foo.com', 'wrong')
			->willThrow('Warden\Exception\Authentication\InvalidPasswordException');

		$view->set('errors', array('password' => \Kohana::message('forms/authentication/login', 'password.incorrect')))->willReturn($view);
		$view->set('post_data', array('email' => 'me@foo.com', 'password' => 'wrong'))->willReturn($view);

		$this->shouldSendViewResponse('\View\Authentication\Login', $view_factory, $view, $response);

		$this->action_login();
	}

	/**
	 * @param \Request                   $request
	 * @param \Response                  $response
	 * @param \Warden                    $warden
	 * @param \ViewFactory               $view_factory
	 * @param \View\Authentication\Login $view
	 */
	public function its_login_action_displays_the_form_with_errors_when_user_gives_unknown_email($request, $response, $warden, $view_factory, $view)
	{
		$request->method()->willReturn(\Request::POST);
		$request->post()->willReturn(array('email' => 'nobody@foo.com', 'password' => 'ok', 'login' => 'login'));

		$warden->authenticate('nobody@foo.com', 'ok')
			->willThrow('Warden\Exception\Authentication\UnknownUserException');

		$view->set('errors', array('email' => \Kohana::message('forms/authentication/login', 'email.not_registered')))->willReturn($view);
		$view->set('post_data', array('email' => 'nobody@foo.com', 'password' => 'ok'))->willReturn($view);

		$this->shouldSendViewResponse('\View\Authentication\Login', $view_factory, $view, $response);

		$this->action_login();
	}

	/**
	 * @param \Warden     $warden
	 * @param \Model\User $user
	 */
	public function its_logout_action_logs_out_active_user_and_redirects_to_login($warden, $user)
	{
		$warden->current_user()->willReturn($user);

		$warden->logout()->shouldBeCalled();

		$this->shouldThrow('\HTTP_Exception_Redirect')
			->during('action_logout');
	}

	/**
	 * @param \Warden     $warden
	 */
	public function its_logout_action_redirects_anonymous_user_to_login($warden)
	{
		$warden->current_user()->willReturn(NULL);

		$warden->logout()->shouldNotBeCalled();

		$this->shouldThrow('\HTTP_Exception_Redirect')
			->during('action_logout');
	}



	/**
	 * Helper for configuring expectations that a particular view class will be created and rendered to the response
	 * body. Set the collaborator expectations on each individual spec and pass them to this method to wire them up
	 * together.
	 *
	 * [!!] Note that this method expects the view to be explicitly rendered before passing to body(). This is much
	 *      safer, as PHP will fail with unreliable fatal errors if there is an exception during a __toString method.
	 *
	 * @param string       $class_name   name of the view class to create
	 * @param \ViewFactory $view_factory view factory mock to pass in
	 * @param \View        $view         view mock to pass in
	 * @param \Response    $response     response that should receive the rendered body
	 *
	 * @return void
	 */
	protected function shouldSendViewResponse($class_name, $view_factory, $view, $response)
	{
		$text_response = "response-from-".$class_name;

		$view_factory->create($class_name)->willReturn($view);
		$view->render()->willReturn($text_response);
		$response->body($text_response)->shouldBeCalled();
	}

}
