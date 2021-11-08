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
RESULT=$(curl -s -X GET -H 'Content-Type: application/json' -u admin:badpassword "$BASEURL/services/openads~openads/parcelles/800016%20%20%200AK0145") && echo $RESULT

# Test avec bon login et bons ids_parcelles
RESULT=$(curl -s -X GET -H 'Content-Type: application/json' -u admin:admin "$BASEURL/services/openads~openads/parcelles/8000160000AK0145") && echo $RESULT

# Test avec bon login et mauvais ids_parcelle
RESULT=$(curl -s -X GET -H 'Content-Type: application/json' -u admin:admin "$BASEURL/services/openads~openads/parcelles/800016") && echo $RESULT

# COMMUNES

# Test requête communes avec code insee
RESULT=$(curl -s -X GET -H 'Content-Type: application/json' -u admin:admin "$BASEURL/services/openads~openads/communes/80016/contraintes") && echo $RESULT

```
