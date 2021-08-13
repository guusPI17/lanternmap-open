export default class DataContextMenu
{
    constructor()
    {
        /**
         * Массив итемов для contextMenu
         * @type {({classname: string, text: string, key: string}|{classname: string, text: string, key: string})[]}
         */
         this.itemsContextMenu = [
            {
                key: 'removeFeature',
                text: 'Удалить фактуру',
                classname: 'not-active-item',
            },
            {
                key: 'propertiesFeature',
                text: 'Свойства фактуры',
                classname: 'not-active-item',
            },
        ];
    }

    /**
     * @returns {Array}
     */
    get itemsContextMenu()
    {
        return this._itemsContextMenu;
    }

    /**
     * @param {Array} value
     */
    set itemsContextMenu(value)
    {
        this._itemsContextMenu = value;
    }

}
