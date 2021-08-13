import {getRightFeature, isIfUndefined, generationFeaturesJson} from "../standartFunctions";

export default class ContextMenuController
{
    constructor(arrayElementHtml, dataHtml, incrementalObject)
    {
        this.arrayElementHtml = {
            elementUpdateLineString: arrayElementHtml['contextMenu']['elementUpdateLineString'],
            elementUpdatePoint: arrayElementHtml['contextMenu']['elementUpdatePoint'],
            formDataLineString: arrayElementHtml['contextMenu']['formDataLineString'],
            formDataPoint: arrayElementHtml['contextMenu']['formDataPoint'],
            openUpdate: arrayElementHtml['contextMenu']['openUpdate'],
            closeUpdate: arrayElementHtml['contextMenu']['closeUpdate'],
        };

        this.dataHtml = dataHtml;
        this.incrementalObject = incrementalObject;
    }

    start()
    {
        let self = this;

        // событие ПКМ по карте
        self.dataHtml.mapInteraction.map.on('contextmenu', function (e)
        {
            let feature = getRightFeature(self.dataHtml.mapInteraction.map, self.incrementalObject['hitTolerance'], e.pixel);
            // удаление
            self.updateItemsContextMenu(feature, 'removeFeature', 'active-item', function ()
            {
                self.dataHtml.mapInteraction.workingSource.removeFeature(feature);
                globalServer.sendData(
                    generationFeaturesJson(
                        self.dataHtml.mapInteraction.workingSource,
                        self.dataHtml.incrementalInfo.projection
                    )
                ); // отправка данных на сервер
            });

            // изменение свойств
            self.updateItemsContextMenu(feature, 'propertiesFeature', 'active-item', function ()
            {
                self.updatePropFetInContextMenu(feature);
            });
        });

        // событие клик по карте
        self.dataHtml.mapInteraction.map.on('click', function ()
        {
            self.incrementalObject['mapControl']['contextMenuControl']['control'].closeMenu(); // если открыто contextMenu, то закрываем
        });
    }

    /**
     * Обновить свойства фактуры в контекст меню
     * @param feature
     */
    updatePropFetInContextMenu(feature)
    {
        let self = this;

        self.arrayElementHtml['openUpdate'](feature.getGeometry().constructor.name);
        // openUpPropFeature();

        self.dataHtml.incrementalInfo['propertiesFeatures'].forEach(function (item)
        {
            if (!isIfUndefined([item['name'], item['inputElem']])) {
                if (feature.getGeometry().constructor.name === item['typeFeature']) {
                    item['inputElem'].val(feature.get(item['name']));
                }
            }
        });

        let nameButton = 'elementUpdate' + feature.getGeometry().constructor.name;
        self.arrayElementHtml[nameButton].off('click').on('click', function ()
        {
            // если в форме нету ошибок и все поля заполнены
            let nameForm = 'formData' + feature.getGeometry().constructor.name;
            if (self.arrayElementHtml[nameForm][0].checkValidity()) {

                self.arrayElementHtml['closeUpdate'](feature.getGeometry().constructor.name);
                // closeUpPropFeature();

                self.dataHtml.incrementalInfo['propertiesFeatures'].forEach(function (item)
                {
                    if (!isIfUndefined([item['name'], item['inputElem']])) {
                        if (feature.getGeometry().constructor.name === item['typeFeature']) {
                            feature.setProperties({
                                [item['name']]: item['inputElem'].val(),
                            });
                        }
                    }
                });
                feature.changed(); // обновление фактуры
                globalServer.sendData(
                    generationFeaturesJson(
                        self.dataHtml.mapInteraction.workingSource,
                        self.dataHtml.incrementalInfo.projection)
                ); // отправка данных на сервер
                return true;
            }
        });
    }

    /**
     * Обновление итемов в contextMenu
     * @param {Object} feature
     * @param {String} keyItem
     * @param {String} classname
     * @param {Function} callback
     */
    updateItemsContextMenu(feature, keyItem, classname, callback)
    {
        let self = this;

        let indexItem = self.incrementalObject['mapControl']['contextMenuControl']['items']
            .findIndex(element => element.key === keyItem);
        self.incrementalObject['mapControl']['contextMenuControl']['items'][indexItem].classname = classname;
        self.incrementalObject['mapControl']['contextMenuControl']['items'][indexItem].callback = callback;
        self.incrementalObject['mapControl']['contextMenuControl']['control']
            .updateContextMenu(feature, self.incrementalObject['mapControl']['contextMenuControl']['items']);
    }
}