<?php
/**
* @package   lizmap
* @subpackage openads
* @author    your name
* @copyright 2011-2021 3liz
* @link      http://3liz.com
* @license    All rights reserved
*/


class openadsModuleInstaller extends jInstallerModule {

    function install() {
        //if ($this->firstDbExec())
        //    $this->execSQLScript('sql/install');

        /*if ($this->firstExec('acl2')) {
            jAcl2DbManager::addSubject('my.subject', 'openads~acl.my.subject', 'subject.group.id');
            jAcl2DbManager::addRight('admins', 'my.subject'); // for admin group
        }
        */
    }
}