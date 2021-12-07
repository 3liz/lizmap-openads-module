<?php

include jApp::getModulePath('openads').'controllers/apiController.php';

class communesCtrl extends apiController
{
    protected $id_commune;
    protected $commune;
    private $layer_name = 'communes';

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
                $message
            );
        }

        // get id dossier
        $this->id_commune = trim($this->param('id_commune', '-1'));
        if ($this->id_commune == '-1') {
            return array(
                '400',
                'error',
                'wrong id_commune',
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
        $this->commune = new \openADS\Communes($utils, $profile, $schema, $this->id_commune);

        return array('200', 'success', 'Project is a valid OpenADS project');
    }

    /**
     * Get constraints info from given town id
     *
     * @return jResponseJson
     */
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
        list($code, $status, $message) = $this->check();
        if ($status == 'error') {
            return $this->apiResponse(
                $code,
                $status,
                $message
            );
        }

        list($code, $status, $result) = $this->commune->executeMethod('contraintes');
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
