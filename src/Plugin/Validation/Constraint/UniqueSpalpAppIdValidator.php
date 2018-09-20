<?php

namespace Drupal\spalp\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validation to check that applanding nodes have unique app IDs.
 */
class UniqueSpalpAppIdValidator extends ConstraintValidator {

  /**
   * Check that the app ID does not match an existing app ID.
   *
   * @param mixed $items The value that should be validated
   * @param \Symfony\Component\Validator\Constraint $constraint The constraint
   *   for the validation
   */
  public function validate(
    $items,
    Constraint $constraint
  ) {

    $node = $items->getParent();

    foreach ($items as $item) {
      // Next check if the value is unique.
      if ($this->appIdExists($item->value, $node->id())) {
        $this->context->addViolation($constraint->notUnique,
          ['%value' => $item->value]);
      }
    }
  }

  /**
   * @param $value
   *
   * @return bool
   *   TRUE if the app ID already exists on another node.
   */
  private function appIdExists($value, $nid) {
    $id_exists = FALSE;

    // TODO: Does a different app node already exist with this ID?
    if (1) {
      $id_exists = TRUE;
    }

    return $id_exists;
  }
}
