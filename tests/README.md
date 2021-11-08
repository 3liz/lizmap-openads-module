# Démarrer la stack Lizmap + openADS avec docker-compose

Étapes:

- Démarrer Lizmap + openADS with docker-compose
    ```
    # Supprimer les versions précédentes (optionnel)
    make clean

    # Lancer les différents services
    make run
    ```

- Un simple projet `openads` est present mais vous devez définir les droits dans l'administration Lizmap afin de le consulter.

- Ouvrez votre navigateur à `http://localhost:9090`

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
