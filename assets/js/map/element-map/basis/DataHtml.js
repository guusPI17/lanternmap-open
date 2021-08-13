export default class DataHtml
{
    /**
     * @param mapInteraction
     * @param incrementalInfo
     */
    constructor(mapInteraction, incrementalInfo)
    {
        this.mapInteraction = mapInteraction;
        this.incrementalInfo = incrementalInfo;
    }

    /**
     * @param {MapInteraction} value
     */
    set mapInteraction(value)
    {
        this._mapInteraction = value;
    }

    /**
     *@return {MapInteraction}
     */
    get mapInteraction()
    {
        return this._mapInteraction;
    }

    /**
     * @param {Object} value
     */
    set incrementalInfo(value)
    {
        this._incrementalInfo = value;
    }

    /**
     *@return {Object}
     */
    get incrementalInfo()
    {
        return this._incrementalInfo;
    }
}