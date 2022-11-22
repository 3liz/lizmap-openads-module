<?php

/**
 * @author    3liz
 * @copyright 2021 3liz
 *
 * @see      https://3liz.com
 *
 * @license    Mozilla Public Licence
 */
class openadsListener extends jEventListener
{
    public function ongetMapAdditions($event)
    {
        // Vérification que le repository et le project correspondent à un projet lizmap
        // et que le projet s'appelle 'openads'
        $repository = $event->repository;
        $project = $event->project;
        $p = lizmap::getProject($repository . '~' . $project);
        if (!$p || $project !== 'openads') {
            \jLog::log('Le module openads nécessite que le projet se nomme "openads"', 'error');

            return;
        }

        $jscode = array();

        if (method_exists($p, 'getCustomProjectVariables')) {
            $customProjectVariables = $p->getCustomProjectVariables();
            if ($customProjectVariables && array_key_exists('openads_url_ads', $customProjectVariables)) {
                $jscode = array('const openads_url_ads = "' . $customProjectVariables['openads_url_ads'] . '";');
            } else {
                $jscode = array('console.warn(`La variable "openads_url_ads" doit être définie dans votre projet QGIS.`);');
            }
        } else {
            \jLog::log('Le module openads nécessite Lizmap 3.5.0 ou 3.4.8 minimum', 'error');

            return;
        }

        $js = array(jUrl::get('jelix~www:getfile', array('targetmodule' => 'openads', 'file' => 'openads.js')));

        $event->add(
            array(
                'js' => $js,
                'jscode' => $jscode,
            )
        );
    }
}
