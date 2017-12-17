<?php
use CRM_Emailcorrector_ExtensionUtil as E;


/**
 * EmailCorrect.Fix API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_email_correct_Fix($params) {
  $emailCorrect = new CRM_Emailcorrector_EmailCorrect();
  return civicrm_api3_create_success($emailCorrect->fixEmails(), $params, 'EmailCorrect', 'Fix');
}
