<?php

namespace openADS;

class Parcelles
{
    // Function to transform the data into JSON
    public function initData($data, $ids_list)
    {
        // array for parcelles found
        $parcelle_find = array();

        // formatted data array
        $data_result = array('parcelles' => array());

        // loop to retrieve the data of each parcelle
        foreach ($data as $line) {
            array_push($parcelle_find, $line->ident);

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

        // check number of sql data related to the requested parcelles
        if (count($parcelle_find) != count($ids_list)) {
            // adding in data result the parcelles not found in the database
            foreach ($ids_list as $id) {
                if (!in_array($id, $parcelle_find)) {
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

        return $data_result;
    }
}
