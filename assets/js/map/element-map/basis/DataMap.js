import Map from 'ol/Map';
import View from 'ol/View';
import {DragBox, Select, Snap, Modify, DragAndDrop, Draw} from 'ol/interaction';

/**
 * Данные объекта Map из 'ol/Map'
 */
export default class DataMap
{
    /**
     * @param {Array} arrayLayers
     * @param {String} div
     * @param {Object} optionsView
     */
    constructor(arrayLayers, div, optionsView)
    {
        this.arrayLayers = arrayLayers;
        let arrayLayersIndex = [];
        for (var item in this.arrayLayers) {
            arrayLayersIndex.push(this.arrayLayers[item]);
        }
        this.map = new Map({
            layers: arrayLayersIndex,
            target: div,
            view: new View({
                center: optionsView.center,
                zoom: optionsView.zoom,
                projection: optionsView.projection,
            }),
        });
    }

    /**
     * @return {Map}
     */
    get map()
    {
        return this._map;
    }

    /**
     * @param {Map} value
     */
    set map(value)
    {
        this._map = value;
    }


    /**
     * @return {Array}
     */
    get arrayLayers()
    {
        return this._arrayLayers;
    }

    /**
     * @param {Array} value
     */
    set arrayLayers(value)
    {
        this._arrayLayers = value;
    }

    /**
     *@return {Select}
     */
    get select()
    {
        return this._select;
    }

    /**
     * @param {Select} value
     */
    set select(value)
    {
        this._select = value;
    }

    /**
     *@return {DragBox}
     */
    get dragBox()
    {
        return this._dragBox;
    }

    /**
     * @param {DragBox} value
     */
    set dragBox(value)
    {
        this._dragBox = value;
    }

    /**
     *@return {Snap}
     */
    get snap()
    {
        return this._snap;
    }

    /**
     * @param {Snap} value
     */
    set snap(value)
    {
        this._snap = value;
    }

    /**
     *@return {Modify}
     */
    get modify()
    {
        return this._modify;
    }

    /**
     * @param {Modify} value
     */
    set modify(value)
    {
        this._modify = value;
    }

    /**
     *@return {DragAndDrop}
     */
    get dragAndDrop()
    {
        return this._dragAndDrop;
    }

    /**
     * @param {DragAndDrop} value
     */
    set dragAndDrop(value)
    {
        this._dragAndDrop = value;
    }

    /**
     *@return {Draw}
     */
    get draw()
    {
        return this._draw;
    }

    /**
     * @param {Draw} value
     */
    set draw(value)
    {
        this._draw = value;
    }

}
