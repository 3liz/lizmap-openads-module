<?php

include \jApp::getModulePath('openads') . 'controllers/apiController.php';

class dossiersCtrl extends apiController
{
    private $id_dossier;

    private $layer_name = 'dossiers_openads';

    private $dossier;

    private $method;

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

        // get id dossier
        $id_dossier = trim($this->param('id_dossier', '-1'));
        if ($id_dossier == '-1') {
            return array(
                '400',
                'error',
                'wrong id_dossier',
            );
        }

        // Utils (sql class)
        $utils = new \openADS\Utils();

        // Get Profile for  database connexion
        $profile = $utils->getProfile($this->lizmap_project, $this->layer_name);
        if (!$profile) {
            return $this->apiResponse(
                '404',
                'error',
                'profile not found for this project.',
            );
        }

        // Get schema for sql query
        $schema = $utils->getSchema($this->lizmap_project, $this->layer_name);
        if (!$schema) {
            return $this->apiResponse(
                '404',
                'error',
                'schema not found for this project',
            );
        }

        // set id_dossier
        $this->id_dossier = $this->param('id_dossier');

        // Define class Dossiers
        $this->dossier = new \openADS\Dossiers($utils, $profile, $schema, $this->id_dossier);

        return array('200', 'success', 'Project is a valid OpenADS project');
    }

    /**
     * Calcul extent of a folder.
     *
     * @httpparam string Parcelles data in JSON
     *
     * @return jResponseJson
     */
    public function emprise()
    {
        // exec check()
        list($code, $status, $message) = $this->check();
        if ($status == 'error') {
            return $this->apiResponse(
                $code,
                $status,
                $message
            );
        }

        // set method used in class to sql and format data
        $this->method = 'emprise';

        // Get http POST parameters
        $body = $this->request->readHttpBody();

        try {
            $params = json_decode($body);
        } catch (\Exception $e) {
            return $this->apiResponse(
                '400',
                'error',
                'POST body is not JSON'
            );
        }

        // check if parcelles is define in params
        if (!property_exists($params, 'parcelles')) {
            if ($status == 'error') {
                return $this->apiResponse(
                    '400',
                    'error',
                    'Bad request, missing property parcelles'
                );
            }
        }

        // use query to check if all parcelles exist and if all was in only one town
        list($code, $status, $result) = $this->dossier->executeMethod('insertDossier', $params->parcelles);
        if ($status == 'error') {
            return $this->apiResponse(
                $code,
                $status,
                $result
            );
        }

        return $this->objectResponse($result);
    }

    /**
     * Calcul centroid of a folder.
     *
     * @return jResponseJson
     */
    public function centroide()
    {
        // exec check()
        list($code, $status, $message) = $this->check();
        if ($status == 'error') {
            return $this->apiResponse(
                $code,
                $status,
                $message
            );
        }

        // set method used in class to sql and format data
        $this->method = 'centroide';

        // get data
        list($code, $status, $result) = $this->dossier->executeMethod($this->method);
        if ($status == 'error') {
            return $this->apiResponse(
                $code,
                $status,
                $result
            );
        }

        // return format data for centroid response
        return $this->objectResponse($result);
    }

    /**
     * Get constraints concerning a folder.
     *
     * @return jResponseJson
     */
    public function contraintes()
    {
        // exec check()
        list($code, $status, $message) = $this->check();
        if ($status == 'error') {
            return $this->apiResponse(
                $code,
                $status,
                $message
            );
        }

        // set method used in class to sql and format data
        $this->method = 'contraintes';

        // get data
        list($code, $status, $result) = $this->dossier->executeMethod($this->method);
        if ($status == 'error') {
            return $this->apiResponse(
                $code,
                $status,
                $result
            );
        }

        // return format data for centroid response
        return $this->objectResponse($result);
    }
}
