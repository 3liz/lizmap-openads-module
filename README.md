# lizmap-openads-module

Module [Lizmap](https://www.lizmap.com/) pour liaison avec le logiciel [OpenADS](https://www.atreal.fr/applications/catalogue/logiciel-urbanisme).

# Installation

## Lizmap Web Client openADS module

NB: all the path given are relative to your Lizmap Web Client instance folder.

* Copy the `openads` directory inside the `lizmap/lizmap-modules/` of a working Lizmap Web Client instance to have a new `lizmap/lizmap-modules/openads/` folder containing the files `module.xml`, `events.xml`, and folders.

* Then modify the file `lizmap/var/config/localconfig.ini.php` to add `openads.access=2` in the `[modules]` section, such as

```ini
[modules]
openads.access=2

```

* Then you need to run the Lizmap installer

```bash
lizmap/install/set_rights.sh
lizmap/install/clean_vartmp.sh
php lizmap/install/installer.php
```

## Test the API

Then you are ready to test. For example with curl (you can pass basic credentials to curl command easily).
```bash
# EXEMPLE
curl -u username:password https://example.com
```

In the following examples, we use `http://lizmap.localhost/` as the base URL:

* Define the API base URL:

```bash
BASEURL="http://lizmap.localhost/openads.php"
``` 

* Parcelles:

```bash
# PARCELLES
###

# Test with bad login
RESULT=$(curl -s -X GET -H 'Content-Type: application/json' -u admin:admi "$BASEURL/services/openads~openads/parcelles/800016%20%20%200AK0145") && echo $RESULT

# Test with good login and good ids_parcelles
RESULT=$(curl -s -X GET -H 'Content-Type: application/json' -u admin:admin "$BASEURL/services/openads~openads/parcelles/8000160000AK0145") && echo $RESULT

# Test with good login and bad ids_parcelle
RESULT=$(curl -s -X GET -H 'Content-Type: application/json' -u admin:admin "$BASEURL/services/openads~openads/parcelles/800016") && echo $RESULT

```
