<?php
/**
 * Class for EmailCorrect
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 13 Dec 2017
 * @license AGPL-3.0
 */

class CRM_Emailcorrector_EmailCorrect {

  private $_topSettingName = NULL;
  private $_secondSettingName = NULL;
  private $_fakeSettingName = NULL;
  private $_fixQueryParams = array();
  private $_fixQueryIndex = NULL;
  private $_topSettings = array();
  private $_secondSettings = array();
  private $_fakeSettings = array();

  /**
   * CRM_Emailcorrector_EmailCorrect constructor.
   */
  function __construct() {
    $this->_topSettingName = "be.aivl.emailcorrector:top_level_domain_corrections";
    $this->_secondSettingName = "be.aivl.emailcorrector:second_level_domain_corrections";
    $this->_fakeSettingName = "be.aivl.emailcorrector:fake_email_addresses";
  }

  /**
   * Getter for top setting name
   *
   * @return null|string
   */
  public function getTopSettingName () {
    return $this->_topSettingName;
  }

  /**
   * Getter for second setting name
   *
   * @return null|string
   */
  public function getSecondSettingName() {
    return $this->_secondSettingName;
  }

  /**
   * Getter for fake setting name
   *
   * @return null|string
   */
  public function getFakeSettingName() {
    return $this->_fakeSettingName;
  }

  /**
   * Method to set the required settings with their initial values if they do not exist yet
   * 
   * @throws CiviCRM_API3_Exception
   */
  public function initializeSettings() {
    $topLevelDomainCorrections = array(
      'con' => 'com',
      'couk' => 'co.uk',
      'cpm' => 'com',
    );
    $this->createSettingIfNotExist($this->_topSettingName, $topLevelDomainCorrections);

    $secondLevelDomainCorrections = array(
      'gmai' => 'gmail',
      'gamil' => 'gmail',
      'gmial' => 'gmail',
      'hotmai' => 'hotmail',
      'hotmal' => 'hotmail',
      'hotmil' => 'hotmail',
      'hotmial' => 'hotmail',
      'htomail' => 'hotmail',
    );
    $this->createSettingIfNotExist($this->_secondSettingName, $secondLevelDomainCorrections);

    $fakeEmailAddresses = array(
      'nomail@amnesty-international.be',
      'nomail@amnesty.be',
    );
    $this->createSettingIfNotExist($this->_fakeSettingName, $fakeEmailAddresses);
  }

  /**
   * Method to create setting if it does not exist
   * @param $name
   * @param $values
   * @throws CiviCRM_API3_Exception
   */
  private function createSettingIfNotExist($name, $values) {
    $result = civicrm_api3('Setting', 'getvalue', array(
      'name' => $name,
      'return' => 'value',
    ));
    if (empty($result)) {
      civicrm_api3('Setting', 'create', array($name => $values));
    }
  }

  /**
   * Method to fix the emails (correct according to settings and remove fakes
   *
   * @throws CiviCRM_API3_Exception
   */
  public function fixEmails() {
    $this->_fixQueryIndex = 0;
    $this->_fixQueryParams = array();
    $this->_fakeSettings = civicrm_api3('Setting', 'getvalue',array(
      'name' => $this->_fakeSettingName,));
    $this->_topSettings = civicrm_api3('Setting', 'getvalue', array(
      'name' => $this->_topSettingName,));
    $this->_secondSettings = civicrm_api3('Setting', 'getvalue', array(
      'name' => $this->_secondSettingName,
    ));
    $fakeWhere = $this->setFakeWhereClause();
    $topWhere = $this->setTopWhereClause();
    $secondWhere = $this->setSecondWhereClause();
    $query = "SELECT id AS email_id, email FROM civicrm_email WHERE ";
    if ($fakeWhere) {
      $query .= $fakeWhere;
    }
    if ($topWhere) {
      $query .= " OR ".$topWhere;
    }
    if ($secondWhere) {
      $query .= " OR ".$secondWhere;
    }
    if (!empty($this->_fixQueryParams)) {
      $dao = CRM_Core_DAO::executeQuery($query, $this->_fixQueryParams);
      while ($dao->fetch()) {
        if ($this->removeFakeEmail($dao) == FALSE) {
          $this->correctEmail($dao);
        }
      }
    }
    return;
  }

  /**
   * Method to remove fake email address if applicable
   *
   * @param $dao
   * @return bool
   * @throws CiviCRM_API3_Exception
   */
  private function removeFakeEmail($dao) {
    if (in_array($dao->email, $this->_fakeSettings)) {
      civicrm_api3('Email', 'delete', array('id' => $dao->email_id));
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Method to correct email address if applicable
   *
   * @param $dao
   */
  private function correctEmail($dao) {
    $emailChanged = FALSE;
    //split email
    $atParts = explode('@', $dao->email);
    if (isset($atParts[1])) {
      //now split second part on dot
      $dotParts = explode('.', $atParts[1]);
      //check if first part occurs in secondSettings
      if (array_key_exists($dotParts[0], $this->_secondSettings)) {
        $dotParts[0] = $this->_secondSettings[$dotParts[0]];
        $emailChanged = TRUE;
      }
      // check if second part occurs in topSettings
      if (array_key_exists($dotParts[1], $this->_topSettings)) {
        $dotParts[1] = $this->_topSettings[$dotParts[1]];
        $emailChanged = TRUE;
      }
      // if email changed, correct in DB
      if ($emailChanged) {
        $newEmail = $atParts[0]."@".implode('.', $dotParts);
        $query = "UPDATE civicrm_email SET email = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($query, array(
          1 => array($newEmail, 'String'),
          2 => array($dao->email_id, 'Integer'),
        ));
      }
    }
    return;
  }

  /**
   * Method to set the top corrections where clause and related params
   *
   * @return bool|string
   */
  private function setTopWhereClause() {
    $whereFields = array();
    if (!empty($this->_topSettings)) {
      foreach ($this->_topSettings as $source => $target) {
        $this->_fixQueryIndex++;
        $this->_fixQueryParams[$this->_fixQueryIndex] = array('%.'.$source. '%', 'String');
        $whereFields[] = "email LIKE %".$this->_fixQueryIndex;
      }
    }
    if (!empty($whereFields)) {
      return "(".implode(' OR ', $whereFields).")";
    }
    return FALSE;
  }

  /**
   * Method to set the second corrections where clause and related params
   *
   * @return bool|string
   */
  private function setSecondWhereClause() {
    $whereFields = array();
    if (!empty($this->_secondSettings)) {
      foreach ($this->_secondSettings as $source => $target) {
        $this->_fixQueryIndex++;
        $this->_fixQueryParams[$this->_fixQueryIndex] = array('%@'.$source. '.%', 'String');
        $whereFields[] = "email LIKE %".$this->_fixQueryIndex;
      }
    }
    if (!empty($whereFields)) {
      return "(".implode(' OR ', $whereFields).")";
    }
    return FALSE;
  }

  /**
   * Method to set the fake emails where clause and related params
   *
   * @return bool|string
   */
  private function setFakeWhereClause() {
    $whereFields = array();
    if (!empty($this->_fakeSettings)) {
      foreach ($this->_fakeSettings as $fakeEmail) {
        $this->_fixQueryIndex++;
        $this->_fixQueryParams[$this->_fixQueryIndex] = array($fakeEmail, 'String');
        $whereFields[] = "%".$this->_fixQueryIndex;
      }
    }
    if (!empty($whereFields)) {
      return "email IN (".implode(',', $whereFields).")";
    }
    return FALSE;
  }
}