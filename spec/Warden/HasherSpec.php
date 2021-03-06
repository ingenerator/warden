<?php
/**
 * Spec for \Warden\Hasher
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Warden;

// Include the Kohana environment generated by koharness
require_once(__DIR__.'/../../koharness_bootstrap.php');

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Spec for the Warden hasher class
 *
 * @package spec\Warden
 * @see Warden\Hasher
 */
class HasherSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Warden\Hasher');
    }

	public function it_hashes_a_value_and_returns_the_hash()
	{
		$this->hash('foobar')->shouldNotMatch('/foobar/');
	}

	public function it_randomly_salts_each_hash_it_creates()
	{
		$hash_1 = $this->hash('password');

		$this->hash('password')->shouldNotReturn($hash_1);
	}

	public function it_can_verify_a_hash_it_created()
	{
		$hash = $this->hash('password');
		$this->verify('password', $hash)->shouldReturn(TRUE);
		$this->verify('something else', $hash)->shouldReturn(FALSE);
	}

	public function it_can_indicate_that_an_existing_hash_should_be_upgraded()
	{
		$this->configure(
			array(
			     'algorithm' => \PASSWORD_DEFAULT,
			     'options'   => array(
				     'cost' => 8
			     )
			)
		);

		$old_hash = $this->hash('password');
		$this->needs_rehash($old_hash)->shouldReturn(FALSE);

		$this->configure(
			array(
			     'algorithm' => \PASSWORD_DEFAULT,
			     'options'   => array(
				     'cost' => 9
			     )
			)
		);

		$this->needs_rehash($old_hash)->shouldReturn(TRUE);
	}

	public function it_rejects_configuration_with_no_algorithm()
	{
		$this->shouldThrow('\InvalidArgumentException')
			->duringConfigure(array('options' => array('cost' => 12))
		);
	}

	public function it_rejects_configuration_with_no_options_array()
	{
		$this->shouldThrow('\InvalidArgumentException')
			->duringConfigure(array('algorithm' => \PASSWORD_DEFAULT));
	}

}
