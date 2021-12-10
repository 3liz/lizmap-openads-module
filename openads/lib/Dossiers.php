<?php

namespace openADS;

class dossiers
{
    protected $utils;
    protected $profile;
    protected $schema;
    protected $id_dossier;

    public function __construct($utils, $profile, $schema, $id_dossier)
    {
        $this->utils = $utils;
        $this->profile = $profile;
        $this->schema = $schema;
        $this->id_dossier = $id_dossier;
    }

    /**
     * Function to transform the data into JSON.
     *
     * @param array|string $data
     * @param string       $function
     *
     * @return array
     */
    protected function formatData($data, $function = 'emprise')
    {
        if ($function == 'emprise' && ($data == 'true' || $data == 'false')) {
            return array(
                '200',
                'success',
                array(
                    $function => array(
                        'statut_calcul_emprise' => $data,
                    ),
                ),
            );
        }
        if ($function == 'centroide' && is_array($data)) {
            $result = array(
                $function => array(
                    'statut_calcul_centroide' => $data[0],
                ),
            );

            if ($data[0] == 'true') {
                $result[$function]['x'] = $data[1]->x;
                $result[$function]['y'] = $data[1]->y;
            }

            return array('200', 'success', $result);
        }
        if ($function == 'contraintes' && is_array($data)) {
            $result = array($function => array());

            foreach ($data as $line) {
                if (is_object($line)) {
                    array_push(
                        $result[$function],
                        array(
                            'contrainte' => $line->id_contraintes,
                            'groupe_contrainte' => $line->groupe,
                            'sous_groupe_contrainte' => $line->sous_groupe,
                            'libelle' => $line->libelle,
                            'texte' => $line->texte,

                        )
                    );
                }
            }

            return array('200', 'success', $result);
        }

        return array('500', 'error', 'Error occured while format data');
    }

    /**
     * Function to get SQL for given action.
     *
     * @param string     $action
     * @param null|array $body
     *
     * @return string $sql
     */
    protected function getSql($action = 'checkParcelles', $body = null)
    {
        $sql = null;
        if (is_array($body) && in_array($action, array('checkParcelles', 'insertDossier'))) {
            $params = '';
            for ($i = 1; $i <= count($body); ++$i) {
                if ($i == 1) {
                    $params .= '$' . $i;
                } else {
                    $params .= ',$' . $i;
                }
            }
            if ($action == 'checkParcelles') {
                // query to check if all parcelle exists and was in only one town
                // always return response
                $sql = "
                    WITH number_parcelles(numbr_parc) AS(
                        SELECT count(ident) FROM !schema!.parcelles
                        WHERE ident IN (${params})
                    ), parcelles_check AS(
                        SELECT 
                            CASE WHEN numbr_parc = " . count($body) . " THEN 'true'
                            ELSE 'false' 
                            END as find
                        FROM number_parcelles		
                    )
                    SELECT 
                        CASE 
                            WHEN find IS NOT NULL AND find = 'true' THEN
                                (SELECT distinct c.codeinsee 
                                FROM !schema!.communes c
                                JOIN !schema!.parcelles p ON st_intersects(c.geom,p.geom)
                                WHERE p.ident IN (${params})) 
                            ELSE 'false'
                        END as \"result\"
                    FROM parcelles_check;
                ";
            } elseif ($action == 'insertDossier') {
                // Upsert folder
                $param_id = '$' . (count($body) + 1);
                $sql = "
                INSERT INTO !schema!.dossiers_openads(numero, parcelles, codeinsee, dossier_importe_geosig, geom)
                    VALUES(${param_id}::text, ARRAY[${params}], 
                        (SELECT codeinsee FROM !schema!.communes c JOIN !schema!.parcelles p ON ST_INTERSECTS(p.geom, c.geom) WHERE p.ident IN (${params}) LIMIT 1),
                        (SELECT COUNT(numero) > 0 FROM !schema!.dossiers_sig WHERE numero = ${param_id}::text),
                        (SELECT ST_Union(geom) FROM !schema!.parcelles WHERE ident IN (${params}))
                    )
                ON CONFLICT (numero) DO UPDATE SET
                    parcelles = ARRAY[${params}],
                    geom = (SELECT ST_Union(geom) FROM !schema!.parcelles WHERE ident IN (${params}))
                    WHERE !schema!.dossiers_openads.numero = ${param_id}::text
                RETURNING numero;
                ";
            }
        } elseif ($action == 'centroide') {
            // create centroide for a folder
            $sql = '
                UPDATE openads.dossiers_openads SET x=ST_X(ST_CENTROID(geom)), y=ST_Y(ST_CENTROID(geom))
                WHERE numero=$1::text RETURNING x,y;         
            ';
        } elseif ($action == 'contraintes') {
            // get constraints for a folder
            $sql = '
                SELECT DISTINCT c.id_contraintes, c.libelle, c."texte", c.groupe, c.sous_groupe
                FROM openads.contraintes c
                JOIN  openads.geo_contraintes gc ON c.id_contraintes=gc.id_contraintes
                JOIN openads.dossiers_openads d ON ST_INTERSECTS(d.geom, gc.geom)
                JOIN openads.communes com ON com.codeinsee=gc.codeinsee
                WHERE d.numero = $1::text;
            ';
        }

        return $sql;
    }

    /**
     * Function to run some action in the database.
     *
     * @param string     $action
     * @param null|array $body
     *
     * @return array
     */
    protected function runDatabaseAction($action, $body = null)
    {
        // Construct params for SQL query
        $params = array();
        if ($action == 'checkParcelles') {
            $params = $body;
        } elseif ($action == 'insertDossier') {
            $params = array_merge($params, $body);
            $params[] = $this->id_dossier;
        } elseif (in_array($action, array('centroide', 'contraintes'))) {
            $params[] = $this->id_dossier;
        }

        // Set messages
        $messages = array(
            'checkParcelles' => 'to check the parcelles',
            'insertDossier' => 'to create/modify the dossier',
            'centroide' => 'to calculate the centroid',
            'contraintes' => 'to get constraints for given dossier',
        );

        $sql = $this->getSql($action, $body);
        if (!$sql) {
            return array(
                '500',
                'error',
                'Error to find SQL Query',
            );
        }

        list($status, $msgError, $data) = $this->utils->execQuery($sql, $this->schema, $this->profile, $messages[$action], $params);
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
                'Error while executing query ' . $messages[$action],
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
    public function executeMethod($method, $body = null)
    {
        list($code, $status, $result) = $this->runDatabaseAction($method, $body);
        if ($status == 'error') {
            return array(
                $code,
                $status,
                $result,
            );
        }

        if ($method == 'checkParcelles') {
            $method = 'emprise';
            if (count($result) != 1 || $result[0]->result == 'false') {
                // return format data for centroid response
                $result = 'false';
            } else {
                list($code, $status, $result) = $this->runDatabaseAction('insertDossier', $body);
                if ($status == 'error') {
                    return array(
                        $code,
                        $status,
                        $result,
                    );
                }
                $result = 'true';
            }
        } elseif ($method == 'centroide') {
            // check if only one row was return
            $data = array('false');
            if (count($result) == 1 && $result[0]->x && $result[0]->y) {
                $data[0] = 'true';
                $data[] = $result[0];
            }
            $result = $data;
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
