<?php

namespace Drupal\spalp\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted value is a unique app ID.
 *
 * @Constraint(
 *   id = "unique_spalp_app_id",
 *   label = @Translation("Spalp App ID is unique across the site", context = "Validation"),
 *   type = "string"
 * )
 */
class UniqueSpalpAppId extends Constraint {
  public $notUnique = 'The app ID must be unique - another landing page already exists for %value';

}
