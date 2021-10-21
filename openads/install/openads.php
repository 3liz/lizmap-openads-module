<?php
/**
 * Entry point for OpenADS API.
 *
 * @author    3liz
 * @copyright 2018-2020 3liz
 *
 * @see      https://3liz.com
 *
 * @license   GPL 3
 */
require '../application.init.php';
require JELIX_LIB_CORE_PATH.'request/jClassicRequest.class.php';

checkAppOpened();

// mapping of url to basic url (/module/controller/method)

$mapping = array(
    'services/:projectKey/parcelles/:ids_parcelles' => array(
        'GET' => 'openads/parcelles/index',
    ),
    'services/:projectKey/communes/:id_commune/contraintes' => array(
        'GET' => 'openads/communes/contraintes',
    ),
    'services/:projectKey/dossiers/:id_dossier/emprise' => array(
        'POST' => 'openads/dossiers/emprise',
    ),
    'services/:projectKey/dossiers/:id_dossier/centroide' => array(
        'POST' => 'openads/dossiers/centroide',
    ),
    'services/:projectKey/dossiers/:id_dossier/contraintes' => array(
        'GET' => 'openads/dossiers/contraintes',
    ),
);

jApp::setCoord(new jCoordinator());
jApp::coord()->process(new \openADS\Request($mapping));
