<?php

namespace templatemonster\ldapauth\services;

use yii\rbac\ManagerInterface;
use common\models\User;
use templatemonster\ldapauth\models\AclForm;

interface LdapAuthInterface
{
    /**
     * Authenticates a user via LDAP.
     * Returns true if authentication succeeds, false otherwise
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function authenticate($username, $password);

    /**
     * Returns list of available ldap groups
     *
     * @return array
     */
    public function getGroupsList();

    /**
     * Returns a list of groups in which user consists
     *
     * @param string $username
     * @return array
     */
    public function getGroupsOfUser($username);

    /**
     * Returns user email or 'empty' if not set
     *
     * @param $username
     * @return string
     */
    public function getUserEmail($username);

    /**
     * Sync Rbac roles with Ldap groups of given $user
     *
     * @param User $user
     * @param AclForm $aclForm
     * @param ManagerInterface $auth
     */
    public function syncRbacRoles(User $user, AclForm $aclForm, ManagerInterface $auth);

    /**
     * Assign Roles using given Manager
     *
     * @param array $roles
     * @param ManagerInterface $auth
     * @param int $id
     */
    public function assignRoles($roles, ManagerInterface $auth, $id);
}
