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
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function skipeventconfirmpage_civicrm_install() {
  _skipeventconfirmpage_civix_civicrm_install();
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
      // The description wording doesn't make sense now, but it's hardcoded in the template. It doesn't really add any useful info so get rid of it.
      \Civi::resources()->addScript('CRM.$("tr.crm-event-manage-registration-form-block-is_confirm_enabled div.description").text("");');
    }
  }

  if (in_array($formName, ['CRM_Event_Form_Registration_Register', 'CRM_Event_Form_Registration_AdditionalParticipant'])) {
    if ($form->_values['event']['is_monetary'] && !$form->_values['event']['is_confirm_enabled']) {
      $changeButtonTitle = FALSE;
      switch ($formName) {
        case 'CRM_Event_Form_Registration_Register':
          $changeButtonTitle = TRUE;
          break;

        case 'CRM_Event_Form_Registration_AdditionalParticipant':
          $params = $form->getVar('_params');
          if (!empty($params[0]['additional_participants'])
            && str_replace('Participant_', '', $form->getVar('_name')) == $params[0]['additional_participants']
          ) {
            $changeButtonTitle = TRUE;
          }
          break;
      }
      if ($changeButtonTitle) {
        $buttons = &$form->getElement('buttons');
        foreach ($buttons->_elements as &$button) {
          if ($button->_attributes['type'] == 'submit' && $button->_attributes['name'] == '_qf_Register_upload') {
            $button->_content = '<i aria-hidden="true" class="crm-i fa-chevron-right"></i> ' . ts('Register');
            break;
          }
        }
      }
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
