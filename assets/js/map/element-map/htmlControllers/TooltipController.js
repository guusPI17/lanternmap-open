import {isIfUndefined} from "../standartFunctions";
import {getRightFeature} from "../standartFunctions";

export default class TooltipController
{
    constructor(arrayElementHtml, dataHtml, incrementalObject)
    {
        this.elementHtml = arrayElementHtml['tooltip']['element'];
        this.dataHtml = dataHtml;
        this.incrementalObject = incrementalObject;
    }

    start()
    {
        let self = this;

        self.elementHtml.attr('style', 'position: absolute; height: 1px;width: 1px; z-index: 100;');
        self.elementHtml.tooltip({
            animation: false,
            trigger: 'manual',
            container: '#' + self.dataHtml.incrementalInfo['idContainerMap'],
        });

        // событие перемещение указателя по карте
        self.dataHtml.mapInteraction.map.on('pointermove', function (e)
        {
            let hit = self.dataHtml.mapInteraction.map.hasFeatureAtPixel(e.pixel, {
                hitTolerance: self.incrementalObject['hitTolerance'],
            });
            self.dataHtml.mapInteraction.map.getViewport().style.cursor = hit ? 'pointer' : '';

            if (e.dragging) {
                self.elementHtml.tooltip('hide');
                return;
            }
            self.displayFeatureTooltip(e);
        });
    }

    /**
     * Вывод информации о feature в tooltip
     * @param {Event} e
     */
    displayFeatureTooltip(e)
    {
        let self = this;

        // e.originalEvent.pageX/Y - пиксели относительно экрана
        self.elementHtml.css({
            left: e.originalEvent.pageX + 'px',
            top: e.originalEvent.pageY - 15 + 'px',
        });
        // e.pixel - пиксели относительно div
        let feature = getRightFeature(this.dataHtml.mapInteraction.map, this.incrementalObject['hitTolerance'], e.pixel);
        if (feature) {
            let arrTile = '';
            self.dataHtml.incrementalInfo['propertiesFeatures'].forEach(function (item)
            {
                if (feature.getGeometry().constructor.name !== item['typeFeature']) {
                    return;
                }
                if (isIfUndefined([item['name'], item['outputTooltip'], item['title']])) {
                    return;
                }
                if (isIfUndefined([feature.get(item['name'])])) {
                    return;
                }
                if (item['outputTooltip']) {
                    arrTile += item['title'] + feature.get(item['name']) + '.';
                }
            });
            self.elementHtml.attr('data-original-title', arrTile).tooltip('show');
        } else {
            self.elementHtml.tooltip('hide');
        }
    }
}