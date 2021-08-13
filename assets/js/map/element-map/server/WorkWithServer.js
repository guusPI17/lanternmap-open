export default class WorkWithServer
{
    constructor(optionsServer)
    {
        this.optionsServer = optionsServer;
    }

    /**
     * Ajax отправка данных на сервер
     * @param jsonData
     */
    sendData(jsonData)
    {
        let self = this;

        let data = {};
        data[self.optionsServer['saveMap']['dataName']] = jsonData;
        $.ajax({
            url: self.optionsServer['saveMap']['url'],
            type: self.optionsServer['saveMap']['method'],
            dataType: self.optionsServer['saveMap']['dataType'],
            data: data,
            success: function (response)
            {
                //console.log("Данные отправлены успешно");
            },
            error: function (e)
            {
                console.log(e);
                //console.log("Ошибка при отправке");
            }
        });
    }

    /**
     * Запуск анализа и получение результатов
     * @param {Object} vectorSource
     * @param {string} query
     */
    loadAnalysisData(vectorSource, query)
    {
        let self = this;

        let xhr = new XMLHttpRequest();
        let url = self.optionsServer['analysis']['url'] + query;
        let method = self.optionsServer['analysis']['method'];
        let responseType = self.optionsServer['analysis']['dataType'];
        xhr.open(method, url, true);
        xhr.responseType = responseType;

        // запускаем визуально иконку загрузки
        self.optionsServer['analysis']['interactions']['startAnalysis']['event']();
        xhr.onreadystatechange = function ()
        {
            // если данные еще не получены
            if (4 !== xhr.readyState) {
                return;
            }

            if (200 !== xhr.status) {
                let title = 'Уведомление об ошибке';
                let text = "Во время анализа произошла ошибка. <br>" +
                    `<u>Статус ошибки:</u> ${xhr.response.code}<br>` +
                    `<u>Текст ошибки:</u> ${xhr.response.message}`;

                // если есть подробности об ошибке
                if (0 !== xhr.response.details.length) {
                    text += "<br><u>Подробности об ошибке:</u> <br>";
                    xhr.response.details.forEach(function (item, i)
                    {
                        text += i + 1 + ") " + item + "<br>";
                    });
                }
                self.optionsServer['analysis']['interactions']['outputResult']['event'](title, text, 'red');
                //console.log("Ошибка при анализе");
            } else {
                let title = 'Уведомление об успешной операции.';
                let text = 'Анализ завершился успешно.';
                self.optionsServer['analysis']['interactions']['outputResult']['event'](title, text, 'green');

                //console.log("Анализ прошел успешно");
                // очищаем все фактуры
                vectorSource.clear();
                // добавляем полученные фактуры в имеющимся
                vectorSource.addFeatures(
                    vectorSource.getFormat().readFeatures(xhr.response)
                );
            }

        };
        xhr.send();
    }
}
