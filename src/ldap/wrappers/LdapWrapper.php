<?php

namespace templatemonster\ldapauth\wrappers;

class LdapWrapper
{
    /**
     * @var resource
     */
    public $ldapConnection;

    /**
     * @param string $host
     * @param string $port
     */
    public function __construct($host, $port)
    {
        $this->ldapConnection = ldap_connect($host, $port);
    }

    /**
     * Bind to LDAP directory
     *
     * @param string $dn
     * @param string $password
     * @return bool
     */
    public function bind($dn, $password)
    {
        try {
            return ldap_bind($this->ldapConnection, $dn, $password);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate search query on given filter and executes it
     *
     * @param string $filter
     * @param string $dn
     * @return array
     */
    public function search($filter, $dn)
    {
        $query = ldap_search($this->ldapConnection, $dn, $filter);
        return ldap_get_entries($this->ldapConnection, $query);
    }

}