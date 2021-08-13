import {Vector as VectorSource} from 'ol/source';
import GeoJSON from "ol/format/GeoJSON";
import {generationFeaturesJson, isIfUndefined} from "../standartFunctions";
import {MapController, SelectDrawController} from "../re/htmlControllers";
import {bbox} from 'ol/loadingstrategy';

export default class AnalysisController
{
    constructor(arrayHtml, dataHtml, incrementalObject)
    {
        this.arrayHtml = arrayHtml;
        this.arrayElementHtml = {
            element: arrayHtml['analysis']['element'],
            elementStart: arrayHtml['analysis']['elementStart'],
            formData: arrayHtml['analysis']['formData'],
            openUpdate: arrayHtml['analysis']['openMenu'],
            closeUpdate: arrayHtml['analysis']['closeMenu'],
            elementsMenu: arrayHtml['analysis']['elementsMenu'],
        };
        this.dataHtml = dataHtml;
        this.incrementalObject = incrementalObject;
    }

    start()
    {
        let self = this;

        self.arrayElementHtml.element.on('click', function ()
        {
            self.arrayElementHtml['openUpdate']();

            self.arrayElementHtml['elementsMenu'].forEach(function (item)
            {
                if (!isIfUndefined([item['name'], item['inputElem']])) {
                    item['inputElem'].val(item['defaultValue']);
                }
            });
        });

        self.arrayElementHtml['elementStart'].off('click').on('click', function ()
        {
            if (self.arrayElementHtml['formData'][0].checkValidity())
            {
                self.arrayElementHtml['closeUpdate']();

                // сбор параметров анализа для отправки в запросе
                let query = '';
                self.arrayElementHtml['elementsMenu'].forEach(function (item, i)
                {
                    query += '?';
                    if (!isIfUndefined([item['name'], item['inputElem']])) {
                        query += globalServer['optionsServer']['analysis']['dataName'][i]['name'];
                        query += '=' + item['inputElem'].val();
                    }
                });

                globalServer.loadAnalysisData(
                    self.dataHtml.mapInteraction.workingSource,
                    query
                ); // Запуск анализа и получение результатов
            }

            /*
            // очень плохой вариант, но рабочий(из-за этого приходится повторный раз контроллер по карте вызывать)
            self.dataHtml.mapInteraction.workingSource = new VectorSource({ // начальные данные с сервера загружаются
                format: new GeoJSON({featureProjection: self.dataHtml.incrementalInfo.projection}),
                url: globalServer['optionsServer']['analysis']['url'] + query,
            });

            self.dataHtml.mapInteraction.incrementalObject['arrayLayers']['loadSource'].setSource(self.dataHtml.mapInteraction.workingSource);

            // перезапуск контроллера по карте
            let mapController = new MapController(self.arrayHtml, self.dataHtml, self.incrementalObject);
            mapController.start();

            */

        });
    }

}