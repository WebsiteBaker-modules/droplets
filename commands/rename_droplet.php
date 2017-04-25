<?php
/**
 *
 * @category        module
 * @package         droplet
 * @author          Ruud Eisinga (Ruud) John (PCWacht)
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: rename_droplet.php 65 2017-03-03 21:38:16Z manu $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2.10/branches/wb/modules/droplets/commands/rename_droplet.php $
 * @lastmodified    $Date: 2017-03-03 22:38:16 +0100 (Fr, 03. Mrz 2017) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
// Get id
if(!isset($iDropletAddId)) {
    $droplet_id = (@$droplet_id?:'droplet_id');
    $droplet_id = ($oApp->checkIDKEY($droplet_id, false, ''));
}
if ($droplet_id === false) {
    $oApp->print_error('MODIFY_DROPLET_IDKEY::'.$oTrans->MESSAGE_GENERIC_SECURITY_ACCESS, $ToolUrl);
    exit();
}
$sOverviewDroplets = $oTrans->DR_TEXTDROPLETS;
$sTimeStamp = (@$sTimeStamp?:'');
$modified_by = $oApp->get_user_id();
$sHeaderDroplet = $DR_TEXT['ADD_DROPLET'];
$sDropletHelp = $oTrans->DROPLET_HELP_DROPLET_RENAME_ADD;
if (($droplet_id > 0)) {
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_droplets` '
          . 'WHERE `id` = '.$droplet_id;
    $oDroplet = $oDb->query($sql);
    $aDroplet = $oDroplet->fetchRow(MYSQLI_ASSOC);
    $content  = (htmlspecialchars($aDroplet['code']));
    $DropletName    = $aDroplet['name'];
    $sSubmitButton  = $TEXT['SAVE'];
    $iDropletIdKey  = $oApp->getIDKEY($droplet_id);
    $iDropletAddId  = $droplet_id;
    $sHeaderDroplet = $oTrans->DROPLET_HEADER_RENAME_DROPLET;
    $sDropletHelp   = $oTrans->DROPLET_HELP_DROPLET_RENAME;
} else if (isset($aCopyDroplet)){
    $aDroplet = $aCopyDroplet;
    $DropletName   = $aDroplet['name'];
    $sSubmitButton = $TEXT['ADD'];
    $iDropletIdKey = $droplet_id;
} else {
    $aDroplet = array();
    // check if it is a normal add or a copy
    if (sizeof($aDroplet)==0) {
        $aDroplet = array(
            'id' => 0,
            'name' => 'Dropletname',
            'code' => 'return true;',
            'description' => '',
            'modified_when' => 0,
            'modified_by' => 0,
            'active' => 0,
            'admin_edit' => 0,
            'admin_view' => 0,
            'show_wysiwyg' => 0,
            'comments' => ''
            );
        $DropletName   = $aDroplet['name'];
        $content = '';
    }
    $sDropletHelp = $oTrans->DROPLET_HELP_DROPLET_RENAME_ADD;
    $sSubmitButton = $TEXT['ADD'];
    $iDropletIdKey = $oApp->getIDKEY($aDroplet['id']);
}
    $aFtan = $admin->getFTAN('');
    // prepare default data for phplib and twig
    $aTplData = array (
        'action' => $action,
        'FTAN_NAME' => $aFtan['name'],
        'FTAN_VALUE' => $aFtan['value'],
        'DropletName' => $aDroplet['name'],
        'iDropletAddId' => $iDropletAddId,
        'iDropletIdKey' => $iDropletIdKey,
        'show_wysiwyg' => $aDroplet['show_wysiwyg'],
        'sSubmitButton' => $sSubmitButton,
        'HEADER_DROPLET' => $sHeaderDroplet,
        'sDropletHelp' => $sDropletHelp,
        );
// Create new template object with phplib
    $oTpl = new Template($sAddonThemePath, 'keep' );
    $oTpl->set_file('page', 'rename_droplet.htt');
    $oTpl->set_block('page', 'main_block', 'main');
    $oTpl->set_var($aLang);
    $oTpl->set_var($aTplDefaults);
    $oTpl->set_var($aTplData);
    $oTpl->set_block('main_block', 'show_admin_edit_block', 'show_admin_edit');
    if ($admin->ami_group_member('1') || $aDroplet['admin_edit'] == 0 ) {
        $oTpl->parse('show_admin_edit', 'show_admin_edit_block', true);
    } else {
        $oTpl->set_block('show_admin_edit', '');
    }
/*-- finalize the page -----------------------------------------------------------------*/
    $oTpl->parse('main', 'main_block', false);
    $oTpl->pparse('output', 'page');
