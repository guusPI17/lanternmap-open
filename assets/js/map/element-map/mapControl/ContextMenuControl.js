import ContextMenu from 'ol-contextmenu';

/**
 * Класс по работе с библиотекой ol-contextmenu
 */
export default class ContextMenuControl
{
    /**
     * @param {Array} itemsContextMenu
     * @param {Object} incrementalInfo
     */
    constructor(itemsContextMenu, incrementalInfo)
    {
        this.itemsContextMenu = $.extend(true, [], itemsContextMenu);
        this.defaultItemsContextMenu = $.extend(true, [], itemsContextMenu);

        this.contextmenu = new ContextMenu({
            width: incrementalInfo['width'],
            defaultItems: false,
            items: this.itemsContextMenu,
        });
    }

    /**
     * @param {ContextMenu} value
     */
    set contextmenu(value)
    {
        this._contextmenu = value;
    }

    /**
     * @return {ContextMenu}
     */
    get contextmenu()
    {
        return this._contextmenu;
    }

    /**
     * Закрытие меню если оно открыто
     */
    closeMenu()
    {
        if (this.contextmenu.isOpen()) {
            this.contextmenu.close();
        }
    }

    /**
     * Обновление contextMenu при пкм на фактуру
     * @param {Object} feature
     * @param {Array} itemsContextMenu
     */
    updateContextMenu(feature, itemsContextMenu)
    {
        if (feature !== undefined) {
            this.itemsContextMenu = $.extend(true, [], itemsContextMenu);
        } else {
            this.itemsContextMenu = $.extend(true, [], this.defaultItemsContextMenu);
        }
        this.contextmenu.clear();
        this.contextmenu.extend(this.itemsContextMenu);
    }

}
