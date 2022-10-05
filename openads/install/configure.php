<?php
/**
 * @author    3liz
 * @copyright 2022 3liz
 *
 * @see      https://3liz.com
 *
 * @license    GPL 3
 */

use Jelix\Routing\UrlMapping\EntryPointUrlModifier;
use \Jelix\Routing\UrlMapping\MapEntry\MapInclude;

/**
 * Configurator for Lizmap 3.6+/Jelix 1.8+
 */
class openadsModuleConfigurator extends \Jelix\Installer\Module\Configurator {

    public function getDefaultParameters()
    {
        return array();
    }


    public function declareUrls(EntryPointUrlModifier $registerOnEntryPoint)
    {
        $registerOnEntryPoint->havingName(
            'openads',
            array(
                new MapInclude('urls.xml')
            )
        )
        ;
    }

    public function getEntryPointsToCreate()
    {
        return array(
            new \Jelix\Installer\Module\EntryPointToInstall(
                'openads.php',
                'openads/config.ini.php',
                'openads.php',
                'config/config.ini.php'
            )
        );
    }


    function configure(\Jelix\Installer\Module\API\ConfigurationHelpers $helpers)
    {
    }
}