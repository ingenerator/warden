<?php
/**
 *  ${CARET}Describe
 *
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @copyright 2013 Creative Carbon Scotland
 * @licence   proprietary
 */
namespace Model;

/**
 * @Entity(repositoryClass="\Model\Repository\User")
 * @Table(name="users")
 */
class User extends \Warden\Model\User {}