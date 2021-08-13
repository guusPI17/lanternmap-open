import {generationFeaturesJson} from "../standartFunctions";

export default class ClearController
{
    constructor(arrayElementHtml, dataHtml, incrementalObject)
    {
        this.elementHtml = arrayElementHtml['clear']['element'];
        this.dataHtml = dataHtml;
        this.incrementalObject = incrementalObject;
    }

    start()
    {
        let self = this;

        // событие очистить фактуры
        self.elementHtml.on('click', function ()
        {
            let check = confirm('Вы действительно хотите удалить все элементы на этой карте?');
            if(check) {
                self.dataHtml.mapInteraction.workingSource.clear();
                globalServer.sendData(
                    generationFeaturesJson(
                        self.dataHtml.mapInteraction.workingSource,
                        self.dataHtml.incrementalInfo.projection
                    )
                ); // отправка данных на сервер
            }
        });
    }
}