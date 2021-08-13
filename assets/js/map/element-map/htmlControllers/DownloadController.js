import {generationFeaturesJson} from "../standartFunctions";

export default class DownloadController
{
    constructor(arrayElementHtml, dataHtml, incrementalObject)
    {
        this.elementHtml = arrayElementHtml['download']['element'];
        this.dataHtml = dataHtml;
        this.incrementalObject = incrementalObject;
    }

    start()
    {
        let self = this;

        // событие сохранить фактуры (выгрузка json)
        self.elementHtml.on('click', function ()
        {
            let json = generationFeaturesJson(
                self.dataHtml.mapInteraction.workingSource,
                self.dataHtml.incrementalInfo.projection
            );
            self.createLincDownload('data:text/json;charset=utf-8,' + json);
        });
    }

    /**
     * Создание ссылки для скачивания файла
     * @param {string} pathFile
     */
    createLincDownload(pathFile)
    {
        let link = document.createElement('a');
        link.setAttribute('href', pathFile);
        link.setAttribute('download', 'features.json');
        onload = link.click();
    }
}