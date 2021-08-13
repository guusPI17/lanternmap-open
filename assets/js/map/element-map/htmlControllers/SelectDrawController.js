export default class SelectDrawController
{
    constructor(arrayElementHtml, dataHtml, incrementalObject)
    {
        this.elementHtml = arrayElementHtml['selectDraw']['element'];
        this.nameType = arrayElementHtml['selectDraw']['nameType'];
        this.dataHtml = dataHtml;
        this.incrementalObject = incrementalObject;
    }

    start()
    {
        let self = this;

        // событие выбора элемента для рисования
        self.elementHtml.on('click', function ()
        {
            self.dataHtml.mapInteraction.map.removeInteraction(self.dataHtml.mapInteraction.draw);
            let selectType = $(this).attr(self.nameType);
            self.dataHtml.mapInteraction.addInteractionDraw(selectType, self.dataHtml.incrementalInfo['propertiesFeatures']);

            // Закрытие доступа к contextMenu во время рисования
            if (selectType === 'None') {
                self.incrementalObject['mapControl']['contextMenuControl']['control'].contextmenu.enable();
            } else {
                self.incrementalObject['mapControl']['contextMenuControl']['control'].contextmenu.disable();
            }

        });

    }
}