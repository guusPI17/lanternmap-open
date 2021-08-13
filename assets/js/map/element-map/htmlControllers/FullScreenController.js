export default class FullScreenController
{
    constructor(arrayElementHtml, dataHtml, incrementalObject)
    {
        this.arrayElementHtml = {
        };
        this.dataHtml = dataHtml;
        this.incrementalObject = incrementalObject;
    }

    start()
    {
        let self = this;

        // событие во время нажатия "полноэкранный режим"
        self.incrementalObject['mapControl']['fullScreenControl']['control'].fullScreen.on('enterfullscreen', function (e)
        {
            self.dataHtml['incrementalInfo']['controls']['fullScreen']['enterfullscreen']();
            if(self.dataHtml['incrementalInfo']['controls']['fullScreen']['lagTime']){ // если нужна задержка по времени
                lagTime(self.dataHtml['incrementalInfo']['controls']['fullScreen']['time']);
            }
        });

        // событие после выхода из "полноэкранный режим"
        self.incrementalObject['mapControl']['fullScreenControl']['control'].fullScreen.on('leavefullscreen', function (e)
        {
            self.dataHtml['incrementalInfo']['controls']['fullScreen']['leavefullscreen']();
            if(self.dataHtml['incrementalInfo']['controls']['fullScreen']['lagTime']){ // если нужна задержка по времени
                lagTime(self.dataHtml['incrementalInfo']['controls']['fullScreen']['time']);
            }
        });

        // функция по задержке времени
        function lagTime(timeout = 300, func = function ()
        {
            self.dataHtml['mapInteraction'].map.updateSize()
        })
        {
            setTimeout(func, timeout);
        }
    }

}