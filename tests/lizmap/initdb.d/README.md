# Mise à jour du jeu de données

```bash
pg_dump -d "service=lizmap-openads" --schema=openads -f schema_openads.sql.ext
```
