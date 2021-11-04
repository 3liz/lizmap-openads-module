<?php
/**
 * @author    3liz
 * @copyright 2021 3liz
 *
 * @see      http://3liz.com
 *
 * @license Mozilla Public License : http://www.mozilla.org/MPL/
 */

namespace openADS;

class Utils
{
    private $sql = array(
        'parcelles' => array(
            'get' => '
                SELECT ident, ndeb, sdeb, type, nom, ccocom
                FROM !schema!.parcelles
                WHERE ident IN
            ',
            // 'add' => '
            //     INSERT INTO openads.parcelles (
            //         ...
            //     )
            //     VALUES ($1)
            //     RETURNING id_parcelles
            // ',
        ),
    );

    // Query database and return json data
    private function query($sql, $params = null, $profile = 'openads')
    {
        $cnx = \jDb::getConnection($profile);
        $cnx->beginTransaction();

        try {
            $resultset = $cnx->prepare($sql);
            $resultset->execute($params);
            $data = $resultset->fetchAll();
            $cnx->commit();
        } catch (Exception $e) {
            $cnx->rollback();
            $data = null;
        }

        return $data;
    }

    /**
     * Get a openads object.
     *
     * @param string $key        The object to get. It corresponds to the table name. Ex: parcelles
     * @param mixed  $get_params Parameters needed for the get SQL
     * @param mixed  $schema
     * @param mixed  $profile
     * @param mixed  $method
     *
     * @return array or null
     */
    public function getObjects($key, $schema, $get_params = null, $profile = 'openads', $method = 'get')
    {
        // Get object
        $sql = $this->sql[$key][$method];
        $sql = str_replace('!schema!', $schema, $sql);
        if ($key == 'parcelles') {
            $sql .= '(';
            for ($i = 0; $i < count($get_params); ++$i) {
                $numParam = $i + 1;
                if ($i == 0) {
                    $sql .= '$'.$numParam;
                } else {
                    $sql .= ',$'.$numParam;
                }
            }
            $sql .= ')';
        }
        $data = $this->query($sql, $get_params, $profile);
        if (!is_array($data)) {
            return null;
        }

        return $data;
    }

    /**
     * Get profile from project and layer name.
     *
     * @param object $lizmap_project Lizmap Project
     * @param string $layerName      Layer Name
     *
     * @return string or null
     */
    public function getProfile($lizmap_project, $layerName)
    {
        // Get layer
        $qgisLayer = $this->getLayer($lizmap_project, $layerName);
        if (!$qgisLayer) {
            return null;
        }
        // get profile
        return $qgisLayer->getDatasourceProfile();
    }

    /**
     * Get layer from project and layer name.
     *
     * @param object $lizmap_project Lizmap Project
     * @param string $layerName      Layer Name
     *
     * @return object or null
     */
    public function getLayer($lizmap_project, $layerName)
    {
        if (!$lizmap_project) {
            return null;
        }
        $layer = $lizmap_project->findLayerByName($layerName);
        if (!$layer) {
            return null;
        }
        $layerId = $layer->id;
        $qgisLayer = $lizmap_project->getLayer($layerId);
        if (!$qgisLayer) {
            return null;
        }

        return $qgisLayer;
    }

    /**
     * Get schema from project and layer name.
     *
     * @param object $lizmap_project Lizmap Project
     * @param string $layerName      Layer Name
     *
     * @return string or null
     */
    public function getSchema($lizmap_project, $layerName)
    {
        // Check project layers and schema
        $schema = null;

        // Get layer
        $qgisLayer = $this->getLayer($lizmap_project, $layerName);
        if (!$qgisLayer) {
            return null;
        }

        $params = $qgisLayer->getDatasourceParameters();
        $schema = $params->schema;
        if (!$schema) {
            return null;
        }

        return $schema;
    }
}
