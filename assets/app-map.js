// any CSS you import will output into a single css file (app.css in this case)
import './styles/map/app-map.scss';

// start the Stimulus application
import './bootstrap';

import 'bootstrap';
import 'jquery';
import 'jquery-ui';
import {OLComponent} from "./js/map/element-map/OLComponent";

import {ScriptsStyle} from './js/map/page-map/ScriptsStyle';
import {translates} from './js/map/page-map/translates.js';

document.addEventListener('DOMContentLoaded', function ()
{
    let idMap = $('.main-body').data('idMap');

    let scriptsStyle = new ScriptsStyle();

    // Настройки сервера взаимодействующего с картой
    let path = 'http://lanternmap.local/menu/map/' + idMap;
    let optionsServer = {
        // данные загружаются в формете geoJson через GET
        loadMap: {
            url: `${path}/loadmap`,
            method: 'GET',
        },

        saveMap: {
            url: `${path}/savemap`,
            method: 'POST',
            dataType: 'json',
            dataName: 'dataMap',
        },

        analysis: {
            url: `${path}/analysis`,
            method: 'GET',
            dataType: 'json',
            dataName: [
                {
                    name: 'budget',
                }
            ],
            interactions: { // скрипты
                startAnalysis: {
                    event: scriptsStyle.startAnalysis,
                },
                outputResult: {
                    event: scriptsStyle.outputResultAnalysis,
                },
            },
        }
    };

    // Массив объектов html елементов связанных с картой
    let arrHtmlElem = {
        download: {
            element: $('#downloadFeatures')
        },
        analysis: {
            element: $('#analysisOpen'),
            elementStart: $('#applyAnalysisMenu'),
            formData: $('#propertiesFormAnalysis'), // форма для feature с типом "линий"
            openMenu: scriptsStyle.openAnalysisMenu, // функция открытия если нужна
            closeMenu: scriptsStyle.closeAnalysisMenu, // функция закрытия если нужна
            elementsMenu: [
                {
                    name: 'budget',
                    defaultValue: '100',
                    inputElem: $('#budgetAnalysis'), // id element куда вводить данные
                },
            ],
        },
        swipe: { // название элемента
            element: $('#swipeMap') // ссылка на элемент
        },
        clear: {
            element: $('#cleaFeatures')
        },
        contextMenu: { // если есть contextMenu, то updatePropFeat обязательно со всеми параметрами
            // если contextMenu нет, то можно только updatePropFeat без параметров
            formDataLineString: $('#propertiesFormLineString'), // форма для feature с типом "линий"
            formDataPoint: $('#propertiesFormPoint'), // форма для feature с типом "точка"
            elementUpdateLineString: $('#applyChangesLineString'),
            elementUpdatePoint: $('#applyChangesPoint'),
            openUpdate: scriptsStyle.openUpPropFeature, // функция открытия если нужна
            closeUpdate: scriptsStyle.closeUpPropFeature, // функция закрытия если нужна
        },
        selectDraw: {
            element: $('.groupDraw'),
            nameType: 'data-type-draw', // название аттрибута для хранения "типа элемента рисования{None,Circle и т.д.}"
        },
        tooltip: {
            element: $('#tooltipFeature'),
        },
        fullScreen: {},
        map: {},
        zoomToExtent: {},
    };

    // Внутренние настройки карты
    let incrementalInfo = {
        projection: 'EPSG:3857', // проекция для контроллеров
        optionsView: { // опции для View
            zoom: 15, // начальный зум
            center: [
                4266502.274545227,
                6938565.849610519,
            ],
            projection: 'EPSG:3857', // проекция для View
        },
        interactions: { // скрипты добавления фактур в информацию левой панели
            selectFeature: { // при выборе фактур
                self: scriptsStyle.getSelf(),
                event: scriptsStyle.addInfosSelectFeatures,
            },
            addFeature: { // при добавлении новых фактур на карту
                self: scriptsStyle.getSelf(),
                event: scriptsStyle.addInfosAddFeatures,
            },
        },
        idContainerMap: 'map', // id контейнера карты
        propertiesFeatures: [ // свойства features на карте
            {
                name: 'name_street',
                defaultValue: '[отсутствует]',
                inputElem: $('#propertiesNameFeatureLineString'), // id element откуда данные если нужно
                outputTooltip: true, // выводить в tooltip?
                unique: true, // уникальное значение (используется при генерации дефолтных значений)
                title: translates.find(translate => translate.name === 'name_street').translateName + ': ', // название при выводе в tooltip
                typeFeature: 'LineString' // для какой feature данное свойство
            },
            {
                name: 'name_lantern',
                defaultValue: 'пусто',
                inputElem: $('#propertiesNameFeaturePoint'),
                outputTooltip: true,
                unique: true,
                title: translates.find(translate => translate.name === 'name_lantern').translateName + ': ',
                typeFeature: 'Point'
            },
            {
                name: 'price',
                defaultValue: 0,
                inputElem: $('#propertiesPriceFeaturePoint'),
                outputTooltip: false,
                unique: false,
                typeFeature: 'Point'
            },
            {
                name: 'height',
                defaultValue: 0,
                inputElem: $('#propertiesHeightFeaturePoint'),
                outputTooltip: false,
                unique: false,
                typeFeature: 'Point'
            },
            {
                name: 'length',
                defaultValue: '[отсутствует]',
                inputElem: $('#propertiesLengthFeatureLineString'),
                outputTooltip: false,
                unique: false,
                typeFeature: 'LineString'
            },
            {
                name: 'width',
                defaultValue: 10,
                inputElem: $('#propertiesWidthFeatureLineString'),
                outputTooltip: false,
                unique: false,
                typeFeature: 'LineString'
            },
            {
                name: 'class_object',
                defaultValue: 'А1', // '' - default
                inputElem: $('#propertiesClassObjectFeatureLineString'),
                outputTooltip: false,
                unique: false,
                typeFeature: 'LineString'
            },
            {
                name: 'priority',
                defaultValue: 1,
                inputElem: $('#propertiesPriorityFeatureLineString'),
                outputTooltip: false,
                unique: false,
                typeFeature: 'LineString'
            },
        ],
        controls: { // контроллеры на карте
            fullScreen: {
                classContainerFullScreen: 'fullscreen',
                enterfullscreen: scriptsStyle.enterfullscreen, // событие во время открытия если нужно
                leavefullscreen: scriptsStyle.leavefullscreen, // событие после закрытия если нужно
                lagTime: true, // если нужна задержка времени во время открытия и после
                time: 300, // время задержки в мс
            },
            zoomToExtent: {
                coordinates: [ // координаты для переход по данной точке (extent - 4 координаты)
                    4261729.959816868,
                    6935310.813501093,
                    4272735.170174378,
                    6942398.311433599,
                ],
            },
            contextMenu: {
                width: 170, // размер contextMenu
            },
        }
    };

    /**
     * Создание элемента карты
     * @type {OLComponent}
     */
    let olComponent = new OLComponent(incrementalInfo, arrHtmlElem, optionsServer);

    scriptsStyle.appFunctionOlMap(olComponent);
});




