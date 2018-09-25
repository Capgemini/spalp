/**
 * @file
 * Example module for the Single Page Application Landing Page module.
 *
 * This example uses jQuery to keep the code simple, but apps can use any
 * framework (or vanilla JavaScript).
 */

(function ($, Drupal) {

    'use strict';

    Drupal.spalpExample = {'app_id': 'spalp_example'};

    Drupal.behaviors.spalpExample = {
      attach: function (context, settings) {
        $('#' + Drupal.spalpExample.app_id, context).once('spalpExample').each(function () {
          Drupal.spalpExample.getConfig();
        });
      }
    };

    /**
     * Get the config from JSON.
     */
    Drupal.spalpExample.getConfig = function () {
        const configURL = $('#appConfig').attr('href');
        let config = {};

        $.getJSON(
            configURL, function (data) {
                config = data;
                Drupal.spalpExample.addContent(config);
            }
        );
        return config;
    };

    /**
     * Add content to the page.
     *
     * @param {object} config
     *   The JSON object with the app configuration.
     */
    Drupal.spalpExample.addContent = function (config) {
        Drupal.spalpExample.printAppTexts(Drupal.spalpExample.app_id, config);
    };

    /**
     * Prints app Text on spalp container.
     *
     * @param {string} appWrapper
     *   App wrapper element id.
     * @param {object} data
     *   The JSON object with the app text.
           */
    Drupal.spalpExample.printAppTexts = function (appWrapper, data) {

      const $appWrapper = $('#' + appWrapper);

      /*
       The appText object is used for text that appears within the app.
       */
      const heading = '<h2>' + data.appText.heading + '</h2>'
      const body = data.appText.body

      $appWrapper.append(heading);

      /*
        The appConfig object is used for configuration options, such as
        API keys or endpoint URLs.

        In this simple example, we use it to control the number of times
        the body is repeated.
       */
      for (let i = 0; i < data.appConfig.bodyRepeat; i++) {
        $appWrapper.append(body);
      }
    };

})(jQuery, Drupal)
