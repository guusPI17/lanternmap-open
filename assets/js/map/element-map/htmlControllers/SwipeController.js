import {getRenderPixel} from "ol/render";

export default class SwipeController
{
    constructor(arrayElementHtml, dataHtml, incrementalObject)
    {
        this.elementHtml = arrayElementHtml['swipe']['element'];
        this.dataHtml = dataHtml;
        this.incrementalObject = incrementalObject;
    }

    start()
    {
        let self = this;

        // событие - ползунок смены карты
        self.elementHtml.on('input', function ()
        {
            self.dataHtml.mapInteraction.map.render();
        });

        // событие пререндинга слоя WorldImagery
        self.dataHtml.mapInteraction.arrayLayers['worldImagery'].on('prerender', function (e)
        {
            self.overlapVectorTile(e);
        });

        // событие после рендинга слоя WorldImagery
        self.dataHtml.mapInteraction.arrayLayers['worldImagery'].on('postrender', function (e)
        {
            let ctx = e.context;
            ctx.restore();
        });
    }

    /**
     * Перекрытия vectorTile
     * @param {event} e
     */
    overlapVectorTile(e)
    {
        let self = this;

        let ctx = e.context;
        let mapSize = this.dataHtml.mapInteraction.map.getSize();
        let width = mapSize[0] * (self.elementHtml.val() / 100);
        let tl = getRenderPixel(e, [width, 0]);
        let tr = getRenderPixel(e, [mapSize[0], 0]);
        let bl = getRenderPixel(e, [width, mapSize[1]]);
        let br = getRenderPixel(e, mapSize);

        ctx.save();
        ctx.beginPath();
        ctx.moveTo(tl[0], tl[1]);
        ctx.lineTo(bl[0], bl[1]);
        ctx.lineTo(br[0], br[1]);
        ctx.lineTo(tr[0], tr[1]);
        ctx.closePath();
        ctx.clip();
    }
}