<?php

include jApp::getModulePath('openads').'controllers/apiController.php';

class communesCtrl extends apiController
{
    public function contraintes()
    {
        // check authenticate
        // $auth_ok = $this->authenticate();
        // if (!$auth_ok) {
        //     return $this->apiResponse(
        //         '401',
        //         'error',
        //         'Access token is missing or invalid',
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
        $id_commune = trim($this->param('id_commune', '-1'));
        if ($id_commune == '-1') {
            $id_commune = '';
        }

        // Utils (sql class)
        $utils = new \openADS\Utils();

        // Get profile from layer communes
        $profile = $utils->getProfile($this->lizmap_project, 'communes');
        if (!$profile) {
            return $this->apiResponse(
                '404',
                'error',
                'profile not found for this project.',
            );
        }

        // Get schema from layer communes
        $schema = $utils->getSchema($this->lizmap_project, 'communes');
        if (!$schema) {
            return $this->apiResponse(
                '404',
                'error',
                'schema not found for this project.',
            );
        }

        // get data
        $data = $utils->getObjects(
            'commune',
            $schema,
            array($id_commune),
            $profile
        );
        if (!$data) {
            return $this->apiResponse(
                '404',
                'error',
                'data not found for given ids.',
            );
        }

        // Communes class
        $commune = new \openADS\Communes();

        // Format data into json
        $result = $commune->initData($data);

        // return result
        return $this->objectResponse($result);
    }
}
