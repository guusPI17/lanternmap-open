import {Vector as VectorLayer} from 'ol/layer';
import GeoJSON from 'ol/format/GeoJSON';
import DataMap from '../basis/DataMap.js';
import {DragBox, Select, Snap, Modify, DragAndDrop, Draw} from 'ol/interaction';
import {platformModifierKeyOnly} from 'ol/events/condition';
import {generationFeaturesJson, isIfUndefined} from '../standartFunctions';
import {getLength} from 'ol/sphere';
import {Style} from "ol/style";


/**
 * Класс по работе с объектом Map из 'ol/Map'.
 * Наследуется от класса DataMap
 */
export class MapInteraction extends DataMap
{
    /**
     * @param {string} div
     * @param {Object} incrementalObject
     */
    constructor(div, incrementalObject)
    {
        super(incrementalObject['arrayLayers'], div, incrementalObject['optionsView']);
        this.incrementalObject = incrementalObject;
        this.styles = incrementalObject['defaultStyles'];
        this.workingSource = incrementalObject['arrayLayers']['loadSource'].getSource(); // векторный слой для работы с фактурами
    }

    /**
     * Загрузка фактур
     */
    loadFeatures()
    {
        super.dragAndDrop = new DragAndDrop({
            formatConstructors: [GeoJSON]
        });
        let source = this.workingSource;
        super.dragAndDrop.on('addfeatures', function (e)
        {
            source.addFeatures(e.features);
        });
        super.map.addInteraction(super.dragAndDrop);
        this.mergerFeatures();
    }

    /**
     * Выделение фактур
     */
    selectFeatures()
    {
        super.select = new Select({
            hitTolerance: this.incrementalObject['hitTolerance'],
        });
        super.map.addInteraction(super.select);
    }

    /**
     * Добавление рамки выделения(Ctrl)
     */
    selectDragBox()
    {
        let self = this;
        super.dragBox = new DragBox({
            condition: platformModifierKeyOnly,
            projection: self.incrementalObject['projection'],
        });
        super.map.addInteraction(super.dragBox);
    }

    /**
     * Модификация фактур при их выборе
     */
    modificationSelectFeatures()
    {
        let self = this;
        super.modify = new Modify({
            features: super.select.getFeatures()
        });
        super.modify.on('modifyend', function (e)
        {
            let features = e.features.getArray();
            features.forEach(function (item)
            {
                let length = 0;
                if (item.getGeometry().constructor.name === 'LineString') {
                    length = getLength(
                        item.getGeometry(),
                        {projection: self.incrementalObject.projection,}
                    ).toFixed(0);
                    item.setProperties({
                        length: length,
                    });
                }
            });
        });
        super.map.addInteraction(super.modify);
    }

    /**
     * Модификация всех фактур
     */
    modificationAllFeatures()
    {
        super.modify = new Modify({
            source: this.workingSource
        });
        super.map.addInteraction(super.modify);
    }

    /**
     * Объединение фактур
     */
    mergerFeatures()
    {
        super.snap = new Snap({
            source: this.workingSource
        });
        super.map.addInteraction(super.snap);
    }

    /**
     * Выбор типа Draw и добавление на карту
     * @param {string} typeSelect
     * @param {array} propertiesFeatures
     */
    addInteractionDraw(typeSelect, propertiesFeatures)
    {
        let self = this;
        let styles = this.styles;
        let indexFeatures = 0;
        if (typeSelect !== 'None') {
            super.draw = new Draw({
                source: this.workingSource,
                type: typeSelect,
            });
            let select = super.select;
            let modify = super.modify;
            super.draw.on('drawstart', function (e)
            {
                indexFeatures = self.workingSource.getFeatures().length;
                select.setActive(false);
                modify.setActive(false);
            });

            super.draw.on('drawend', function (e)
            {
                propertiesFeatures.forEach(function (item)
                {
                    if (!isIfUndefined([item['name'], item['defaultValue']])) {
                        if(e.feature.getGeometry().constructor.name === item['typeFeature']) {
                            if (true === item['unique']) {
                                e.feature.setProperties({
                                    [item['name']]: item['defaultValue'] + '_' + indexFeatures++
                                });
                            } else {
                                e.feature.setProperties({
                                    [item['name']]: item['defaultValue']
                                });
                            }
                        }

                    }
                });
                let length = 0;
                if (e.feature.getGeometry().constructor.name === 'LineString') {
                    // в метрах
                    length = getLength(
                        e.feature.getGeometry(),
                        {projection: self.incrementalObject.projection,}
                    ).toFixed(0);
                    e.feature.setProperties({
                        length: length,
                    });
                }
                //console.log(length);
                e.feature.setStyle(function (feature)
                {
                    return styles[feature.getGeometry().getType()];
                });
                //e.feature.setId("12345"); // уникальный id
                select.setActive(true);
                modify.setActive(true);
            });
            super.map.addInteraction(super.draw);
        }
    }

    /**
     *@return {Array}
     */
    get styles()
    {
        return this._styles;
    }

    /**
     * @param {Array} value
     */
    set styles(value)
    {
        this._styles = value;
    }

    /**
     *@return {VectorSource}
     */
    get workingSource()
    {
        return this._workingSource;
    }

    /**
     * @param {VectorSource} value
     */
    set workingSource(value)
    {
        this._workingSource = value;
    }

    /**
     *@return {VectorLayer}
     */
    get workingLayer()
    {
        return this._workingLayer;
    }

    /**
     * @param {VectorLayer} value
     */
    set workingLayer(value)
    {
        this._workingLayer = value;
    }

}
