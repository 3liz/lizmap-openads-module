<?php

include jApp::getModulePath('openads').'controllers/apiController.php';

class parcellesCtrl extends apiController
{
    public function index()
    {
        // check authenticate
        // $auth_ok = $this->authenticate();
        // if (!$auth_ok) {
        //     return $this->apiResponse(
        //         '401',
        //         'error',
        //         'Access denied, invalid login',
        //     );
        // }

        //check project
        list($code, $status, $message) = $this->checkProject();
        if ($status == 'error') {
            return $this->apiResponse(
                $code,
                $status,
                $message
            );
        }

        // split string ids to array
        $ids_parcelles = trim($this->param('ids_parcelles', '-1'));
        if ($ids_parcelles == '-1') {
            $ids_parcelles = '';
        }

        $ids_list = explode(';', $ids_parcelles);

        // Utils (sql class)
        $utils = new \openADS\Utils();

        // Get Profile for  database connexion
        $profile = $utils->getProfile($this->lizmap_project, 'parcelles');
        if (!$profile) {
            return $this->apiResponse(
                '404',
                'error',
                'profile not found for this project.',
            );
        }

        // Get schema for sql query
        $schema = $utils->getSchema($this->lizmap_project, 'parcelles');
        if (!$schema) {
            return $this->apiResponse(
                '404',
                'error',
                'schema not found for this project.',
            );
        }

        // Get Data
        $data = $utils->getObjects(
            'parcelles',
            $schema,
            $ids_list,
            $profile
        );
        if (!$data) {
            return $this->apiResponse(
                '404',
                'error',
                'data not found for given ids.'
            );
        }

        // Parcelles class
        $parcelle = new \openADS\Parcelles();

        // format data
        $result = $parcelle->initData($data, $ids_list);

        // return result
        return $this->objectResponse($result);
    }
}
