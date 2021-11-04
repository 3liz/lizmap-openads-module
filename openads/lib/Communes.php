<?php

namespace openADS;

class Communes
{
    // Function to transform the data into JSON
    public function initData($data)
    {
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

        return $data_result;
    }
}
