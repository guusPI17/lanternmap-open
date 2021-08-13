import 'ol/ol.css';
import {Tile as TileLayer, Vector as VectorLayer} from "ol/layer";
import {Vector as VectorSource} from 'ol/source';
import {OSM} from "ol/source";
import XYZ from "ol/source/XYZ";
import {MapInteraction} from "./map/MapInteraction";
import {DataContextMenu, DataHtml, DataStyles} from "./re/basis";
import {ContextMenuControl, FullScreenControl, ZoomToExtentControl} from "./re/mapControl";
import {
    ClearController, ContextMenuController, MapController, DownloadController,
    SelectDrawController, SwipeController, TooltipController, FullScreenController,
    ZoomToExtentController, AnalysisController
} from "./re/htmlControllers";
import GeoJSON from "ol/format/GeoJSON";
import {WorkWithServer} from "./re/server";

export class OLComponent
{
    constructor(incrementalInfo, arrHtmlElem, optionsServer)
    {
        /**
         * Массив html объектов связанных с картой
         */
        this.arrHtmlElem = arrHtmlElem;

        /**
         * глобальный объект по работе с сервером
         * @type {WorkWithServer}
         */
        window.globalServer = new WorkWithServer(optionsServer);

        /**
         * данные по стилям
         * @type {DataStyles}
         */
        let dataStyles = new DataStyles();

        /**
         * данные по contextMenu
         * @type {DataContextMenu}
         */
        let dataContextMenu = new DataContextMenu();

        /**
         * Основные начальные слои
         * @type {{worldImagery: *, osm: *, loadSource: *}}
         */
        let arrayLayers = {
            osm: new TileLayer({ // слой схематической карты
                source: new OSM(),
            }),
            worldImagery: new TileLayer({ // слой спутниковой карты
                source: new XYZ({
                    attributions:
                        'Tiles © <a href="https://services.arcgisonline.com/ArcGIS/' +
                        'rest/services/World_Imagery/MapServer">ArcGIS</a>',
                    url:
                        'https://server.arcgisonline.com/ArcGIS/rest/services/' +
                        'World_Imagery/MapServer/tile/{z}/{y}/{x}',
                    maxZoom: 17,
                    projection: incrementalInfo.projection,
                }),
            }),
            loadSource: new VectorLayer({ // слой для работы с фактурами
                source: new VectorSource({ // начальные данные с сервера загружаются
                    format: new GeoJSON({featureProjection: incrementalInfo.projection}),
                    url: globalServer['optionsServer']['loadMap']['url'],
                }),
                style: function (feature)
                {
                    return dataStyles.defaultStyles[feature.getGeometry().getType()];
                },
            }),
        };

        /**
         * Внутренний контроллер "контекстному меню"
         * @type {ContextMenuControl}
         */
        let contextMenuControl = new ContextMenuControl(
            dataContextMenu.itemsContextMenu,
            incrementalInfo['controls']['contextMenu']
        );
        /**
         * Внутренний контроллер "полноэкранный режим"
         * @type {FullScreenControl}
         */
        let fullScreenControl = new FullScreenControl(incrementalInfo['controls']['fullScreen']);

        /**
         * Внутренний контроллер "зум карты и телепорт по координатам"
         * @type {ZoomToExtentControl}
         */
        let zoomToExtentControl = new ZoomToExtentControl(incrementalInfo['controls']['zoomToExtent']);

        /**
         * Объект добавочной информации
         */
        this.incrementalObject = {
            hitTolerance: 3, // радиус нахождения фактуры от точки нажатия
            projection: incrementalInfo.projection, // название проекции
            optionsView: incrementalInfo.optionsView,
            mapControl: {
                contextMenuControl: {
                    control: contextMenuControl,
                    items: dataContextMenu.itemsContextMenu,
                },
                fullScreenControl: {
                    control: fullScreenControl,
                },
                zoomToExtentControl: {
                    control: zoomToExtentControl,
                },
            },
            arrayLayers: arrayLayers,
            defaultStyles: dataStyles.defaultStyles,
            updateStyles: dataStyles.updateStyles
        };

        /**
         * взаимодействие с map
         * @type {MapInteraction}
         */
        this.mapInteraction = new MapInteraction(incrementalInfo['idContainerMap'], this.incrementalObject);

        /**
         * объект объединенных данных
         * @type {DataHtml}
         */
        this.dataHtml = new DataHtml(this.mapInteraction, incrementalInfo);

        this.startInteriorControl();
        this.functionalLaunchMap();
        this.startHtmlControllers();

    }

    /**
     * Запуск внутренних контроллеров карты
     */
    startInteriorControl()
    {
        /**
         * Добавление внутреннего контроллера по контекстному меню
         */
        this.mapInteraction.map.addControl(
            this.incrementalObject['mapControl']['contextMenuControl']['control'].contextmenu
        );

        /**
         * Добавление внутреннего контроллера по полноэкранному режиму
         */
        this.mapInteraction.map.addControl(
            this.incrementalObject['mapControl']['fullScreenControl']['control'].fullScreen
        );

        /**
         * Добавление внутреннего контроллера по зуму и телепорту по координатам на карте
         */
        this.mapInteraction.map.addControl(
            this.incrementalObject['mapControl']['zoomToExtentControl']['control'].zoomToExtent
        );
    }

    /**
     * Запуск функционала карты
     */
    functionalLaunchMap()
    {
        /**
         * Включение загрузки фактур DragAndDrop
         */
        this.mapInteraction.loadFeatures();

        /**
         * Включение выделения фактур
         */
        this.mapInteraction.selectFeatures();

        /**
         * Включение редактирование фактур
         */
        this.mapInteraction.modificationSelectFeatures();

        /**
         * Включение выделение рамкой(Ctrl) фактур
         */
        this.mapInteraction.selectDragBox();
    }

    /**
     * Запуск html контроллеров
     */
    startHtmlControllers()
    {
        /**
         * Html контроллер очистки фактур
         * @type {ClearController}
         */
        if (this.arrHtmlElem['clear']) {
            let clearController = new ClearController(this.arrHtmlElem, this.dataHtml, this.incrementalObject);
            clearController.start();
        }

        /**
         * Html контроллер контекстного меню
         * @type {ContextMenuController}
         */
        if (this.arrHtmlElem['contextMenu']) {
            let contextMenuController = new ContextMenuController(this.arrHtmlElem, this.dataHtml, this.incrementalObject);
            contextMenuController.start();
        }

        /**
         * Html контроллер полноэкранного режима
         * @type {FullScreenController}
         */
        if (this.arrHtmlElem['fullScreen']) {
            let fullScreenController = new FullScreenController(this.arrHtmlElem, this.dataHtml, this.incrementalObject);
            fullScreenController.start();
        }

        /**
         * Html контроллер карты
         * @type {MapController}
         */
        if (this.arrHtmlElem['map']) {
            let mapController = new MapController(this.arrHtmlElem, this.dataHtml, this.incrementalObject);
            mapController.start();
        }

        /**
         * Html контроллер панели рисования фактур
         * @type {SelectDrawController}
         */
        if (this.arrHtmlElem['selectDraw']) {
            let selectDrawController = new SelectDrawController(this.arrHtmlElem, this.dataHtml, this.incrementalObject);
            selectDrawController.start();
        }

        /**
         * Html контроллер ползунка
         * @type {SwipeController}
         */
        if (this.arrHtmlElem['swipe']) {
            let swipeMenuController = new SwipeController(this.arrHtmlElem, this.dataHtml, this.incrementalObject);
            swipeMenuController.start();
        }

        /**
         * Html контроллер подсказки об фактуре
         * @type {TooltipController}
         */
        if (this.arrHtmlElem['tooltip']) {
            let tooltipController = new TooltipController(this.arrHtmlElem, this.dataHtml, this.incrementalObject);
            tooltipController.start();
        }

        /**
         * Html контроллер зума карты и телепорта по координатам
         * @type {ZoomToExtentController}
         */
        if (this.arrHtmlElem['zoomToExtent']) {
            let zoomToExtentController = new ZoomToExtentController(this.arrHtmlElem, this.dataHtml, this.incrementalObject);
            zoomToExtentController.start();
        }

        /**
         * Html контроллер скачивания карты
         * @type {DownloadController}
         */
        if (this.arrHtmlElem['download']) {
            let downloadController = new DownloadController(this.arrHtmlElem, this.dataHtml, this.incrementalObject);
            downloadController.start();
        }

        /**
         * Html контроллер анализа карты
         * @type {DownloadController}
         */
        if (this.arrHtmlElem['analysis']) {
            let analysisController = new AnalysisController(this.arrHtmlElem, this.dataHtml, this.incrementalObject);
            analysisController.start();
        }
    }

    /**
     * Обновить координаты карты
     */
    updateSize()
    {
        this.mapInteraction.map.updateSize();
    }
}