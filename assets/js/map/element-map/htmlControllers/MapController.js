import {generationFeaturesJson} from "../standartFunctions";

export default class MapController
{
    constructor(arrayElementHtml, dataHtml, incrementalObject)
    {
        // this.elementHtml = arrayElementHtml['']['element']
        this.arrayElementHtml = {};
        this.dataHtml = dataHtml;
        this.incrementalObject = incrementalObject;
        this.mainZoom = dataHtml.mapInteraction.map.getView().getZoom(); // zoom
    }

    start()
    {
        let self = this;

        // событие вывод всех выделенных фактур в infoText
        self.dataHtml.mapInteraction.select.getFeatures().on(['add', 'remove'], function ()
        {
            let features = self.dataHtml.mapInteraction.select.getFeatures().getArray().map(function (feature)
            {
                let arr = {};
                self.dataHtml.incrementalInfo['propertiesFeatures'].forEach(function (item)
                {
                    if(feature.getGeometry().constructor.name === item['typeFeature']) {
                        arr[item['name']] = (feature.get(item['name']));
                    }
                });

                return arr;
            });
            let selfCallBack = self.dataHtml.incrementalInfo['interactions']['selectFeature']['self'];
            self.dataHtml.incrementalInfo['interactions']['selectFeature']['event'](selfCallBack, features);

            globalServer.sendData(
                generationFeaturesJson(
                    self.dataHtml.mapInteraction.workingSource,
                    self.dataHtml.incrementalInfo.projection
                )
            ); // отправка данных на сервер (редактирование, удаление фактур)
        });

        self.dataHtml.mapInteraction.workingSource.on('addfeature', function ()
        {
            globalServer.sendData(
                generationFeaturesJson(
                    self.dataHtml.mapInteraction.workingSource,
                    self.dataHtml.incrementalInfo.projection
                )
            ); // отправка данных на сервер
        });

        // Общее событие изменения. Срабатывает при увеличении счетчика ревизий
        self.dataHtml.mapInteraction.workingSource.on('change', function (e)
        {
            let features = self.dataHtml.mapInteraction.workingSource.getFeatures().map(function (feature)
            {
                let arr = {};
                self.dataHtml.incrementalInfo['propertiesFeatures'].forEach(function (item)
                {
                    if(feature.getGeometry().constructor.name === item['typeFeature']) {
                        arr[item['name']] = (feature.get(item['name']));
                    }
                });

                return arr;
            });
            let selfCallBack = self.dataHtml.incrementalInfo['interactions']['addFeature']['self'];
            self.dataHtml.incrementalInfo['interactions']['addFeature']['event'](selfCallBack, features);

        });

        // событие конец перетаскивания выделения
        self.dataHtml.mapInteraction.dragBox.on('boxend', function ()
        {
            self.selectionFeaturesInSingle();
        });

        // очистить выбор при разработке новой коробки и при нажатии на карте
        self.dataHtml.mapInteraction.dragBox.on('boxstart', function ()
        {
            self.dataHtml.mapInteraction.select.getFeatures().clear();
        });

        // событие начала перемещения по карте(zoom тоже)
        // включить ПОТОМ!!! событие и в DataStyles поменять размер radius а 0.0001
/*        self.dataHtml.mapInteraction.map.on('movestart', function ()
        {
            let currentZoom = self.dataHtml.mapInteraction.map.getView().getZoom();
            if(self.mainZoom === currentZoom){
                return;
            }
            let radius = 0;
            if (currentZoom > 18.8) {
                radius = 3.7;
            } else if (currentZoom  < 14) {
                radius = 0.0001;
            } else {
                radius = currentZoom / 15;
            }
            self.dataHtml.mapInteraction.workingSource.getFeatures().forEach(function (item)
            {
                if(item.getGeometry().constructor.name === 'Point') {
                    let point = new Style(
                        {
                            image: new CircleStyle({
                                fill: new Fill({
                                    color: 'red',
                                }),
                                radius: radius,
                                stroke: new Stroke({
                                    color: 'red',
                                    width: 1.25,
                                }),
                            })
                        });
                    item.setStyle(point);
                }
            });

        });*/
    }

    /**
     * Объединение фактур в единое выделение
     */
    selectionFeaturesInSingle()
    {
        let self = this;

        let rotation = self.dataHtml.mapInteraction.map.getView().getRotation();
        let oblique = rotation % (Math.PI / 2) !== 0;
        let candidateFeatures = oblique ? [] : self.dataHtml.mapInteraction.select.getFeatures();
        let extent = self.dataHtml.mapInteraction.dragBox.getGeometry().getExtent();
        self.dataHtml.mapInteraction.workingSource.forEachFeatureIntersectingExtent(extent, function (feature)
        {
            candidateFeatures.push(feature);
        });
        if (oblique) {
            let anchor = [0, 0];
            let geometry = this.dataHtml.mapInteraction.dragBox.getGeometry().clone();
            geometry.rotate(-rotation, anchor);
            let extent$1 = geometry.getExtent();
            candidateFeatures.forEach(function (feature)
            {
                let geometry = feature.getGeometry().clone();
                geometry.rotate(-rotation, anchor);
                if (geometry.intersectsExtent(extent$1)) {
                    self.dataHtml.mapInteraction.select.getFeatures().push(feature);
                }
            });
        }
    }
}