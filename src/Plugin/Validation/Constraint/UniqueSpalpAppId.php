<?php

namespace Drupal\spalp\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted value is a unique app ID.
 *
 * @Constraint(
 *   id = "unique_spalp_app_id",
 *   label = @Translation("Unique App ID", context = "Validation"),
 *   type = "string"
 * )
 */
class UniqueSpalpAppId extends Constraint {
  public $notUnique = '%value is not unique';
}
