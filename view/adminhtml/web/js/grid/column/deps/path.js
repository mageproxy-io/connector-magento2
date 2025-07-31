/*
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

define(['Magento_Ui/js/grid/columns/column'], function (Column) {
  'use strict';
  return Column.extend({
    getPath: function (record) {
      const ctx = require.s.contexts._,
        moduleId = record.module_id,
        baseUrl = ctx.config.baseUrl,
        hasExt = moduleId.match(/\.(js|html|json)$/);

      const moduleNamePlusExt = hasExt ? moduleId.split('!')[1] : moduleId + '.js';

      return require.toUrl(moduleNamePlusExt).replace(baseUrl, '');
    }
  })
});
