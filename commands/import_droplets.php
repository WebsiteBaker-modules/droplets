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
 * @version         $Id: import_droplets.php 65 2017-03-03 21:38:16Z manu $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2.10/branches/wb/modules/droplets/commands/import_droplets.php $
 * @lastmodified    $Date: 2017-03-03 22:38:16 +0100 (Fr, 03. Mrz 2017) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
if( !$oApp->checkFTAN() ){
    $oApp->print_error($oTrans->MESSAGE_GENERIC_SECURITY_ACCESS, $ToolUrl );
    exit();
}

            if( isset( $_FILES['zipFiles'] ) && !$_FILES['zipFiles']['error']) {
                $aRequestVars['uploads']  = $_FILES['zipFiles'];
                $sArchiveFile = $_FILES['zipFiles']['tmp_name'];

                move_uploaded_file (
                     $_FILES['zipFiles']['tmp_name'] ,
                     $oReg->AppPath.'/temp/'. $_FILES['zipFiles']['name']
                );
                $sArchiveFile = ( $oReg->AppPath.'/temp/'. $_FILES['zipFiles']['name'] );
            } else {
                $sArchiveFile = ( $oReg->AppPath.$aRequestVars['zipFiles']);
            }
if (!is_readable( $sArchiveFile)) {
    msgQueue::add( $oTrans->DROPLET_MESSAGE_GENERIC_MISSING_ARCHIVE_FILE );
} else if ( is_readable( $sArchiveFile ) ) {

    if( !class_exists('PclZip',false) ) { require( $oReg->AppPath.'/include/pclzip/pclzip.lib.php'); }
    $oArchive = new PclZip( $sArchiveFile );
    $aFilesInArchiv = $oArchive->listContent();
    if ($aFilesInArchiv == 0) {
        msgQueue::add( $oTrans->DROPLET_MESSAGE_GENERIC_MISSING_ARCHIVE_FILE );
    } else {
        $aFtan = $admin->getFTAN('');
        // prepare default data for phplib and twig
        $aTplData = array (
            'FTAN_NAME' => $aFtan['name'],
            'FTAN_VALUE' => $aFtan['value'],
            'sArchiveFile' => $sArchiveFile,
            'sArchiveFilename' => basename($sArchiveFile),
            );
    // Create new template object with phplib
        $oTpl = new Template($sAddonThemePath, 'keep' );
        $oTpl->set_file('page', 'import_droplets.htt');
        $oTpl->set_block('page', 'main_block', 'main');
        $oTpl->set_var($aLang);
        $oTpl->set_var($aTplDefaults);
        $oTpl->set_var($aTplData);
        $oTpl->set_block('main_block', 'list_archiv_block', 'list_archiv');
        $oTpl->set_block('main_block', 'show_archiv_folder_block', 'show_archiv_folder');
        foreach ($aFilesInArchiv as $key=>$value) {
            $aData = array (
                'index' => $value['index'],
                'filename' => basename($value['filename'],'.php'),
                'comment' => $value['comment'],
                'size' => $value['size'],
                'created_when' => date('d.m.Y'.' '.'H:i', $value['mtime']+TIMEZONE),
                );
            $oTpl->set_var($aData);
            if ( $value['folder'] ) {
                $oTpl->parse('show_archiv_folder', 'show_archiv_folder_block', true);
            } else {
                $oTpl->set_block('show_archiv_folder_block', '');
            }
            $oTpl->parse('list_archiv', 'list_archiv_block', true);
        }
/*-- finalize the page -----------------------------------------------------------------*/
        $oTpl->parse('main', 'main_block', false);
        $oTpl->pparse('output', 'page');
    }
}
