SHELL = bash
.ONESHELL:
.PHONY: env
#

LIZMAP_DIR=$(shell pwd)/lizmap

LIZMAP_USER_ID:=$(shell id -u)
LIZMAP_USER_GID:=$(shell id -g)
LIZMAP_USER:=userphp
LIZMAP_GROUP:=groupphp

LIZMAP_VERSION_TAG:=3.8
QGIS_VERSION_TAG:=3.34
POSTGIS_VERSION_TAG:=15-3

QGIS_MAP_WORKERS:=1

LIZMAP_PORT:=9090
OWS_PORT:=127.0.0.1:9091
LZMPGPORT:=127.0.0.1:9032

BASEDIR:=$(shell realpath ..)

OPENADS_MODULE_SRC:=$(shell realpath ..)

main:
	@cat <<- EOF
		Commandes:
			run:   initialise environnement and run stack
			stop:  stop docker and remove containers
			clean: reset containers and remove lizmap configuration

			Note: Un fichier d'environment pour docker compose
			est crée à chaque invocation de 'run'.
			Cela permet d'utiliser les commandes 'docker compose'
			sans passer par le Makefile
	EOF

dirs:
	@mkdir -p $(LIZMAP_DIR)/var/log/nginx \
		      $(LIZMAP_DIR)/var/nginx-cache \
			  $(LIZMAP_DIR)/qgis-server-plugins \
		      $*

env: dirs
	@cat <<- EOF > .env
		COMPOSE_PROJECT_NAME=openads
		LIZMAP_DIR=$(LIZMAP_DIR)
		LIZMAP_USER_ID=$(LIZMAP_USER_ID)
		LIZMAP_USER_GID=$(LIZMAP_USER_GID)
		QGIS_MAP_WORKERS=$(QGIS_MAP_WORKERS)
		LIZMAP_PORT=$(LIZMAP_PORT)
		OWS_PORT=$(OWS_PORT)
		LZMPGPORT=$(LZMPGPORT)
		OPENADS_MODULE_SRC=$(OPENADS_MODULE_SRC)
		LIZMAP_VERSION_TAG=$(LIZMAP_VERSION_TAG)
		QGIS_VERSION_TAG=$(QGIS_VERSION_TAG)
		POSTGIS_VERSION_TAG=$(POSTGIS_VERSION_TAG)
		EOF

run: stop install-plugins
	docker compose up

stop: env
	docker compose down || true

clean: stop
	docker compose down -v
	rm -rf $(LIZMAP_DIR)/www/*
	rm -rf $(LIZMAP_DIR)/var/*
	rm -f .env

# Pull images from github 3liz
pull:
	docker pull 3liz/lizmap-web-client:${LIZMAP_VERSION_TAG}
	docker pull 3liz/qgis-map-server:${QGIS_VERSION_TAG}
	docker pull 3liz/postgis:${POSTGIS_VERSION_TAG}

install-plugins: dirs
	@echo "Install QGIS server plugins"
	docker run \
		-u $(LIZMAP_USER_ID):$(LIZMAP_USER_GID) \
		--rm -i \
		-e QGSRV_SERVER_PLUGINPATH=/srv/plugins \
		-e QGIS_PLUGIN_MANAGER_SOURCES_FILE=/tmp/sources-plugin-manager.list \
		-e QGIS_PLUGIN_MANAGER_CACHE_DIR=/tmp/cache-plugin-manager \
		-v $(LIZMAP_DIR)/qgis-server-plugins:/srv/plugins \
		-v $(shell pwd)/:/src \
		--entrypoint /bin/bash \
		3liz/qgis-map-server:$(QGIS_VERSION_TAG) \
		-c "\
		    echo "https://packages.3liz.org/pub/server-plugins-repository/unstable/plugins.[VERSION].xml" > /tmp/sources-plugin-manager.list; \
		    qgis-plugin-manager update; \
		    qgis-plugin-manager install 'Lizmap server'"

shell:
	docker compose exec -u ${LIZMAP_USER} lizmap /bin/sh -l

shell-root:
	docker compose exec lizmap /bin/sh -l

setup-module:
	docker compose exec -u ${LIZMAP_USER} lizmap php /www/lizmap/install/configurator.php -v openads
	docker compose exec -u ${LIZMAP_USER} lizmap php /www/lizmap/install/installer.php -v
