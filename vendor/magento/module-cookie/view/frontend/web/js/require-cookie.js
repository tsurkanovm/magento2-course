/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('mage.requireCookie', {
        options: {
            event: 'click',
            noCookieUrl: 'enable-cookies',
            triggers: ['.action.login', '.action.submit']
        },

        /**
         * Constructor
         * @private
         */
        _create: function () {
            this._bind();
        },

        /**
         * This method binds elements found in this widget.
         * @private
         */
        _bind: function () {
            var events = {};

            $.each(this.options.triggers, function (index, value) {
                events['click ' + value] = '_checkCookie';
            });
            this._on(events);
        },

        /**
         * This method set the url for the redirect.
         * @param {jQuery.Event} event
         * @private
         */
        _checkCookie: function (event) {
            if (navigator.cookieEnabled) {
                return;
            }
            event.preventDefault();
            window.location = this.options.noCookieUrl;
        }
    });

    return $.mage.requireCookie;
});
