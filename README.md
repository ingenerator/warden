Warden
======

An all-in-one user authentication, authorization and management module for Kohana 3.3.

[![Build Status](https://secure.travis-ci.org/ingenerator/warden.png?branch=master)](http://travis-ci.org/ingenerator/warden)

What is it?
-----------

Warden is an opinionated module providing a full-stack solution for authenticating, authorizing and managing users in
your application. It combines (and depends on) a number of other inGenerator modules to provide a quick and ready to
integrate application component with support for:

* Login
* Automatic password hash upgrading
* ~~Logout~~
* ~~Registration~~
* ~~Password reset~~
* ~~Verify email change~~
* ~~Manage user roles and permissions~~
* ~~OAUTH authorization server~~

It is designed to be extensible, but will enforce more conventions and ways of doing things on you than the stock Kohana
auth module. In particular, you'll need to be using view models and managing your layout as part of the view layer.

Installation
------------

Add to your composer.json:

```json
{
	"require": {
		"ingenerator/warden" : "dev-master"
	}
}
```

Load the module in your application bootstrap. Also include the module routes unless you want to define your own
routing.

```php
Kohana::$modules(array(
	'warden' => MODPATH.'warden'
));

require_once(MODPATH.'warden/routes.php');
```

Hacking
-------

Warden is built with [PHPSpec](http://phpspec.net) specifications - every change should be led by a new spec
implementation that verifies the functionality. The specs assume a standard Kohana working directory layout and that
they are being run from the root path.

Roadmap
-------
We'll start by building the functionality listed above.

Contributors and Credits
------------------------

* Andrew Coulton [acoulton](http://github.com/acoulton) [lead developer]

Licence
-------
[BSD Licence - see LICENSE](LICENSE)