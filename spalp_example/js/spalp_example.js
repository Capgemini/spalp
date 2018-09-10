/**
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
      $('#'+Drupal.spalpExample.app_id, context).once('spalpExample').each(function () {
        Drupal.spalpExample.getConfig();
      });
    }
  };

  /**
   * Get the config
   */
  Drupal.spalpExample.getConfig = function () {
    const configURL = $('#appConfig1').attr('href');
    let config = {};
    if (typeof configURL != 'undefined' && configURL != null) {
      $.getJSON(
          configURL, function (data) {
            config = data;
            Drupal.spalpExample.addContent(config);
          }
      );
    }
    return config;
  };

  /**
   * Add content to the page.
   *
   * @param {object} config
   *   The JSON object with the app configuration.
   */
  Drupal.spalpExample.addContent = function (config) {
    Drupal.spalpExample.printAppTexts(Drupal.spalpExample.app_id, config.app_text);
  };

  /**
   * Prints app Text on splap container.
   *
   * @param {string} appWrapper
   *   App wrapper element id.
   * @param {object} data
   *   The JSON object with the app text.
   */
  Drupal.spalpExample.printAppTexts = function (appWrapper, data) {
    $.each(data, function (key, value) {
      if ($.isPlainObject(value))
        Drupal.spalpExample.printAppTexts(appWrapper, value);
      else
        $('#'+appWrapper).append('<li>' + key + ' : ' + value + '</li>');
    });
  };

})(jQuery, Drupal)
