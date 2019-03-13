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
   * @param mixed $items
   *   The value that should be validated.
   * @param \Symfony\Component\Validator\Constraint $constraint
   *   The constraint for the validation.
   */
  public function validate(
    $items,
    Constraint $constraint
  ) {

    $node = $items->getParent()->getValue();
    $this_nid = $node->id();
    foreach ($items as $item) {
      // Does this app id value exist on another node?.
      if ($this->appIdExists($item->value, $this_nid)) {
        $this->context->addViolation($constraint->notUnique,
          ['%value' => $item->value]);
      }
    }
  }

  /**
   * Check if a different applanding node exists for this app ID.
   *
   * @param string $value
   *   The app id.
   * @param int $nid
   *   The id of the node being created or edited.
   *
   * @return bool
   *   TRUE if the app ID already exists on another node.
   */
  private function appIdExists($value, $nid) {
    $id_exists = FALSE;

    // Does a different app node already exist with this ID?
    // TODO: do this using dependency injection.
    $existing_node = \Drupal::service('spalp.core')->getAppNode($value);

    if (!empty($existing_node) && $existing_node->id() != $nid) {
      $id_exists = TRUE;
    }

    return $id_exists;
  }

}
