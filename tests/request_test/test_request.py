#! /usr/bin/env python

from pathlib import Path

import json
import unittest
import requests


class TestRequests(unittest.TestCase):

    # noinspection PyPep8Naming
    def __init__(self, methodName="runTest"):
        super().__init__(methodName)
        self.base_url = 'http://localhost:9090/openads.php/'

    def setUp(self) -> None:
        pass

    def test_parcelles_requests(self):
        """ Tests requests parcelles"""

        url_test = 'services/openads~openads/parcelles/800016000AK0145'
        req = requests.get(self.base_url + url_test, auth=('admin', 'admin'))
        self.assertEqual(req.status_code, 200)

        check_response = {
            "parcelles": [{
                "parcelle": "800016000AK0145",
                "existe": "true",
                "adresse": {
                    "numero_voie": "0057  ",
                    "type_voie": "CHE",
                    "nom_voie": "CROISE DE LA JUSTICE      ",
                    "arrondissement": "016"
                }
            }]
        }

        self.assertDictEqual(json.loads(req.text), check_response)

        url_test = 'services/openads~openads/parcelles/80016'
        req = requests.get(self.base_url + url_test, auth=('admin', 'admin'))
        self.assertEqual(req.status_code, 200)

        check_response = {"parcelles": [{"parcelle": "80016", "existe": "false"}]}

        self.assertDictEqual(json.loads(req.text), check_response)

    def test_communes_requests(self):
        """ Tests requests communes"""

        # Opening JSON file
        with open('response_request/commune_contraintes.json') as json_file:
            data = json.load(json_file)

        url_test = 'services/openads~openads/communes/80016/contraintes'
        req = requests.get(self.base_url + url_test, auth=('admin', 'admin'))
        self.assertEqual(req.status_code, 200)

        self.assertDictEqual(json.loads(req.text), data)
        json_file.close()

    def test_dossiers_requests(self):
        """ Tests requests dossiers"""

        url_test = 'services/openads~openads/dossiers/44444/emprise'
        params = {"parcelles":["800016000AT0031", "800016000AO0179"]}
        req = requests.post(self.base_url + url_test, json=params, auth=('admin', 'admin'))
        self.assertEqual(req.status_code, 200)

        check_response = {"emprise": {"statut_calcul_emprise": "true"}}
        self.assertDictEqual(json.loads(req.text), check_response)

        url_test = 'services/openads~openads/dossiers/44444/centroide'
        req = requests.post(self.base_url + url_test, auth=('admin', 'admin'))
        self.assertEqual(req.status_code, 200)

        check_response = {
            "centroide": {
                "statut_calcul_centroide": "true",
                "x": "674251.814403417",
                "y": "6988657.01009031"
            }
        }
        self.assertDictEqual(json.loads(req.text), check_response)

        # Opening JSON file
        with open('response_request/dossiers_contraintes.json') as json_file:
            data = json.load(json_file)

        url_test = 'services/openads~openads/dossiers/44444/contraintes'
        req = requests.get(self.base_url + url_test, auth=('admin', 'admin'))
        self.assertEqual(req.status_code, 200)

        self.assertDictEqual(json.loads(req.text), data)


if __name__ == "__main__":
    unittest.main()
