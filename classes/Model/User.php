<?php
/**
 * Model an application user
 *
 * @author     Andrew Coulton <andrew@ingenerator.com>
 * @copyright  2013 inGenerator Ltd
 * @licence    BSD
 */
namespace Model;

/**
 * @Entity(repositoryClass="\Model\Repository\User")
 * @Table(name="users")
 */
class User extends \Warden\Model\User {}