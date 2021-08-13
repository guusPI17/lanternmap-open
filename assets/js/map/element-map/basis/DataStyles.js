import {Circle as CircleStyle, Fill, Stroke, Style} from "ol/style";
import Icon from "ol/style/Icon";

export default class DataStyles
{
    constructor()
    {
        /**
         * Стили по умолчанию
         * @type {{LineString: Style, Point: Style, Polygon: Style}}
         */
        this.defaultStyles = {
            'Point': new Style({
                image: new CircleStyle({
                    fill: new Fill({
                        color: 'red',
                    }),
                    radius: 5,
                    stroke: new Stroke({
                        color: 'red',
                        width: 1.25,
                    }),
                }),
            }),
            'LineString': new Style({
                stroke: new Stroke({
                    color: 'red',
                    width: 3,
                }),
            }),
            'Polygon': new Style({
                image: new CircleStyle({
                    fill: new Fill({
                        color: 'red',
                    }),
                    stroke: new Stroke({
                        color: 'red',
                        width: 1.25,
                    }),
                    radius: 5
                }),
                fill: new Fill({
                    color: 'red',
                }),
                stroke: new Stroke({
                    color: 'red',
                    width: 1.25,
                }),
            }),
        };

        /**
         * Стили после обновления свойств
         * @type {{LineString: Style, Point: Style, Polygon: Style}}
         */
        this.updateStyles = {
            'Point': new Style({
                image: new CircleStyle({
                    fill: new Fill({
                        color: 'red',
                    }),
                    radius: 0.0001,
                    stroke: new Stroke({
                        color: 'red',
                        width: 1.25,
                    }),
                }),
            }),
            'LineString': new Style({
                stroke: new Stroke({
                    color: 'red',
                    width: 3,
                }),
            }),
            'Polygon': new Style({
                image: new CircleStyle({
                    fill: new Fill({
                        color: 'red',
                    }),
                    stroke: new Stroke({
                        color: 'red',
                        width: 1.25,
                    }),
                    radius: 5
                }),
                fill: new Fill({
                    color: 'red',
                }),
                stroke: new Stroke({
                    color: 'red',
                    width: 1.25,
                }),
            }),
        }
    }

    /**
     * @return {{LineString: Style, Point: Style, Polygon: Style}}
     */
    get defaultStyles()
    {
        return this._defaultStyles;
    }

    /**
     * @param {{LineString: Style, Point: Style, Polygon: Style}} value
     */
    set defaultStyles(value)
    {
        this._defaultStyles = value;
    }

    /**
     * @return {{LineString: Style, Point: Style, Polygon: Style}}
     */
    get updateStyles()
    {
        return this._updateStyles;
    }

    /**
     * @param {{LineString: Style, Point: Style, Polygon: Style}} value
     */
    set updateStyles(value)
    {
        this._updateStyles = value;
    }
}