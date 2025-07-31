/*
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

define([
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry',
    'mage/translate'
], function (Element, registry, $t) {
    'use strict';

    return Element.extend({
        defaults: {
            dateFrom: null,
            dateTo: null,
            duration: null,
            progress: null,
            elementTmpl: 'Mageproxy_Connector/form/element/progress',
            imports: {
                startedAt: '${ $.provider }:data.started_at',
                finishedAt: '${ $.provider }:data.finished_at',
                status: '${ $.provider }:data.status',
                duration: '${ $.provider }:data.duration'
            }
        },

        initialize: function () {
            this._super();
            const duration = parseInt(this.duration) * 60000; // convert minutes to milliseconds
            this.max = duration;
            if (this.finishedAt) {
                this.visible(false);
            } else if (new Date().getTime() > new Date(this.startedAt).getTime() + duration) {
                const elem = registry.async(this.parentName + '.duration');
                elem('error', $t('Recording should have been finished. Please check the cron is running normally.'));
                this.visible(false);
            } else if (this.startedAt && !this.finishedAt && this.status.toLowerCase() === 'running') {
                this.initProgress();
            }
        },

        initProgress: function () {
            const progress = new Date().getTime() - new Date(this.startedAt).getTime();
            this.value(progress);
        }
    });

});
