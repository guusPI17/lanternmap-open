import GeoJSON from "ol/format/GeoJSON";

/**
 * Возвращает true, если есть undefined элемент
 * @param array
 * @returns {boolean}
 */
export function isIfUndefined(array){
    return array.indexOf() !== -1;
}

/**
 * Возвращает верный feature при выделении
 * (Пояснения: где имеется возможность редактировать feature, появляется дополнительный элемент point.
 * Данная функция служит определением самой feature от точки редактирования)
 * @param {Map} map
 * @param {number} hitTolerance
 * @param {array} pixel
 * @returns {Object}
 */
export function getRightFeature(map, hitTolerance, pixel)
{
    let arrayFeature = map.getFeaturesAtPixel(pixel, {
        hitTolerance: hitTolerance,
    });
    let indexFeature = arrayFeature.length > 1 ? 1 : 0; // индекс фактуры на которую кликнули
    return arrayFeature[indexFeature];
}

/**
 * Генерация json файла фактур с карты
 * @param {VectorSource} workingSource
 * @param {string} projection
 * @return {string}
 */
export function generationFeaturesJson(workingSource, projection)
{
    let format = new GeoJSON({featureProjection: projection});
    let features = workingSource.getFeatures();
    return format.writeFeatures(features)
}