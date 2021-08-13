import {ZoomToExtent} from "ol/control";

/**
 * Класс по работе с ZoomToExtentControl
 */
export default class ZoomToExtentControl
{
    /**
     * @param {Object} incrementalInfo
     */
    constructor(incrementalInfo)
    {
        this.zoomToExtent = new ZoomToExtent({
            extent: incrementalInfo['coordinates']
        })
    }

    /**
     * @param {ZoomToExtent} value
     */
    set zoomToExtent(value)
    {
        this._zoomToExtent = value;
    }

    /**
     * @return {ZoomToExtent}
     */
    get zoomToExtent()
    {
        return this._zoomToExtent;
    }
}