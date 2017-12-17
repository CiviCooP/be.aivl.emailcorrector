<?php
use CRM_Emailcorrector_ExtensionUtil as E;

class CRM_Emailcorrector_Page_Settings extends CRM_Core_Page {

  private $_topSettings = array();
  private $_secondSettings = array();
  private $_fakeSettings = array();

  /**
   * Method to run the page
   *
   * @return null|void
   * @throws CiviCRM_API3_Exception
   */
  function run() {
    CRM_Utils_System::setTitle(ts('AIVL Instellingen voor Email Corrector'));
    $emailCorrect = new CRM_Emailcorrector_EmailCorrect();
    $this->_topSettings = civicrm_api3('Setting', 'getvalue', array(
      'name' => $emailCorrect->getTopSettingName(),
    ));
    $this->_secondSettings = civicrm_api3('Setting', 'getvalue', array(
      'name' => $emailCorrect->getSecondSettingName(),
    ));
    $this->_fakeSettings = civicrm_api3('Setting', 'getvalue', array(
      'name' => $emailCorrect->getFakeSettingName(),
    ));
    $rows = array(
      $this->buildTopRow(),
      $this->buildSecondRow(),
      $this->buildFakeRow(),
    );
    $this->assign('rows', $rows);
    parent::run();
  }

  /**
   * Method to build the top corrections row
   *
   * @return array
   */
  private function buildTopRow() {
    $row = array();
    $row['label'] = ts('Te corrigeren achter de punt');
    $values = array();
    foreach ($this->_topSettings as $topSource => $topTarget) {
      $values[] = "{".$topSource."} ".ts("wordt gecorrigeerd naar {".$topTarget."}");
    }
    $row['values'] = implode(" / ", $values);
    $updateUrl = CRM_Utils_System::url('civicrm/emailcorrector/form/settings', 'reset=1&action=update&type=top', true);
    $row['edit'] = '<a class="action-item" title="Update" href="'.$updateUrl.'">'.ts('Edit').'</a>';
    return $row;
  }

  /**
   * Method to build the second corrections row
   *
   * @return array
   */
  private function buildSecondRow() {
    $row = array();
    $row['label'] = ts('Te corrigeren voor de punt');
    $values = array();
    foreach ($this->_secondSettings as $secondSource => $secondTarget) {
      $values[] = "{".$secondSource."} ".ts("wordt gecorrigeerd naar {".$secondTarget."}");
    }
    $row['values'] = implode(" / ", $values);
    $updateUrl = CRM_Utils_System::url('civicrm/emailcorrector/form/settings', 'reset=1&action=update&type=second', true);
    $row['edit'] = '<a class="action-item" title="Update" href="'.$updateUrl.'">'.ts('Edit').'</a>';
    return $row;
  }

  /**
   * Method to build the fake row
   *
   * @return array
   */
  private function buildFakeRow() {
    $row = array();
    $row['label'] = ts('Emailadressen die verwijderd worden');
    $values = array();
    foreach ($this->_fakeSettings as $fakeEmail) {
      $values[] = $fakeEmail;
    }
    $row['values'] = implode(" / ", $values);
    $updateUrl = CRM_Utils_System::url('civicrm/emailcorrector/form/settings', 'reset=1&action=update&type=fake', true);
    $row['edit'] = '<a class="action-item" title="Update" href="'.$updateUrl.'">'.ts('Edit').'</a>';
    return $row;
  }

}
