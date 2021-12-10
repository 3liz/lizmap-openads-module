<?php

namespace openADS;

class Communes
{
    protected $utils;
    protected $profile;
    protected $schema;
    protected $id_commune;

    public function __construct($utils, $profile, $schema, $id_commune)
    {
        $this->utils = $utils;
        $this->profile = $profile;
        $this->schema = $schema;
        $this->id_commune = $id_commune;
    }

    /**
     * Function to transform the data into JSON.
     *
     * @param array|string $data
     * @param string       $method
     *
     * @return array
     */
    public function formatData($data, $method)
    {
        if ($method == 'contraintes' && is_array($data)) {
            // formatted data array
            $data_result = array('contraintes' => array());

            // loop to retrieve the data of each constraints
            foreach ($data as $line) {
                array_push(
                    $data_result['contraintes'],
                    array(
                        'contrainte' => $line->id_contraintes,
                        'groupe_contrainte' => $line->groupe,
                        'sous_groupe_contrainte' => $line->sous_groupe,
                        'libelle' => $line->libelle,
                        'texte' => $line->texte,

                    )
                );
            }

            return array('200', 'success', $data_result);
        }

        return array('500', 'error', 'Error occured while formating data');
    }

    /**
     * Function to get SQL for given method.
     *
     * @param string     $method
     * @param null|array $body
     *
     * @return null|string $sql
     */
    protected function getSql($method)
    {
        $sql = null;
        if ($method == 'contraintes') {
            $sql = '
                SELECT DISTINCT c.id_contraintes, c.libelle, c.texte, c.groupe, c.sous_groupe
                FROM !schema!.contraintes c
                JOIN  !schema!.geo_contraintes gc ON c.id_contraintes=gc.id_contraintes
                WHERE gc.codeinsee = $1::text;
            ';
        }

        return $sql;
    }

    /**
     * Function to run some action in the database.
     *
     * @param string     $method
     * @param null|array $body
     *
     * @return array
     */
    protected function runDataBaseAction($method)
    {
        // Construct params for SQL query
        $params = array($this->id_commune);

        // Set messages
        $messages = array(
            'contraintes' => 'to get the contraints for this town',
        );
        $sql = $this->getSql($method);
        if (!$sql) {
            return array(
                '500',
                'error',
                'No SQL query found',
            );
        }
        list($status, $msgError, $data) = $this->utils->execQuery($sql, $this->schema, $this->profile, $messages[$method], $params);
        if ($status == 'error') {
            return array(
                '500',
                $status,
                $msgError,
            );
        }
        // check if data not null, it's for centroide and contraintes
        // if null id_dossier does not exists in database
        if (!$data) {
            return array(
                '500',
                'error',
                'No data found ' . $messages[$method],
            );
        }

        return array('200', 'success', $data);
    }

    /**
     * Function to use runDatabaseAction and formatData with given method.
     *
     * @param string     $method
     * @param null|array $body
     *
     * @return array
     */
    public function executeMethod($method = 'contraintes')
    {
        list($code, $status, $result) = $this->runDatabaseAction($method);
        if ($status == 'error') {
            return array(
                $code,
                $status,
                $result,
            );
        }

        // return format data for centroid response
        list($code, $status, $result) = $this->formatData($result, $method);
        if ($status == 'error') {
            return array(
                $code,
                $status,
                $result,
            );
        }

        return array('200', 'success', $result);
    }
}
