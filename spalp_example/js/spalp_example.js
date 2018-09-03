/**
 * Example module for the Single Page Application Landing Page module.
 *
 * This example uses jQuery to keep the code simple, but apps can use any
 * framework (or vanilla JavaScript).
 */

(function ($, Drupal) {
    Drupal.behaviors.spalpExample = {
        attach: function (context, settings) {
            Drupal.spalpExample.getConfig();
        }
    };

    Drupal.spalpExample = {};

    /**
     * Get the language of the current page.
     *
     * @return {string}
     *   The ISO language code of the page.
     */
    Drupal.spalpExample.getLanguage = function () {
        return $('html').attr('lang');
    };

    /**
     * Get the config
     */
    Drupal.spalpExample.getConfig = function () {
        // TODO: get this from the node if it's available.
        const configURL = '/modules/contrib/spalp/spalp_example/config/spalp_example.json'
        let config = {}

        $.getJSON(
            configURL, function (data) {
                config = data;

                const language = Drupal.spalpExample.getLanguage();
                Drupal.spalpExample.addContent(config, language);
            }
        );
        return config;
    };

    /**
     * Add content to the page.
     *
     * @param {object} config
     *   The JSON object with the app configuration.
     * @param {string} language
     *   The ISO language code.
     */
    Drupal.spalpExample.addContent = function (config, language) {
        const $appWrapper = $('#spalp_example');

        /*
         The appText object is used for text that appears within the app.
         It is keyed by language code.
         */
        const heading = '<h2>' + config.appText[language].heading + '</h2>'
        const body = config.appText[language].body

        $appWrapper.append(heading);

        /*
          The appConfig object is used for configuration options, such as
          API keys or endpoint URLs.

          In this simple example, we use it to control the number of times
          the body is repeated.
         */
        for (let i = 0; i < config.appConfig.bodyRepeat; i++) {
            $appWrapper.append(body);
        }
    };

})(jQuery, Drupal)
