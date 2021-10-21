<?php
/**
 * @author    3liz
 * @copyright 2011-2021 3liz
 *
 * @see      https://3liz.com
 *
 * @license    GPL 3
 */
class openadsModuleInstaller extends jInstallerModule
{
    public function install()
    {

        // Copy entry point
        // Needed in the upgrade process
        // if the variable $mapping has changed
        $www_path = jApp::wwwPath('openads.php');
        $this->copyFile('openads.php', $www_path);
    }
}
