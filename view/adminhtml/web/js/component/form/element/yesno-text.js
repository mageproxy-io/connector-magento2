/*
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
define(['Magento_Ui/js/form/element/abstract', 'mage/translate'], function (Abstract, $t) {
    'use strict';

    return Abstract.extend({

        defaults: {
            valueMap: {
                0: $t('No'),
                1: $t('Yes'),
            },
        },

        initialize: function () {
            this._super();
            this.mapValue();
            return this;
        },

        mapValue: function () {
            this.value(this.valueMap[this.value()]);
        }

    });
});
