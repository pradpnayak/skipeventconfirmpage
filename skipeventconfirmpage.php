<?php

require_once 'skipeventconfirmpage.civix.php';
use CRM_Skipeventconfirmpage_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function skipeventconfirmpage_civicrm_config(&$config) {
  _skipeventconfirmpage_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function skipeventconfirmpage_civicrm_xmlMenu(&$files) {
  _skipeventconfirmpage_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function skipeventconfirmpage_civicrm_install() {
  _skipeventconfirmpage_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function skipeventconfirmpage_civicrm_postInstall() {
  _skipeventconfirmpage_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function skipeventconfirmpage_civicrm_uninstall() {
  _skipeventconfirmpage_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function skipeventconfirmpage_civicrm_enable() {
  _skipeventconfirmpage_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function skipeventconfirmpage_civicrm_disable() {
  _skipeventconfirmpage_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function skipeventconfirmpage_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _skipeventconfirmpage_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function skipeventconfirmpage_civicrm_managed(&$entities) {
  _skipeventconfirmpage_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function skipeventconfirmpage_civicrm_caseTypes(&$caseTypes) {
  _skipeventconfirmpage_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function skipeventconfirmpage_civicrm_angularModules(&$angularModules) {
  _skipeventconfirmpage_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function skipeventconfirmpage_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _skipeventconfirmpage_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function skipeventconfirmpage_civicrm_entityTypes(&$entityTypes) {
  _skipeventconfirmpage_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function skipeventconfirmpage_civicrm_themes(&$themes) {
  _skipeventconfirmpage_civix_civicrm_themes($themes);
}

/**
 * Implements hook_civicrm_alterReportVar().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterReportVar
 */
function skipeventconfirmpage_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Event_Form_ManageEvent_Registration') {
    if (!$form->elementExists('is_confirm_enabled')) {
      $form->assign('is_monetary', 0);
      $form->addYesNo('is_confirm_enabled',
        ts('Use a confirmation screen?'), NULL, NULL,
        ['onclick' => "return showHideByValue('is_confirm_enabled','','confirm_screen_settings','block','radio',false);"]
      );
    }
  }
}

/**
 * Implements hook_civicrm_alterReportVar().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterReportVar
 */
function skipeventconfirmpage_civicrm_postProcess($formName, &$form) {
  if (in_array($formName, ['CRM_Event_Form_Registration_Register', 'CRM_Event_Form_Registration_AdditionalParticipant'])) {
    if ($form->_values['event']['is_monetary'] && !$form->_values['event']['is_confirm_enabled']) {

      $redirect = FALSE;
      switch ($formName) {
        case 'CRM_Event_Form_Registration_Register':
          if (empty($form->_submitValues['additional_participants'])) {
            $redirect = TRUE;
          }
          break;

        case 'CRM_Event_Form_Registration_AdditionalParticipant':
          $params = $form->getVar('_params');
          if (!empty($params[0]['additional_participants'])
            && $form->isLastParticipant()
          ) {
            $redirect = TRUE;
          }
          break;
      }

      if ($redirect) {
        $confirmForm = &$form->controller->_pages['Confirm'];
        $confirmForm->preProcess();
        $confirmForm->buildQuickForm();
        $data = &$form->controller->container();
        $data['valid']['Confirm'] = 1;
        $qfKey = $form->controller->_key;
        $confirmForm->postProcess();
        CRM_Utils_System::redirect(CRM_Utils_System::url(
          'civicrm/event/register', "_qf_ThankYou_display=1&qfKey=$qfKey",
          TRUE, NULL, FALSE
        ));
      }
    }
  }
}
