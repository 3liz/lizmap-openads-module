<?php

namespace openADS;

class Parcelles
{
    protected $utils;
    protected $profile;
    protected $schema;
    protected $ids_list;

    public function __construct($utils, $profile, $schema, $ids_list)
    {
        $this->utils = $utils;
        $this->profile = $profile;
        $this->schema = $schema;
        $this->ids_list = $ids_list;
    }

    /**
     * Function to transform the data into JSON
     *
     * @param Array|string  $data
     * @param string        $method
     *
     * @return Array
     */
    public function formatData($data, $method='index')
    {
        if ($method == 'index' && is_array($data)) {
            // array for parcelles found
            $parcelle_found = array();

            // formatted data array
            $data_result = array('parcelles' => array());

            // loop to retrieve the data of each parcelle
            foreach ($data as $line) {
                if (is_object($line)) {
                    array_push($parcelle_found, $line->ident);

                    // Format numero
                    $numero = $line->ndeb;
                    if (!empty($line->sdeb)) {
                        $numero .= ' '.$line->sdeb;
                    }

                    array_push(
                        $data_result['parcelles'],
                        array(
                            'parcelle' => $line->ident,
                            'existe' => 'true',
                            'adresse' => array(
                                'numero_voie' => $numero,
                                'type_voie' => $line->type,
                                'nom_voie' => $line->nom,
                                'arrondissement' => $line->ccocom,

                            ),
                        )
                    );
                }
            }

            // check number of sql data related to the requested parcelles
            if (count($parcelle_found) != count($this->ids_list)) {
                // adding in data result the parcelles not found in the database
                foreach ($this->ids_list as $id) {
                    if (!in_array($id, $parcelle_found)) {
                        array_push(
                            $data_result['parcelles'],
                            array(
                                'parcelle' => $id,
                                'existe' => 'false',
                            )
                        );
                    }
                }
            }

            return array('200', 'success', $data_result);
        }

        return array('500', 'error' , 'Error occured while formating data');
    }

    /**
     * Function to get SQL for given method
     *
     * @param string       $method
     * @param Array|null   $body
     *
     * @return string|null      $sql
     */
    protected function getSql($method='index')
    {
        $sql = null;
        if (is_array($this->ids_list) && $method == 'index') {
            $params = '';
            for ($i = 1; $i <= count($this->ids_list); $i++) {
                if ($i == 1) {
                    $params .= '$' . $i;
                } else {
                    $params .= ',$' . $i;
                }
            }
            $sql = "
                SELECT ident, ndeb, sdeb, type, nom, ccocom
                FROM !schema!.parcelles
                WHERE ident IN ($params);
            ";
        }
        return $sql;
    }

    /**
     * Function to run some action in the database
     *
     * @param string       $method
     * @param Array|null   $body
     *
     * @return Array
     */
    protected function runDataBaseAction($method='index')
    {
        // Construct params for SQL query
        $params = $this->ids_list;

        // Set messages
        $messages = array(
            'index' => 'to get the data parcelles',
        );
        $sql = $this->getSql($method);
        if (!$sql) {
            return array(
                '500',
                'error',
                'No SQL query found'
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
        // if (!$data) {
        //     return array(
        //         '500',
        //         'error',
        //         'Error while executing query ' . $messages[$method]
        //     );
        // }

        return array('200', 'success', $data);
    }

    /**
     * Function to use runDatabaseAction and formatData with given method
     *
     * @param string       $method
     * @param Array|null   $body
     *
     * @return Array
     */
    public function executeMethod($method)
    {
        list($code, $status, $result) =  $this->runDatabaseAction($method);
        if ($status == 'error') {
            return array(
                $code,
                $status,
                $result
            );
        }

        // return format data for centroid response
        list($code, $status, $result) = $this->formatData($result, $method);
        if ($status == 'error') {
            return array(
                $code,
                $status,
                $result
            );
        }

        return array('200', 'success', $result);
    }
}
