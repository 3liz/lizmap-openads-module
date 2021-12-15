lizMap.events.on({
    uicreated: () => {

        const NOM_COUCHE_PARCELLES = 'parcelles';
        const NOM_COUCHE_DOSSIER = 'dossiers_openads';

        // Fusion de deux emprises
        // Source : https://github.com/openlayers/openlayers/blob/v6.9.0/src/ol/extent.js#L318
        function extend(extent1, extent2) {
            if (extent2[0] < extent1[0]) {
                extent1[0] = extent2[0];
            }
            if (extent2[2] > extent1[2]) {
                extent1[2] = extent2[2];
            }
            if (extent2[1] < extent1[1]) {
                extent1[1] = extent2[1];
            }
            if (extent2[3] > extent1[3]) {
                extent1[3] = extent2[3];
            }
            return extent1;
        }

        const urlSearchParams = new URLSearchParams(window.location.search);
        const params = Object.fromEntries(urlSearchParams.entries());

        if (params && params.parcelles) {

            // Construction de la requête de récupération de
            // l'emprise pour la/les parcelles en paramètre
            const sentFormData = new FormData();

            sentFormData.append('SERVICE', 'WFS');
            sentFormData.append('VERSION', '1.1.0');
            sentFormData.append('REQUEST', 'GetFeature');
            sentFormData.append('TYPENAME', NOM_COUCHE_PARCELLES);
            sentFormData.append('OUTPUTFORMAT', 'GeoJSON');
            sentFormData.append('GEOMETRYNAME', 'extent');

            // Transformation du paramètre d'URL parcelles en EXP_FILTER
            // e.g. : 800016000AK0145;800016000ZA0002 => '800016000AK0145','800016000ZA0002'
            const parcelleIdentForExp_Filter = params.parcelles.split(';').map((parcelleIdent) => `'${parcelleIdent}'`).join(',');
            sentFormData.append('EXP_FILTER', `"ident" IN (${parcelleIdentForExp_Filter})`);

            fetch(`${lizUrls.wms}?repository=${lizUrls.params.repository}&project=${lizUrls.params.project}`, {
                body: sentFormData,
                method: "POST"
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data?.features.length > 0) {
                    let extent = data.features[0].bbox;
                    const parcellesIds = [];

                    for (const feature of data.features) {
                        // L'emprise zoomée est celle de l'ensemble des parcelles
                        extent = extend(extent, feature.bbox);
                        // ids des parcelles
                        parcellesIds.push(feature.id.split(NOM_COUCHE_PARCELLES + '.')[1]);
                    }

                    // Conversion de l'extent de 4326 vers la projection de la carte
                    // TODO : expose OL6 transformExtent in Lizmap
                    // https://openlayers.org/en/latest/apidoc/module-ol_proj.html#.transformExtent
                    const topleft = lizMap.mainLizmap.transform([extent[0], extent[1]], 'EPSG:4326', lizMap.mainLizmap.projection);
                    const bottomright = lizMap.mainLizmap.transform([extent[2], extent[3]], 'EPSG:4326', lizMap.mainLizmap.projection);
                    // Zoom sur l'emprise de la/les parcelles en paramètre
                    lizMap.mainLizmap.map.getView().fit([topleft[0], topleft[1], bottomright[0], bottomright[1]]);

                    // Surbrillance
                    for (let index = 0; index < parcellesIds.length; index++) {
                        lizMap.events.triggerEvent("layerfeatureselected", {
                            'featureType': NOM_COUCHE_PARCELLES,
                            'fid': parcellesIds[index],
                            'updateDrawing': index === (parcellesIds.length - 1) // update drawing only for last element
                        });
                    }
                }
            });
        }

        if (params && params.dossier) {
            // Construction de la requête de récupération
            // de l'emprise d'un dossier
            const sentFormData = new FormData();

            sentFormData.append('SERVICE', 'WFS');
            sentFormData.append('VERSION', '1.1.0');
            sentFormData.append('REQUEST', 'GetFeature');
            sentFormData.append('TYPENAME', NOM_COUCHE_DOSSIER);
            sentFormData.append('OUTPUTFORMAT', 'GeoJSON');
            sentFormData.append('GEOMETRYNAME', 'extent');

            sentFormData.append('EXP_FILTER', `"numero" = '${params.dossier}'`);

            fetch(`${lizUrls.wms}?repository=${lizUrls.params.repository}&project=${lizUrls.params.project}`, {
                body: sentFormData,
                method: "POST"
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data?.features.length > 0) {
                    const extent = data.features[0].bbox;
                    const fid = data.features[0].id.split(NOM_COUCHE_DOSSIER + '.')[1];

                    // Conversion de l'extent de 4326 vers la projection de la carte
                    // TODO : expose OL6 transformExtent in Lizmap
                    // https://openlayers.org/en/latest/apidoc/module-ol_proj.html#.transformExtent
                    const topleft = lizMap.mainLizmap.transform([extent[0], extent[1]], 'EPSG:4326', lizMap.mainLizmap.projection);
                    const bottomright = lizMap.mainLizmap.transform([extent[2], extent[3]], 'EPSG:4326', lizMap.mainLizmap.projection);
                    // Zoom sur l'emprise du dossier en paramètre
                    lizMap.mainLizmap.map.getView().fit([topleft[0], topleft[1], bottomright[0], bottomright[1]]);

                    // Sélection s'il n'y a pas de paramètre `parcelles` dans l'URL
                    if (!params.parcelles) {
                        lizMap.events.triggerEvent("layerfeatureselected",
                            { 'featureType': NOM_COUCHE_DOSSIER, 'fid': fid, 'updateDrawing': true }
                        );
                    }
                }
            });
        }
    }
});
