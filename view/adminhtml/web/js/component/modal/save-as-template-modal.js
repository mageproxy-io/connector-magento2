/*
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
define([
    'jquery',
    'Magento_Ui/js/modal/modal-component',
], function ($, Modal) {
    'use strict';

    return Modal.extend({
        defaults: {
            submitUrl: ''
        },

        openModal: function (data) {
            if (!data.url) {
                throw new Error('Submit URL is required');
            }
            if (typeof data === 'object') {
                this.submitUrl = data.url;
            }
            // default to "All Stores"
            this.getChild('autorun_template_settings').getChild('store').value(0);
            return this._super();
        },

        submitAjax: function () {
            $.ajax({
                url: this.submitUrl,
                data: this.source.get(this.dataScope),
                type: 'post',
                dataType: 'json',
                showLoader: true,
                complete: $.proxy(function (data) {
                    this._onAjaxResult(data);
                }, this)
            });

            this.closeModal();
        },

        _onAjaxResult: function (data) {
            const payload = data.responseJSON;
            const notification = $('body').data('mage-notification');
            notification.clear();
            notification.add({
                error: payload.error,
                message: payload.message,
                insertMethod: function (msg) {
                    const placeholder = $(notification.placeholder);
                    if (placeholder.length) {
                        placeholder.toggle();
                        placeholder.html(msg);
                    } else {
                        const wrapper = $('<div class="modal-content"></div>')
                            .html(msg)
                        $('.modal-header').after(wrapper);
                    }
                    $(notification.placeholder).fadeOut(2500, function() {
                        notification.clear();
                    });
                }
            });
        }

    });
});
