import {translates} from './translates.js';

/**
 * Данный класс служит набором функций для стилизации всех элементов
 */
export class ScriptsStyle
{
    constructor()
    {
        let self = this;

        $(document).keyup(function (e)
        {
            self.keyCallHandling(e);
        });

        $('#closeUpdatePropFeaturePoint').on('click', function ()
        {
            self.closeUpPropFeature('Point');
        });
        $('#closeUpdatePropFeatureLineString').on('click', function ()
        {
            self.closeUpPropFeature('LineString');
        });

        $('#closeAnalysisMenu').on('click', function ()
        {
            self.closeAnalysisMenu();
        });

        $('.groupDraw').on('click', function ()
        {
            self.selectDrawElem($(this));
        });

        $("#sidebar").on('click', ".sidebar-dropdown > a", function ()
        {
            self.openCloseList($(this));
        });

    }

    /**
     * События после вызова карты
     * @param {OLComponent} olComponent
     */
    appFunctionOlMap(olComponent)
    {
        let self = this;
        // повторная прогрузка карты (из-за смещения левой панели)
        setTimeout(function ()
        {
            olComponent.updateSize();
        }, 500);

        // закрытие левого sidebar
        $("#close-sidebar").on('click', function ()
        {
            $(".page-wrapper").removeClass("toggled");
            setTimeout(function ()
            {
                olComponent.updateSize();
            }, 300);
        });

        // открытие левого sidebar
        $("#show-sidebar").on('click', function ()
        {
            $(".page-wrapper").addClass("toggled");
            setTimeout(function ()
            {
                olComponent.updateSize();
            }, 300);
        });

        // $("#applyAnalysisMenu").on('click', function ()
        // {
        //     $('#faviconLoading').toggleClass('loading');
        //     $('#panelDuringAnalysis').css('width', '100%');
        //     self.defaultDrawElement($(".groupDraw"));
        // });
    }

    /**
     * Дефолтное значение на панели рисования
     * @param {jQuery} selectElem - элемент на который был произведен клик
     */
    defaultDrawElement(selectElem)
    {
        // удаление элементов из "выбранного"
        let arrI = $('.panelDraw').find('i');
        arrI.each(function (index, element)
        {
            $(element).removeClass('selectDrawI');
        });
        // добавление нового элемента в "выбранный"
        $("#noneDrawI").addClass('selectDrawI');
    }

    /**
     * @returns {ScriptsStyle}
     */
    getSelf()
    {
        return this;
    }

    /**
     * Обработчик вызова клавиш
     * @param {Event} e
     */
    keyCallHandling(e)
    {
        if (e.shiftKey && e.keyCode === 49) {
            $('a[data-type-draw="None"]').click();
        }
        if (e.shiftKey && e.keyCode === 50) {
            $('a[data-type-draw="Point"]').click();
        }
        if (e.shiftKey && e.keyCode === 51) {
            $('a[data-type-draw="Polygon"]').click();
        }
        if (e.shiftKey && e.keyCode === 52) {
            $('a[data-type-draw="LineString"]').click();
        }
    }

    /**
     * Генерация списка с features
     * @param {Object} features
     * @param {string} idContainer
     */
    generationListFeatures(features, idContainer)
    {
        $("#" + idContainer).empty();
        if (features.length > 0) {
            let i = 0;
            $.each(features, function (key, value)
            {
                let attrLink = {
                    'href': '#',
                    'style': 'word-break:break-all',
                };
                let textLink = '';
                if (typeof value['name_street'] !== "undefined") {
                    textLink = value['name_street'];
                } else if (typeof value['name_lantern'] !== "undefined") {
                    textLink = value['name_lantern'];
                }
                let link = $('<a/>').attr(attrLink).text(textLink);

                let attrLi = {
                    'class': 'sidebar-dropdown',
                };
                let li = $('<li/>').attr(attrLi);

                link.appendTo(li); // добавление feature в список
                li.appendTo($("#" + idContainer)); // добавление feature в список

                let attrDiv = {
                    'class': 'sidebar-submenu',
                };
                let div = $('<div/>').attr(attrDiv);

                div.appendTo(li); // создание div под свойства feature

                let ul = $('<ul/>');

                ul.appendTo(div);

                for (let key in value) {
                    let outValue = '';
                    if (value[key] === 'false') {
                        outValue = 'нет';
                    } else if (value[key] === 'true') {
                        outValue = 'да';
                    } else {
                        outValue = value[key];
                    }
                    let textLink = translates.find(translate => translate.name === key)
                        .translateName + ':' + outValue;
                    let attrLink = {
                        'href': '#',
                        'style': 'word-break:break-all',
                    };
                    let link = $('<a/>').attr(attrLink).text(textLink);

                    let li = $('<li/>');

                    link.appendTo(li);
                    li.appendTo(ul);
                }
                i++;
            });
        } else {
            let attrLink = {
                'href': '#',
            };
            let textLink = 'Отсутствуют';
            let link = $('<a/>').attr(attrLink).text(textLink);

            let attrLi = {
                'class': 'sidebar-dropdown',
            };
            let li = $('<li/>').attr(attrLi);

            link.appendTo(li); // добавление feature в список
            li.appendTo($("#" + idContainer)); // добавление feature в список
        }
    }

    /**
     * Добавление информации и фактурах в "выделенные фактуры"
     * @param {ScriptsStyle} self
     * @param {Object} features
     */
    addInfosSelectFeatures(self, features)
    {
        self.generationListFeatures(features, 'listFeaturesSelect');
    }

    /**
     * Добавление информации и фактурах в "все фактуры"
     * @param {ScriptsStyle} self
     * @param {Object} features
     */
    addInfosAddFeatures(self, features)
    {
        self.generationListFeatures(features, 'listFeaturesAdd');
    }


    /**
     * Вывод информации по результата анализа
     * @param {string} title
     * @param {string} text
     * @param {string} color
     */
    outputResultAnalysis(title, text, color)
    {
        let modalNotification = $('#modalNotification');
        modalNotification.on('show.bs.modal', function (e)
        {
            let modalNotificationTitle = $('#modalNotificationTitle');
            modalNotificationTitle.css('color', color);
            modalNotificationTitle.html(title);
            $('#textInModalNotification').html(text);
        });

        // удаляем иконку загрузки во время "ожидание анализа"
        $('#faviconLoading').removeClass('loading');

        // скрываем панель "ожидание анализа"
        $('#panelDuringAnalysis').css('width', '0%');

        // вызываем модульное окно
        modalNotification.modal('show');
    }

    startAnalysis()
    {
        $('#faviconLoading').toggleClass('loading');
        $('#panelDuringAnalysis').css('width', '100%');
    }

    /**
     * Стилизация панели рисования
     * @param {jQuery} selectElem - элемент на который был произведен клик
     */
    selectDrawElem(selectElem)
    {
        // удаление элементов из "выбранного"
        let arrI = $('.panelDraw').find('i');
        arrI.each(function (index, element)
        {
            $(element).removeClass('selectDrawI');
        });
        // добавление нового элемента в "выбранный"
        selectElem.find('i').addClass('selectDrawI');
    }

    /**
     * Открытие списка в левом меню
     */
    openCloseList(self)
    {
        // $(".sidebar-submenu").slideDown(200);
        if (self.parent().hasClass("active")) {
            //$(".sidebar-dropdown").removeClass("active");
            self.next(".sidebar-submenu").slideUp(200);
            self.parent().removeClass("active");
        } else {
            //$(".sidebar-dropdown").removeClass("active");
            self.next(".sidebar-submenu").slideDown(200);
            self.parent().addClass("active");
        }
    }

    /**
     * Открыть панель "свойства фактуры" из пкм
     * @param {string} typeFeature
     */
    openUpPropFeature(typeFeature)
    {
        $(`#updatePropertiesFeature${typeFeature}`).css('width', '100%');
    }

    /**
     * Закрыть панель "свойства фактуры" из пкм
     * @param {string} typeFeature
     */
    closeUpPropFeature(typeFeature)
    {
        $(`#updatePropertiesFeature${typeFeature}`).css('width', '0%');
    }

    /**
     * Открыть панель анализа
     */
    openAnalysisMenu()
    {
        $('#analysisMenu').css('width', '100%');
    }

    /**
     * Закрыть панель анализа
     */
    closeAnalysisMenu()
    {
        $('#analysisMenu').css('width', '0%');
        //this.defaultDrawElement($(".groupDraw"));
    }

    /**
     * Возникает при открытии fullscreen
     */
    enterfullscreen()
    {
        $('#map').css('height', '100%');
    }

    /**
     * Возникает при закрытии fullscreen
     */
    leavefullscreen()
    {
        $('#map').css('height', '84%');
    }

}