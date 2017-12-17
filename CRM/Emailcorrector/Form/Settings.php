<?php

use CRM_Emailcorrector_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Emailcorrector_Form_Settings extends CRM_Core_Form {

  private $_type = NULL;
  private $_topSettings = array();
  private $_secondSettings = array();
  private $_fakeSettings = array();

  /**
   * Overridden parent method to buid the form
   *
   * @throws CiviCRM_API3_Exception
   */
  public function buildQuickForm() {
    $emailCorrect = new CRM_Emailcorrector_EmailCorrect();
    $this->add('hidden', 'type');
    switch ($this->_type) {
      case 'top':
        $elementNames = array();
        $this->_topSettings = civicrm_api3('Setting', 'getvalue',array(
          'name' => $emailCorrect->getTopSettingName(),
          ));
        $index = 1;
        foreach ($this->_topSettings as $source => $target) {
          $this->add('text', 'top_source_'.$index, ts('Te corrigeren waarde'), array(), false);
          $this->add('text', 'top_target_'.$index, ts('veranderen naar'), array(), false);
          $elementNames['top_source_'.$index] = 'top_target_'.$index;
          $index++;
        }
        //add 5 empty elements
        for ($x=$index; $x<=$index+5;$x++) {
          $this->add('text', 'top_source_'.$x, ts('Te corrigeren waarde'), array(), false);
          $this->add('text', 'top_target_'.$x, ts('veranderen naar'), array(), false);
          $elementNames['top_source_'.$x] = 'top_target_'.$x;
        }
        $this->assign('elementNames', $elementNames);
        break;
      case 'second':
        $this->_secondSettings = civicrm_api3('Setting', 'getvalue',array(
          'name' => $emailCorrect->getSecondSettingName(),
        ));
        $index = 1;
        foreach ($this->_secondSettings as $source => $target) {
          $this->add('text', 'second_source_'.$index, ts('Te corrigeren waarde'), array(), false);
          $this->add('text', 'second_target_'.$index, ts('veranderen naar'), array(), false);
          $elementNames['second_source_'.$index] = 'second_target_'.$index;
          $index++;
        }
        //add 5 empty elements
        for ($x=$index; $x<=$index+5;$x++) {
          $this->add('text', 'second_source_'.$x, ts('Te corrigeren waarde'), array(), false);
          $this->add('text', 'second_target_'.$x, ts('veranderen naar'), array(), false);
          $elementNames['second_source_'.$x] = 'second_target_'.$x;
        }
        $this->assign('elementNames', $elementNames);
        break;
      case 'fake':
        $this->_fakeSettings = civicrm_api3('Setting', 'getvalue',array(
          'name' => $emailCorrect->getFakeSettingName(),
        ));
        $index = 1;
        foreach ($this->_fakeSettings as $fakeId => $email) {
          $this->add('text', 'fake_email_'.$index, ts('Te verwijderen emailadres'), array('size' => 80,), false);
          $this->assign('max_index', $index);
          $index++;
        }
        //add 5 empty elements
        for ($x=$index; $x<=$index+5;$x++) {
          $this->add('text', 'fake_email_'.$x, ts('Te verwijderen emailadres'), array('size' => 80,), false);
        }
        $this->assign('elementNames', $this->getRenderableElementNames());
        break;
    }
    $this->assign('settings_type', $this->_type);
    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
      array('type' => 'cancel', 'name' => ts('Cancel'),),));
    // export form elements
    parent::buildQuickForm();
  }

  /**
   * Method to set default values
   *
   * @return array|NULL
   */
  public function setDefaultValues() {
    $defaults = array();
    $defaults['type'] = $this->_type;
    switch ($this->_type) {
      case 'top':
        $index = 1;
        foreach ($this->_topSettings as $source => $target) {
          $defaults['top_source_'.$index] = $source;
          $defaults['top_target_'.$index] = $target;
          $index++;
        }
        break;
      case 'second':
        $index = 1;
        foreach ($this->_secondSettings as $source => $target) {
          $defaults['second_source_'.$index] = $source;
          $defaults['second_target_'.$index] = $target;
          $index++;
        }
        break;
      case 'fake':
        $index = 1;
        foreach ($this->_fakeSettings as $fakeEmail) {
          $defaults['fake_email_'.$index] = $fakeEmail;
          $index++;
        }
        break;
    }
    return $defaults;
  }

  /**
   * Overridden parent method processing before form is built
   */
  public function preProcess() {
    $requestValues = CRM_Utils_Request::exportValues();
    if (!isset($requestValues['type'])) {
      CRM_Core_Error::createError('Missing parameter type in '.__METHOD__.'(extension be.aivl.emailcorrector)');
    }
    switch ($requestValues['type']) {
      case "top":
        CRM_Utils_System::setTitle('Instellingen email corrector - corrigeren achter de punt');
        break;
      case "second":
        CRM_Utils_System::setTitle('Instellingen email corrector - corrigeren voor de punt');
        break;
      case "fake":
        CRM_Utils_System::setTitle('Instellingen email corrector - emailadressen die verwijderd worden');
        break;
      default:
        CRM_Core_Error::createError('Invalid parameter type value of '.$requestValues['type'].' in '.__METHOD__.' (extension be.aivl.emailcorrector)');
        break;
    }
    $this->_type = $requestValues['type'];
    $userContext = CRM_Utils_System::url('civicrm/emailcorrector/page/settings', 'reset=1', true);
    CRM_Core_Session::singleton()->pushUserContext($userContext);
  }

  /**
   * Overridden parent method to process form submission
   *
   * @throws CiviCRM_API3_Exception
   */
  public function postProcess() {
    $this->_type = $this->_submitValues['type'];
    switch ($this->_type) {
      case 'fake':
        $this->saveFakeSettings();
        CRM_Core_Session::setStatus('Instellingen voor te verwijderen emailadressen opgeslagen', 'Instellingen voor email corrector opgeslagen', 'success');
        break;
      case 'top':
        $this->saveTopSettings();
        CRM_Core_Session::setStatus('Instellingen voor corrigeren achter de punt opgeslagen', 'Instellingen voor email corrector opgeslagen', 'success');
        break;
      case 'second':
        $this->saveSecondSettings();
        CRM_Core_Session::setStatus('Instellingen voor corrigeren voor de punt opgeslagen', 'Instellingen voor email corrector opgeslagen', 'success');
        break;
    }
    parent::postProcess();
  }

  /**
   * Method to save the top level domain correction settings
   *
   * @throws CiviCRM_API3_Exception
   */
  private function saveTopSettings() {
    $sources = array();
    $targets = array();
    foreach ($this->_submitValues as $key => $value) {
      if (substr($key,0,11) == 'top_source_') {
        $sources[] = $value;
      }
      if (substr($key,0,11) == 'top_target_') {
        $targets[] = $value;
      }
    }
    $tops = array();
    foreach ($sources as $sourceId => $sourceValue) {
      if (!empty($sourceValue) && isset($targets[$sourceId])) {
        $tops[$sourceValue] = $targets[$sourceId];
      }
    }
    $emailCorrect = new CRM_Emailcorrector_EmailCorrect();
    civicrm_api3('Setting', 'create', array(
      $emailCorrect->getTopSettingName() => $tops));
  }

  /**
   * Method to save the second level domain correction settings
   *
   * @throws CiviCRM_API3_Exception
   */
  private function saveSecondSettings() {
    $sources = array();
    $targets = array();
    foreach ($this->_submitValues as $key => $value) {
      if (substr($key,0,14) == 'second_source_') {
        $sources[] = $value;
      }
      if (substr($key,0,14) == 'second_target_') {
        $targets[] = $value;
      }
    }
    $seconds = array();
    foreach ($sources as $sourceId => $sourceValue) {
      if (!empty($sourceValue) && isset($targets[$sourceId])) {
        $seconds[$sourceValue] = $targets[$sourceId];
      }
    }
    $emailCorrect = new CRM_Emailcorrector_EmailCorrect();
    civicrm_api3('Setting', 'create', array(
      $emailCorrect->getSecondSettingName() => $seconds));
  }

  /**
   * Method to save the fake settings
   *
   * @throws CiviCRM_API3_Exception
   */
  private function saveFakeSettings() {
    $fakeEmails = array();
    foreach ($this->_submitValues as $key => $value) {
      if (substr($key,0,11) == 'fake_email_') {
        if (!empty($value)) {
          $fakeEmails[] = $value;
        }
      }
    }
    $emailCorrect = new CRM_Emailcorrector_EmailCorrect();
    civicrm_api3('Setting', 'create', array(
      $emailCorrect->getFakeSettingName() => $fakeEmails));
  }


  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

  /**
   * Overridden method to add validation rules
   * @throws HTML_QuickForm_Error
   */
  public function addRules() {
    if ($this->_type == 'top' || $this->_type == 'second') {
      $this->addFormRule(array('CRM_Emailcorrector_Form_Settings', 'validateSourceTarget'));
    }
  }

  /**
   * Method to validate creation year
   *
   * @param $fields
   * @return bool|array
   */
  public static function validateSourceTarget($fields) {
    if (isset($fields['type']) && !empty($fields['type'])) {
      switch ($fields['type']) {
        case "top":
          $validSource = "top_source_";
          $validTarget = "top_target_";
          $validLength = 11;
          break;
        case "second":
          $validSource = "second_source_";
          $validTarget = "second_target_";
          $validLength = 14;
          break;
        default:
          CRM_Core_Error::createError('Not a valid type in '.__METHOD__.' (extension be.aivl.emailcorrector');
          break;
      }
    }
    foreach ($fields as $fieldName => $fieldValue) {
      // only validate if field name contains valid part
      if (substr($fieldName, 0, $validLength) == $validSource) {
        $sourceName = $validSource.substr($fieldName, $validLength);
        $targetName = $validTarget.substr($fieldName, $validLength);
        if (!empty($fields[$sourceName]) && empty($fields[$targetName])) {
          $errors[$targetName] = ts('Beide velden moeten of leeg of gevuld zijn');
        }
        if (!empty($fields[$targetName]) && empty($fields[$sourceName])) {
          $errors[$sourceName] = ts('Beide velden moeten of leeg of gevuld zijn');
        }
      }
    }
    if (!empty($errors)) {
      return $errors;
    } else {
      return TRUE;
    }
  }

}
