<?php

class apiController extends jController
{
    protected $error_codes = array(
        'error' => 0,
        'success' => 1,
    );

    protected $http_codes = array(
        '200' => 'Successfull operation',
        '401' => 'Unauthorize',
        '400' => 'Bad Request',
        '403' => 'Forbidden',
        '404' => 'Not found',
        '405' => 'Method Not Allowed',
        '500' => 'Internal Server Error',
    );

    protected $user;

    protected $lizmap_project;

    /**
     * Authenticate the user via JWC token
     * Token is given in Authorization header as: Authorization: Bearer <token>.
     */
    protected function authenticate()
    {
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            return jAuth::login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        }
    }

    // Check if the given project in parameter is valid and accessible
    protected function checkProject()
    {
        // Check projectKey parameter
        $project_key = $this->param('projectKey');
        if (!$project_key) {
            return array(
                '400',
                'error',
                'The projectKey parameter is mandatory',
            );
        }

        // Check project name is openads
        if (strtolower(explode($project_key, '~')[1]) !== 'openads') {
            return array(
                '404',
                'error',
                'The project name must be openads',
            );
        }

        // Check project is valid
        try {
            $lizmap_project = lizmap::getProject($project_key);
            if (!$lizmap_project) {
                return array(
                    '404',
                    'error',
                    'The given project key does not refer to a known project',
                );
            }
        } catch (UnknownLizmapProjectException $e) {
            return array(
                '404',
                'error',
                'The given project key does not refer to a known project',
            );
        }

        // Check the authenticated user can access to the project
        if (!$lizmap_project->checkAcl($this->user->login)) {
            return array(
                '403',
                'error',
                jLocale::get('view~default.repository.access.denied'),
            );
        }

        // Set lizmap project property
        $this->lizmap_project = $lizmap_project;

        // Ok
        return array('200', 'success', 'Project is a valid OpenADS project');
    }
}