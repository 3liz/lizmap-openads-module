# lizmap-openads-module

Module [Lizmap](https://www.lizmap.com/) pour liaison avec le logiciel [OpenADS](https://www.atreal.fr/applications/catalogue/logiciel-urbanisme).

# Installation

## Module openADS pour Lizmap Web Client 

NB: tous les chemins ci-dessous sont relatifs au dossier de Lizmap Web Client.

* Copier le répertoire `openads` dans le répertoire `lizmap/lizmap-modules/` d'une instance Lizmap Web Client afin d'avoir un répertoire `lizmap/lizmap-modules/openads/` contenant les fichiers `module.xml`, `events.xml`, et d'autres répertoires.

* Puis modifier le fichier `lizmap/var/config/localconfig.ini.php` en ajoutant `openads.access=2` dans la section `[modules]`, de cette façon

```ini
[modules]
openads.access=2

```

* Ensuite exécuter l'installeur Lizmap

```bash
lizmap/install/set_rights.sh
lizmap/install/clean_vartmp.sh
php lizmap/install/installer.php
```

## Tester l'API
###Toutes les requêtes suivantes sont toutes tester via les [tests unitaires](tests/)

Vous pouvez ensuite tester avec `curl` et une authentification basique.
```bash
# EXEMPLE
curl -u username:password https://exemple.com
```

Dans les exemples suivant, nous utilisons comme ULR de base `http://lizmap.localhost/`:

* Define the API base URL:

```bash
BASEURL="http://lizmap.localhost/openads.php"
``` 

* Parcelles:

```bash
# PARCELLES
###

# Test avec mauvais mot de passe
RESULT=$(curl -s -X GET -H 'Content-Type: application/json' -u admin:badpassword "$BASEURL/services/openads~openads/parcelles/800016000AK0145") && echo $RESULT

# Test avec bon login et bons ids_parcelles
RESULT=$(curl -s -X GET -H 'Content-Type: application/json' -u admin:admin "$BASEURL/services/openads~openads/parcelles/800016000AK0145") && echo $RESULT
# Exemple de résultat
# {"parcelles":[{"parcelle":"800016000AK0145","existe":"true","adresse":{"numero_voie":"0057  ","type_voie":"CHE","nom_voie":"CROISE DE LA JUSTICE      ","arrondissement":"016"}}]}

# Test avec bon login et mauvais ids_parcelle
RESULT=$(curl -s -X GET -H 'Content-Type: application/json' -u admin:admin "$BASEURL/services/openads~openads/parcelles/80016") && echo $RESULT
# Exemple de résultat
# {"parcelles":[{"parcelle":"800016000A","existe":"false"}]}

# COMMUNES

# Test requête communes avec code insee
RESULT=$(curl -s -X GET -H 'Content-Type: application/json' -u admin:admin "$BASEURL/services/openads~openads/communes/80016/contraintes") && echo $RESULT
# Exemple de résultat
# {"contraintes":[{"contrainte":"64","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Uec","texte":"Secteur urbain \u00e9conomique"},{"contrainte":"63","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Uco","texte":"Secteur urbain de commerce"},{"contrainte":"51","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"1AUm","texte":"Zone \u00e0 urbaniser mixte habitat \/ \u00e9conomie"},{"contrainte":"57","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"2AUec","texte":"Zone \u00e0 urbaniser \u00e0 vocation \u00e9conomique"},{"contrainte":"36","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Up","texte":"Secteur urbain en p\u00e9riph\u00e9rie du centre-ville et des anciens faubourgs d'Albert"},{"contrainte":"37","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Nzh","texte":"Secteur naturel concern\u00e9 par des Zones \u00e0 Dominante Humide du SDAGE Artois-Picardie"},{"contrainte":"44","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"N","texte":"Zone naturelle"},{"contrainte":"43","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Uag","texte":"Secteur urbain avec enjeux agricoles"},{"contrainte":"33","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Ueq","texte":"Secteur urbain d'\u00e9quipements publics"},{"contrainte":"42","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"A","texte":"Zone agricole"},{"contrainte":"53","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Uc","texte":"Secteur urbain compos\u00e9 majoritairement d'extensions r\u00e9centes"},{"contrainte":"62","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Azh","texte":"Secteur agricole concern\u00e9 par des Zones \u00e0 Dominante Humide du SDAGE Artois-Picardie"},{"contrainte":"50","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"2AUh","texte":"Zone \u00e0 urbaniser \u00e0 vocation principale d'habitat"},{"contrainte":"52","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Ap","texte":"Secteur agricole prot\u00e9g\u00e9"},{"contrainte":"46","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"1AUco","texte":"Zone \u00e0 urbaniser \u00e0 vocation commerciale"},{"contrainte":"45","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Uf","texte":"Secteur urbain des anciens faubourgs autour du centre-ville d'Albert"},{"contrainte":"60","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Uv","texte":"Secteur urbain du centre-ville d'Albert"},{"contrainte":"34","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Neq","texte":"Secteur naturel d'\u00e9quipements publics"},{"contrainte":"41","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"1AUh","texte":"Zone \u00e0 urbaniser \u00e0 vocation principale d'habitat"}]}

# DOSSIERS

# Test du calcul de l'emprise
RESULT=$(curl -s -X POST -H 'Content-Type: application/json' -d '{"parcelles":["800016000AT0031", "800016000AO0179"]}' -u admin:admin "$BASEURL/services/openads~openads/dossiers/444444/emprise") && echo $RESULT
# Exemple de résultat
# {"emprise":{"statut_calcul_emprise":"true"}}

# Test du calcul du centroïde
RESULT=$(curl -s -X POST -H 'Content-Type: application/json' -u admin:admin "$BASEURL/services/openads~openads/dossiers/444444/centroide") && echo $RESULT
# Exemple de résultat
# {"centroide":{"statut_calcul_centroide":"true","x":"674251.814403417","y":"6988657.01009032"}}

# Test de récupération des contraintes
RESULT=$(curl -s -X GET -H 'Content-Type: application/json' -u admin:admin "$BASEURL/services/openads~openads/dossiers/444444/contraintes") && echo $RESULT
# Exemple de résultat
# {"contraintes":[{"contrainte":"36","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Up","texte":"Secteur urbain en p\u00e9riph\u00e9rie du centre-ville et des anciens faubourgs d'Albert"},{"contrainte":"45","groupe_contrainte":"Zonage","sous_groupe_contrainte":"ZOne urba","libelle":"Uf","texte":"Secteur urbain des anciens faubourgs autour du centre-ville d'Albert"}]}

```
