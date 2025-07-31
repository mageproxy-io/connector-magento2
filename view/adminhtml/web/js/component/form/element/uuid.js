/*
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

define([
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function (Element, alert, $t) {

    'use strict';

    return Element.extend({

        defaults: {
            elementTmpl: 'Mageproxy_Connector/form/element/uuid'
        },

        copyToClipBoard: function () {
            const uuid = this.value();
            const tempInput = document.createElement('input');
            tempInput.value = uuid;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            alert({content: $t('Copied!')})
        },

    });
});
