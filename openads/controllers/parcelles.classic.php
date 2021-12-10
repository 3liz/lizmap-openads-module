<?php

include jApp::getModulePath('openads') . 'controllers/apiController.php';

class parcellesCtrl extends apiController
{
    private $ids_list;
    private $parcelle;
    private $layer_name = 'parcelles';

    // Define function check to group all checks
    private function check()
    {
        // authenticate
        $auth_ok = $this->authenticate();
        // check authenticate
        // if (!$auth_ok) {
        //     return array(
        //         '401',
        //         'error',
        //         'Access denied, invalid login',
        //     );
        // }

        //check project
        list($code, $status, $message) = $this->checkProject();
        if ($status == 'error') {
            return array(
                $code,
                $status,
                $message,
            );
        }

        // split string ids to array
        $ids_parcelles = trim($this->param('ids_parcelles', '-1'));
        if ($ids_parcelles == '-1') {
            return array(
                '400',
                'error',
                'wrong parameter ids_parcelles',
            );
        }

        $this->ids_list = explode(';', $ids_parcelles);
        if (empty($this->ids_list) || !is_array($this->ids_list)) {
            return array(
                '404',
                'error',
                'Ids given in URL wasn\'t found',
            );
        }

        // Utils (sql class)
        $utils = new \openADS\Utils();

        // Get Profile for  database connexion
        $profile = $utils->getProfile($this->lizmap_project, $this->layer_name);
        if (!$profile) {
            return array(
                '404',
                'error',
                'profile not found for this project.',
            );
        }

        // Get schema for sql query
        $schema = $utils->getSchema($this->lizmap_project, $this->layer_name);
        if (!$schema) {
            return array(
                '404',
                'error',
                'schema not found for this project',
            );
        }

        // Parcelles class
        $this->parcelle = new \openADS\Parcelles($utils, $profile, $schema, $this->ids_list);

        return array('200', 'success', 'Project is a valid OpenADS project');
    }

    /**
     * Get parcelles info from given parcelles ids.
     *
     * @return jResponseJson
     */
    public function index()
    {
        //check project
        list($code, $status, $message) = $this->check();
        if ($status == 'error') {
            return $this->apiResponse(
                $code,
                $status,
                $message
            );
        }

        list($code, $status, $result) = $this->parcelle->executeMethod('index');
        if ($status == 'error') {
            return $this->apiResponse(
                $code,
                $status,
                $result
            );
        }

        // return result
        return $this->objectResponse($result);
    }
}
