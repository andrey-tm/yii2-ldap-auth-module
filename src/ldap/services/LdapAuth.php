<?php

namespace templatemonster\ldapauth\services;

use templatemonster\ldapauth\wrappers\LdapWrapper;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\rbac\ManagerInterface;
use common\models\User;
use templatemonster\ldapauth\models\Acl;
use templatemonster\ldapauth\models\AclForm;

class LdapAuth extends Component implements LdapAuthInterface
{
    const OBJECT_CLASS_FILTER = '(objectclass=*)';

    /**
     * @var string
     */
    public $baseDN = '';

    /**
     * @var string
     */
    public $userDN = '';

    /**
     * @var string
     */
    public $groupDN = '';

    /**
     * @var LdapWrapper
     */
    public $ldap;

    /**
     * @var null|array
     */
    private $rawGroupsList = null;

    /**
     * @inheritdoc
     */
    public function authenticate($username, $password)
    {
        return $this->ldap->bind($this->generateUserDn($username), $password);
    }

    /**
     * @inheritdoc
     */
    public function getGroupsList()
    {
        $result = array();
        foreach ($this->getRawGroupsList() as $item) {
            if (isset($item['cn'][0])) {
                $groupName = $item['cn'][0];

                if (!in_array($groupName, $result)) {
                    $result[$groupName] = $groupName;
                }
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getGroupsOfUser($username)
    {
        $result = array();
        $groups = $this->getRawGroupsList();

        foreach ($groups as $group) {
            if (
                !empty($group['cn'][0]) &&
                isset($group['memberuid']) &&
                is_array($group['memberuid']) &&
                in_array($username, $group['memberuid'])
            ) {
                $result[] = $group['cn'][0];
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getUserEmail($username)
    {
        $userInfo = $this->ldap->search("cn=$username", $this->baseDN);
        return ArrayHelper::getValue($userInfo, [0, 'mail', 0], "$username@example.com");
    }

    /**
     * @inheritdoc
     */
    public function syncRbacRoles(User $user, AclForm $aclForm, ManagerInterface $auth)
    {
        $auth->revokeAll($user->id);
        $userGroups = $this->getGroupsOfUser($user->username);
        foreach ($userGroups as $group) {
            if ($acl = Acl::findOne(['ldap_group' => $group])) {
                $this->assignRoles($acl, $aclForm, $auth, $user->id);
            }
        }
    }

    /**
     * Assign Roles to user
     *
     * @param Acl $acl
     * @param AclForm $aclForm
     * @param ManagerInterface $auth
     * @param int $userId
     */
    private function assignRoles(Acl $acl, AclForm $aclForm, ManagerInterface $auth, $userId)
    {
        $aclForm->setModel($acl);
        if (is_array($aclForm->roles)) {
            foreach ($aclForm->roles as $role) {
                $auth->revoke($auth->getRole($role), $userId);
                $auth->assign($auth->getRole($role), $userId);
            }
        }
    }

    /**
     * Returns a raw list of available groups in ldap
     *
     * @return array
     */
    private function getRawGroupsList()
    {
        if (is_null($this->rawGroupsList)) {
            $this->rawGroupsList = $this->ldap->search(self::OBJECT_CLASS_FILTER, $this->groupDN);
        }

        return $this->rawGroupsList;
    }

    /**
     * Replace macros {$username} with given username value
     *
     * @param string $username
     * @return string
     */
    private function generateUserDn($username)
    {
        return str_replace('{$username}', $username, $this->userDN);
    }
}
