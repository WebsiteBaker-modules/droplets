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
 * @version         $Id: select_archiv.php 65 2017-03-03 21:38:16Z manu $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2.10/branches/wb/modules/droplets/commands/select_archiv.php $
 * @lastmodified    $Date: 2017-03-03 22:38:16 +0100 (Fr, 03. Mrz 2017) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */
    $sBackupDir = $sAddonRel.'/data/archiv/';
    $aZipFiles = glob($oReg->AppPath.$sAddonRel.'/data/archiv/*.zip', GLOB_NOSORT); 
    $aFtan = $admin->getFTAN('');
    // prepare default data for phplib and twig
    $aTplData = array (
        'FTAN_NAME' => $aFtan['name'],
        'FTAN_VALUE' => $aFtan['value'],
        );
// Create new template object with phplib
    $oTpl = new Template($sAddonThemePath, 'keep' );
    $oTpl->set_file('page', 'select_archiv.htt');
    $oTpl->set_block('page', 'main_block', 'main');
    $oTpl->set_var($aLang);
    $oTpl->set_var($aTplDefaults);
    $oTpl->set_var($aTplData);
    $oTpl->set_block('main_block', 'list_archiv_block', 'list_archiv');
    foreach( $aZipFiles as $files ) {
        $value =  basename($files);
        $files = str_replace($oReg->AppPath, '', $files );
        $oTpl->set_var('files', $files);
        $oTpl->set_var('value', $value);
        $oTpl->parse('list_archiv', 'list_archiv_block', true);
    }

/*-- finalize the page -----------------------------------------------------------------*/
    $oTpl->parse('main', 'main_block', false);
    $oTpl->pparse('output', 'page');

