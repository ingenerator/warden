<?php
/**
 * Template for rendering the login form
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 *
 * @see \View\Authentication\Login
 * @var array $errors    array of error messages to display, keyed by fieldname
 * @var array $post_data array of previously-submitted values
 */
?>
<form action='/login' method='post'>
	<fieldset>
		<legend>Login</legend>

		<p>
			<label for="email">Email: </label>
			<input id="email" name="email" value="<?=$post_data['email'];?>">
			<?php if (isset($errors['email'])): ?>
				<strong><?=$errors['email'];?></strong>
			<?php endif; ?>
		</p>

		<p>
			<label for="password">Password: </label>
			<input id="password" name="password" value="<?=$post_data['password'];?>">
			<?php if (isset($errors['password'])): ?>
				<strong><?=$errors['password'];?></strong>
			<?php endif; ?>
		</p>

		<p>
			<button type="submit" name="login" value="login">Login</button>
		</p>
	</fieldset>
</form>
