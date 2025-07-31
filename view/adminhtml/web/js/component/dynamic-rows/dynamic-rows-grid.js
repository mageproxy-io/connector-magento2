/*
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

define(['Magento_Ui/js/dynamic-rows/dynamic-rows-grid'], function (DynamicRowsGrid) {
    'use strict';

    return DynamicRowsGrid.extend({
        initialize: function () {
            this._super();
        },

        populate: function () {
            this.set('insertData', this.source.data[this.dataProvider]);
        }

    });
});
