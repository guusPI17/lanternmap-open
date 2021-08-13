import {FullScreen} from "ol/control";

/**
 * Класс по работе с FullScreenControl
 */
export default class FullScreenControl
{
    /**
     * @param {Object} incrementalInfo
     */
    constructor(incrementalInfo)
    {
        this.fullScreen = new FullScreen({
            source: incrementalInfo['classContainerFullScreen'],
        })
    }

    /**
     * @param {FullScreen} value
     */
    set fullScreen(value)
    {
        this._fullScreen = value;
    }

    /**
     * @return {FullScreen}
     */
    get fullScreen()
    {
        return this._fullScreen;
    }
}