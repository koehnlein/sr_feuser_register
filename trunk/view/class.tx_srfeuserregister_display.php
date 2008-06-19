<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2008 Stanislas Rolland <stanislas.rolland(arobas)sjbr.ca)>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Part of the sr_feuser_register (Frontend User Registration) extension.
 *
 * display functions
 *
 * $Id$
 *
 * @author Kasper Skaarhoj <kasper2007@typo3.com>
 * @author Stanislas Rolland <stanislas.rolland(arobas)sjbr.ca>
 * @author Franz Holzinger <contact@fholzinger.com>
 *
 * @package TYPO3
 * @subpackage sr_feuser_register
 *
 *
 */


class tx_srfeuserregister_display {
	var $pibase;
	var $conf = array();
	var $config = array();
	var $data;
	var $marker;
	var $tca;
	var $control;
	var $controlData;
	var $extKey;  // The extension key.
	var $cObj;


	function init(&$pibase, &$conf, &$config, &$data, &$marker, &$tca, &$control)	{
		$this->pibase = &$pibase;
		$this->conf = &$conf;
		$this->config = &$config;
		$this->data = &$data;
		$this->marker = &$marker;
		$this->tca = &$tca;
		$this->control = &$control;
		$this->controlData = &$control->controlData;
		$this->extKey = $pibase->extKey;
		$this->cObj = &$pibase->cObj;
	}


	/**
	* Displays the record update form
	*
	* @param array  $origArr: the array coming from the database
	* @return string  the template with substituted markers
	*/
	function editForm($theTable, $dataArray, $origArr,$cmd,$cmdKey,$mode) {
		global $TSFE;

		$prefixId = $this->controlData->getPrefixId();
		$currentArray = array_merge($origArr, $dataArray);
		$templateCode = $this->cObj->getSubpart($this->data->getTemplateCode(), '###TEMPLATE_EDIT'.$this->marker->getPreviewLabel().'###');

		if (!$this->conf['linkToPID'] || !$this->conf['linkToPIDAddButton'] || !($mode == MODE_PREVIEW || !$this->conf['edit.']['preview'])) {
			$templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_LINKTOPID_ADD_BUTTON###', '');
		}

		$failure = t3lib_div::_GP('noWarnings') ? '': $this->controlData->getFailure();
		if (!$failure) {
			$templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_REQUIRED_FIELDS_WARNING###', '');
		}
		$markerArray = $this->marker->getArray();
		$this->marker->addMd5EventsMarkers($markerArray, 'edit', $this->controlData->getUseMd5Password());

		$templateCode = $this->removeRequired($templateCode, $failure);
		$markerArray = $this->marker->fillInMarkerArray($markerArray, $currentArray, '', TRUE, 'FIELD_', TRUE);
		$this->marker->addStaticInfoMarkers($markerArray, $currentArray);
		$this->tca->addTcaMarkers($markerArray, $currentArray, $origArr, $cmd, $cmdKey, $theTable, true);
		$this->tca->addTcaMarkers($markerArray, $currentArray, $origArr, $cmd, $cmdKey, $theTable);
		$this->marker->addLabelMarkers($markerArray, $theTable, $currentArray, $origArr, array(), $this->controlData->getRequiredArray(), $this->data->getFieldList(), $this->tca->TCA['columns'], false);
		$this->marker->addFileUploadMarkers('image', $markerArray, $cmd, $cmdKey, $currentArray, $this->controlData->getMode() == MODE_PREVIEW);
		$templateCode = $this->marker->removeStaticInfoSubparts($templateCode, $markerArray);
		$markerArray['###HIDDENFIELDS###'] .= chr(10) . '<input type="hidden" name="FE['.$theTable.'][uid]" value="'.$currentArray['uid'].'" />';
		if ($theTable != 'fe_users') {
			$authObj = &t3lib_div::getUserObj('&tx_srfeuserregister_auth');
			$markerArray['###HIDDENFIELDS###'] .= chr(10) . '<input type="hidden" name="'.$prefixId.'[aC]" value="'.$authObj->authCode($origArr).'" />';
			$markerArray['###HIDDENFIELDS###'] .= chr(10) . '<input type="hidden" name="'.$prefixId . '[cmd]" value="edit" />';
		} elseif ($this->conf[$cmdKey.'.']['useEmailAsUsername'] && $this->conf['templateStyle'] != 'css-styled') {
			$markerArray['###HIDDENFIELDS###'] .= chr(10) . '<input type="hidden" name="FE['.$theTable.'][username]" value="'.$currentArray['username'].'" />';
			$markerArray['###HIDDENFIELDS###'] .= chr(10) . '<input type="hidden" name="FE['.$theTable.'][email]" value="'.$currentArray['email'].'" />';
		}
		$this->marker->addHiddenFieldsMarkers($markerArray, $cmdKey, $mode, $currentArray);
		$content = $this->cObj->substituteMarkerArray($templateCode, $markerArray);
		if ($this->conf['templateStyle'] != 'css-styled' || $mode != MODE_PREVIEW) {
			$form = $this->pibase->pi_getClassName($theTable.'_form');
			$modData = $this->data->modifyDataArrForFormUpdate($currentArray);
			$fields = $this->data->getFieldList().$this->data->getAdditionalUpdateFields();
			$updateJS = $this->cObj->getUpdateJS($modData, $form, 'FE['.$theTable.']', $fields);
			$content .= $updateJS;
			$TSFE->additionalHeaderData['JSincludeFormupdate'] = '<script type="text/javascript" src="' . $TSFE->absRefPrefix . t3lib_extMgm::siteRelPath('sr_feuser_register') .'scripts/jsfunc.updateform.js"></script>';
		}
		return $content;
	}	// editForm


	/**
	* Generates the record creation form
	*
	* @return string  the template with substituted markers
	*/
	function createScreen($cmd='create', $cmdKey, $mode, $theTable, $dataArray) {
		global $TSFE;

		$templateCode = &$this->data->getTemplateCode();
		$prefixId = $this->controlData->getPrefixId();
		$origArr = $this->data->getOrigArray();
		$currentArray = array_merge($origArr, $dataArray);

		if ($this->conf['create']) {

				// Call all beforeConfirmCreate hooks before the record has been shown and confirmed
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey][$prefixId]['registrationProcess'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey][$prefixId]['registrationProcess'] as $classRef) {
					$hookObj= &t3lib_div::getUserObj($classRef);
					if (method_exists($hookObj,'registrationProcess_beforeConfirmCreate')) {
						$hookObj->registrationProcess_beforeConfirmCreate($dataArray, $this->controlData);
					}
				}
			}

			$key = ($cmd == 'invite') ? 'INVITE': 'CREATE';
			$markerArray = $this->marker->getArray();
			$this->marker->addMd5EventsMarkers($markerArray, 'create', $this->controlData->getUseMd5Password());

			$subpartKey = ((!($theTable == 'fe_users' && $GLOBALS['TSFE']->loginUser) || $cmd == 'invite') ? '###TEMPLATE_'.$key.$this->marker->getPreviewLabel().'###':'###TEMPLATE_CREATE_LOGIN###');
			$templateCode = $this->cObj->getSubpart($templateCode, $subpartKey);

			$failure = t3lib_div::_GP('noWarnings') ? FALSE: $this->controlData->getFailure();
			if ($failure==FALSE)	{
				$templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_REQUIRED_FIELDS_WARNING###', '');
			}
			$templateCode = $this->removeRequired($templateCode, $failure);
			$markerArray = $this->marker->fillInMarkerArray($markerArray, $currentArray, '',TRUE, 'FIELD_', TRUE);
			$this->marker->addStaticInfoMarkers($markerArray, $dataArray);
			$this->tca->addTcaMarkers($markerArray, $dataArray, $origArr, $cmd, $cmdKey, $theTable);
			$this->marker->addFileUploadMarkers('image', $markerArray, $cmd, $cmdKey, $dataArray, $this->controlData->getMode() == MODE_PREVIEW);
			$this->marker->addLabelMarkers($markerArray, $theTable, $dataArray, $origArr, array(), $this->controlData->getRequiredArray(), $this->data->getFieldList(), $this->tca->TCA['columns'], false);

			$templateCode = $this->marker->removeStaticInfoSubparts($templateCode, $markerArray);
			$this->marker->addHiddenFieldsMarkers($markerArray, $cmdKey, $mode, $dataArray);

			$content = $this->cObj->substituteMarkerArray($templateCode, $markerArray);
			if ($this->conf['templateStyle'] != 'css-styled' || $mode != MODE_PREVIEW) {
				$form = $this->pibase->pi_getClassName($theTable.'_form');
				$updateJScontent = $this->cObj->getUpdateJS($this->data->modifyDataArrForFormUpdate($dataArray), $form, 'FE['.$theTable.']', $this->data->fieldList.$this->data->additionalUpdateFields);
				$content .= $updateJScontent;
				$TSFE->additionalHeaderData['JSincludeFormupdate'] = '<script type="text/javascript" src="' . $TSFE->absRefPrefix . t3lib_extMgm::siteRelPath('sr_feuser_register') .'scripts/jsfunc.updateform.js"></script>';
			}
		}
		return $content;
	} // createScreen


	/**
	* Checks if the edit form may be displayed; if not, a link to login
	*
	* @return string  the template with substituted markers
	*/
	function editScreen($theTable, $dataArray, $origArr, $cmd, $cmdKey, $mode) {
		global $TSFE;

			// If editing is enabled
		if ($this->conf['edit']) {
			$authObj = &t3lib_div::getUserObj('&tx_srfeuserregister_auth');

			if($theTable != 'fe_users' && $this->conf['setfixed.']['EDIT.']['_FIELDLIST']) {
				$fD = t3lib_div::_GP('fD', 1);
				$fieldArr = array();
				if (is_array($fD)) {
					reset($fD);
					foreach($fD as $field => $value) {
						$origArr[$field] = rawurldecode($value);
						$fieldArr[] = $field;
					}
				}
				$theCode = $authObj->setfixedHash($origArr, $origArr['_FIELDLIST']);
			}
			if (is_array($origArr))	{
				$origArr = $this->data->parseIncomingData($origArr);
			}

			if (
				is_array($origArr) && 
				( ($theTable == 'fe_users' && $TSFE->loginUser) || $authObj->aCAuth($origArr) || $theCode && !strcmp($authObj->authCode, $theCode) ) 
			) {
				$markerArray = $this->marker->getArray();
				$this->marker->addMd5EventsMarkers($markerArray, 'edit', $this->controlData->getUseMd5Password());
				$this->marker->setArray($markerArray);
				// Must be logged in OR be authenticated by the aC code in order to edit
				// If the recUid selects a record.... (no check here)
				if ( !strcmp($authObj->authCode, $theCode) || $authObj->aCAuth($origArr) || $this->cObj->DBmayFEUserEdit($theTable, $origArr, $GLOBALS['TSFE']->fe_user->user, $this->conf['allowedGroups'], $this->conf['fe_userEditSelf'])) {
					// Display the form, if access granted.
					$content = $this->editForm($theTable, $dataArray, $origArr, $cmd, $cmdKey, $mode);
				} else {
					// Else display error, that you could not edit that particular record...
					$content = $this->getPlainTemplate($this->data->getTemplateCode(), '###TEMPLATE_NO_PERMISSIONS###', $dataArray, $origArr);
				}
			} else {
				// This is if there is no login user. This must tell that you must login. Perhaps link to a page with create-user or login information.
				$content = $this->getPlainTemplate($this->data->getTemplateCode(), '###TEMPLATE_AUTH###', $dataArray, $this->data->getOrigArray());
			}
		} else {
			$langObj = &t3lib_div::getUserObj('&tx_srfeuserregister_lang');
			$content .= $langObj->pi_getLL('internal_edit_option');
		}
		return $content;
	}	// editScreen


	/**
		* This is basically the preview display of delete
		*
		* @return string  the template with substituted markers
		*/
	function deleteScreen($theTable, $dataArray, $origArr) {
		if ($this->conf['delete']) {
			$prefixId = $this->controlData->getPrefixId();
			$templateCode = $this->data->getTemplateCode();
			$authObj = &t3lib_div::getUserObj('&tx_srfeuserregister_auth');

			// If deleting is enabled
			$origArr = $GLOBALS['TSFE']->sys_page->getRawRecord($theTable, $this->data->getRecUid());
			if ( ($theTable == 'fe_users' && $GLOBALS['TSFE']->loginUser) || $authObj->aCAuth($origArr)) {
				// Must be logged in OR be authenticated by the aC code in order to delete

				// If the recUid selects a record.... (no check here)
				if (is_array($origArr)) {
					if ($authObj->aCAuth($origArr) || $this->cObj->DBmayFEUserEdit($theTable, $origArr, $GLOBALS['TSFE']->fe_user->user, $this->conf['allowedGroups'], $this->conf['fe_userEditSelf'])) {
						$markerArray = $this->marker->getArray();
						// Display the form, if access granted.
						$markerArray['###HIDDENFIELDS###'] .= '<input type="hidden" name="rU" value="'.$this->data->getRecUid().'" />';
						if ( $theTable != 'fe_users' ) {
							$markerArray['###HIDDENFIELDS###'] .= '<input type="hidden" name="'.$prefixId .'[aC]" value="'.$authObj->authCode($origArr).'" />';
							$markerArray['###HIDDENFIELDS###'] .= '<input type="hidden" name="'.$prefixId .'[cmd]" value="delete" />';
						}
						$this->marker->setArray($markerArray);
						$content = $this->getPlainTemplate($templateCode, '###TEMPLATE_DELETE_PREVIEW###', $dataArray, $origArr);
					} else {
						// Else display error, that you could not edit that particular record...
						$content = $this->getPlainTemplate($templateCode, '###TEMPLATE_NO_PERMISSIONS###', $dataArray, $origArr);

					}
				}
			} else {
				// Finally this is if there is no login user. This must tell that you must login. Perhaps link to a page with create-user or login information.
				if ( $theTable == 'fe_users' ) {
					$content = $this->getPlainTemplate($templateCode, '###TEMPLATE_AUTH###', $origArr);

				} else {
					$content = $this->getPlainTemplate($templateCode, '###TEMPLATE_NO_PERMISSIONS###', $origArr);

				}
			}
		} else {
			$content .= 'Delete-option is not set in TypoScript';
		}
		return $content;
	}	// deleteScreen


	/**
	* Initializes a template, filling values for data and labels
	*
	* @param string  $key: the template key
	* @param array  $row: the data array, if any
	* @return string  the template with substituted parts and markers
	*/
	function getPlainTemplate($templateCode, $key, $origArr, $row = '') {

		$templateCode = $this->cObj->getSubpart($templateCode, $key);
		$markerArray = $this->marker->getArray();

		if (is_array($row))	{
			$markerArray = $this->marker->fillInMarkerArray($markerArray, $row, '',TRUE, 'FIELD_', TRUE);
		}
		$this->marker->addStaticInfoMarkers($markerArray, $row);
		$cmd = $this->controlData->getCmd();
		$cmdKey = $this->controlData->getCmdKey();
		$theTable = $this->controlData->getTable();
		$this->tca->addTcaMarkers($markerArray, $row, $origArr, $cmd, $cmdKey, $theTable, true);
		$this->marker->addLabelMarkers($markerArray, $theTable, $row, $origArr, array(), $this->controlData->getRequiredArray(), $this->data->getFieldList(), $this->tca->TCA['columns'], false);
		$templateCode = $this->marker->removeStaticInfoSubparts($templateCode, $markerArray);
		$rc = $this->cObj->substituteMarkerArray($templateCode, $markerArray);

		return $rc;
	}	// getPlainTemplate


	/**
		* Removes required and error sub-parts when there are no errors
		*
		* Works like this:
		* - Insert subparts like this ###SUB_REQUIRED_FIELD_".$theField."### that tells that the field is required, if it's not correctly filled in.
		* - These subparts are all removed, except if the field is listed in $failure string!
		* - Subparts like ###SUB_ERROR_FIELD_".$theField."### are also removed if there is no error on the field
		* - Remove also the parts of non-included fields, using a similar scheme!
		*
		* @param string  $templateCode: the content of the HTML template
		* @param string  $failure: the list of fields with errors
		* @return string  the template with susbstituted parts
		*/
	function removeRequired($templateCode, $failure = '') {
		$cmdKey = $this->controlData->getCmdKey();
		$requiredArray = $this->controlData->getRequiredArray();
		$includedFields = t3lib_div::trimExplode(',', $this->conf[$cmdKey.'.']['fields'], 1);
		if ($this->controlData->getFeUserData('preview') && !in_array('username', $includedFields)) {
			$includedFields[] = 'username';
		}
		$infoFields = explode(',', $this->data->fieldList);
		if (!t3lib_extMgm::isLoaded('direct_mail')) {
			$infoFields[] = 'module_sys_dmail_category';
			$infoFields[] = 'module_sys_dmail_html';
		}
		if (!$this->controlData->useCaptcha()) {
			$templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_INCLUDED_FIELD_captcha_response###', '');
		}

		foreach($infoFields as $k => $theField) {
			if (in_array(trim($theField), $requiredArray) ) {
				if (!t3lib_div::inList($failure, $theField)) {
					$templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_REQUIRED_FIELD_'.$theField.'###', '');
					$templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_ERROR_FIELD_'.$theField.'###', '');
				} else if (!$this->data->inError[$theField]) {
					$templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_ERROR_FIELD_'.$theField.'###', '');
				}
			} else {
				if (!in_array(trim($theField), $includedFields)) {
					$templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_INCLUDED_FIELD_'.$theField.'###', '');
				} else {
					$templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_REQUIRED_FIELD_'.$theField.'###', '');
					if (!t3lib_div::inList($failure, $theField)) {
						$templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_ERROR_FIELD_'.$theField.'###', '');
					}
					if (is_array($this->conf['parseValues.']) && strstr($this->conf['parseValues.'][$theField],'checkArray')) {
						$listOfCommands = t3lib_div::trimExplode(',', $this->conf['parseValues.'][$theField], 1);
						foreach($listOfCommands as $cmd) {
							$cmdParts = split('\[|\]', $cmd); // Point is to enable parameters after each command enclosed in brackets [..]. These will be in position 1 in the array.
							$theCmd = trim($cmdParts[0]);
							switch($theCmd) {
								case 'checkArray':
									$positions = t3lib_div::trimExplode(';', $cmdParts[1]);
									for($i=0; $i<10; $i++) {
										if(!in_array($i, $positions)) {
											$templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_INCLUDED_FIELD_'.$theField.'_'.$i.'###', '');
										}
									}
								break;
							}
						}
					}
				}
			}
		}
		return $templateCode;
	}	// removeRequired

	function removeHTMLComments($content) {
		return preg_replace('/<!(?:--[\s\S]*?--\s*)?>[\t\v\n\r\f]*/','',$content);
	}

	function replaceHTMLBr($content) {
		$rc = preg_replace('/<br\s?\/>/',chr(10),$content);
		return $rc;
	}
}


if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/sr_feuser_register/view/class.tx_srfeuserregister_display.php'])  {
  include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/sr_feuser_register/view/class.tx_srfeuserregister_display.php']);
}
?>
