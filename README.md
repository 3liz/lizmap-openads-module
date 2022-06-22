# Lizmap OpenADS

[![Tests 🎳](https://github.com/3liz/lizmap-openads-module/actions/workflows/php_lint.yml/badge.svg)](https://github.com/3liz/lizmap-openads-module/actions/workflows/php_lint.yml)
[![Packagist](https://img.shields.io/packagist/v/lizmap/lizmap-openads-module)](https://packagist.org/packages/lizmap/lizmap-openads-module)

Module [Lizmap](https://www.lizmap.com/) pour une liaison avec le logiciel [OpenADS](https://www.atreal.fr/applications/catalogue/logiciel-urbanisme).

Il nécessite Lizmap 3.5 ou version suivantes.

## Installation

Il est recommandé d'installer le module avec [Composer](https://getcomposer.org),
le gestionnaire de paquet pour PHP.
Si vous ne pouvez pas l'utiliser, utilisez la méthode manuelle indiquée plus bas.

NB : tous les chemins ci-dessous sont relatifs au dossier de Lizmap Web Client.

### Installation automatique avec Composer

* Dans `lizmap/my-packages`, créer le fichier `composer.json` si il n'existe pas déjà,
  en copiant le fichier `composer.json.dist`, puis installer le module avec Composer :

```bash
cp -n lizmap/my-packages/composer.json.dist lizmap/my-packages/composer.json
composer require --working-dir=lizmap/my-packages "lizmap/lizmap-openads-module"
```

* Si vous utilisez Lizmap 3.6 et suivante, exécuter ensuite la commande de configuration :

```bash
php lizmap/install/configurator.php
```

* Lancez enfin l'installation du module :

```bash
php lizmap/install/installer.php
./lizmap/install/clean_vartmp.sh
./lizmap/install/set_rights.sh
```

Go to the "Configuration" section.

### Installation manuelle, sans Composer

* Téléchargez l'archive sur la [page des version dans Github](https://github.com/3liz/lizmap-openads-module/releases).
* Extrayez les fichiers de l'archive et copier le répertoire `openads` dans `lizmap/lizmap-modules/`.


* Si vous utilisez Lizmap 3.5, éditez le fichier `lizmap/var/config/localconfig.ini.php` pour ajouter 
  dans la section `[modules]`

```ini
openads.access=2
```

* Si vous utilisez Lizmap 3.6, lancez la commande 

```bash
php lizmap/install/configurator.php
```

* Pour toutes versions de Lizmap, lancez l'installateur :

```bash
php lizmap/install/installer.php
./lizmap/install/clean_vartmp.sh
./lizmap/install/set_rights.sh
```

## Tester l'API

**Toutes les requêtes suivantes sont testables via les [tests unitaires](tests/)**

Vous pouvez ensuite tester avec `curl` et une authentification basique.
```bash
# EXEMPLE
curl -u username:password https://exemple.com
```

Dans les exemples suivants, nous utilisons comme URL de base `http://lizmap.localhost/`:

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
