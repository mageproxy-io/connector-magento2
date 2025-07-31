/*
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

define(['Magento_Ui/js/form/element/abstract'], function (Element) {
    'use strict';
    return Element.extend({
        onStoreSelected: function (selection) {
            this.visible(!!parseInt(selection));
        },
    });
});
