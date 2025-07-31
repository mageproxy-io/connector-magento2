/*
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

define([
    'uiComponent',
    'chartJs',
    'ko',
    'jquery',
    'chartjs/chartjs-adapter-moment',
    'chartjs/es6-shim.min',
    'moment',
], function (
    Component,
    Chart,
    ko,
    $
) {

    'use strict';

    return Component.extend({

        defaults: {
            requestConfig: {
                url: '${ $.updateUrl }',
                method: 'GET',
                dataType: 'json',
                showLoader: true,
                data: {}
            },
            chart: null
        },

        initialize: function () {
            this._super();
            this.registerKnockoutBinding();
        },

        createChart: function (elem) {
            this.chart = new Chart(elem, this.getChartConfig());
            this.refreshChartData();
        },

        refreshChartData: function () {
            this.requestConfig.data['form_key'] = FORM_KEY;
            this.requestConfig.data.recording_id = this.source.data.recording_id;
            $.ajax(this.requestConfig).done(this.updateChart.bind(this));
        },

        updateChart: function (data) {
            this.chart.data.datasets[0].data = data.depsTs;
            this.chart.data.datasets[1].data = data.optsTs;
            this.chart.update();
        },

        getChartConfig: function () {
            return {
                type: 'line',
                maintainAspectRatio: false,
                data: {
                    datasets: [
                        {
                            label: this.chartLabel,
                            tension: 0.1,
                            fill: false,
                            backgroundColor: '#f1d4b3',
                            borderColor: '#eb5202',
                            borderWidth: 2
                        },
                        {
                            label: 'Deployments',
                            backgroundColor: '#514943',
                            borderWidth: 0,
                            barPercentage: 0.1,
                            categoryPercentage: 0.5,
                            type: 'bar',
                            fill: false
                        }
                    ]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false,
                            position: 'top',
                        }
                    },
                    scales: {
                        x: {
                            type: 'timeseries',
                            time: {
                                unit: 'hour', // or 'day' if you prefer
                                displayFormats: {
                                    hour: 'MMM d, HH:mm', // e.g., "May 5, 14:00"
                                    day: 'MMM d',          // for daily view
                                },
                                tooltipFormat: 'MMM d, HH:mm:ss'
                            },
                            title: {
                                display: true,
                                text: 'Time'
                            },
                            adapters: {
                                date: {
                                    timezone: 'Asia/Tokyo', // Set the timezone to Asia/Tokyo
                                }
                            }
                        },
                        y: {
                            type: 'linear',
                            position: 'left',
                            title: {
                                display: true,
                                text: this.yScaleLabel
                            },
                            ticks: {
                                precision: 0,
                                beginAtZero: true,
                            },
                            min: 0
                        },
                    }
                }
            }
        },

        registerKnockoutBinding: function () {
            const self = this;
            ko.bindingHandlers.initChart = {
                init: function (element) {
                    self.createChart(element);
                }
            };
        }
    });
});
