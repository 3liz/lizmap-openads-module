# Démarrer la stack Lizmap + openADS avec docker-compose

Étapes:

- Démarrer Lizmap + openADS with docker-compose
    ```
    # Supprimer les versions précédentes (optionnel)
    make clean

    # Lancer les différents services
    make run
    ```

- Activer le module openads (lizmap 3.6+ uniquement)

```bash
make setup-module
```

- Ouvrez votre navigateur à `http://localhost:9090`, identifiez-vous dans
  l'administration. login: admin, mot de passe : admin.
- Aller dans la configuration des projets. Un simple projet `openads` est présent
  mais vous devez définir les droits dans l'administration Lizmap afin de le consulter.


Pour plus d'information, veuillez vous référer à la [documentation docker-compose](https://docs.docker.com/compose/)

## Accès à l'instance PostgreSQL dans docker

Vous pouvez accéder à la base de test PostgreSQL `lizmap` dans docker depuis votre hôte en configurant un
[fichier de service](https://docs.qgis.org/latest/fr/docs/user_manual/managing_data_source/opening_data.html#postgresql-service-connection-file).
Le fichier de service peut être enregistré dans votre home `~/.pg_service.conf` et doit contenir cette section

```ini
[lizmap-openads]
dbname=lizmap
host=localhost
port=9032
user=lizmap
password=lizmap1234!
```

Vous pouvez ensuite utiliser un client PostgreSQL (psql, QGIS, PgAdmin, DBeaver) et utiliser le `service`
au lieu des identifiants classiques (hôte, port, nom de la base de donnée, login et mot de passe).

```bash
psql service=lizmap-openads
```

## Tests des requêtes API

Avec `pytest` vous pouvez tester automatiquements les requêtes vers l'API openads:

```bash
# In a venv, it's better, but this is out of scope
pip install -r requirements/tests.txt
cd tests/request_test
pytest
pytest -s -v
```

## Accès au conteneur lizmap

Si vous voulez aller dans le conteneur lizmap pour y taper des commandes :
`make shell`.

