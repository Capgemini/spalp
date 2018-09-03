/**
 * @file
 * This provides a simple example of how to use the Single Page
 * Application Landing Page module
 *
 * This example uses jQuery to keep the code simple, but apps can use any
 * framework (or vanilla JavaScript).
 */
(function ($, Drupal) {
  // TODO: get this from the node if it's available.
  const configURL = '/modules/contrib/spalp/spalp_example/config/spalp_example.json'
  let config = {};
  $.getJSON(configURL, function (data) {
    config = data;

    // TODO: multilingual.
    const heading = '<h2>' + config.appText.en.heading + '</h2>';
    const body = config.appText.en.body;
    $('#spalp_example').append(heading).append(body);
  });

})(jQuery, Drupal)
