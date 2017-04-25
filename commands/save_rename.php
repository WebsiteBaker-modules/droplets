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
 * @version         $Id: save_rename.php 65 2017-03-03 21:38:16Z manu $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2.10/branches/wb/modules/droplets/commands/save_rename.php $
 * @lastmodified    $Date: 2017-03-03 22:38:16 +0100 (Fr, 03. Mrz 2017) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) {
    require_once(dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
    throw new IllegalFileException();
} else {
    if (($droplet_id === false)) {
     //   $oApp->print_header();
        $oApp->print_error('IDKEY_DROPLET::'. $MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl );
    }
    $modified_when = time();
    $modified_by   = (int) $oApp->get_user_id();
    $sNewName      = trim($aRequestVars['title']);
    if (!$sNewName) {
        msgQueue::add($Droplet_Message['GENERIC_MISSING_TITLE']);
    } else {
        $sOldName      = trim(@$aRequestVars['existingTitle']?:$sNewName);
        $sGenericNewName = getUniqueName($oDb, $sNewName);
        switch ($subCommand):
            case 'add_droplet':
                $sql = 'INSERT INTO `'.TABLE_PREFIX.'mod_droplets` SET '
                  .    '`name`=\''.$oDb->escapeString($sGenericNewName).'\','
                  .    '`code`=\'\', '
                  .    '`description`=\'\', '
                 .     '`modified_when`='.$modified_when.','
                 .     '`modified_by`='.$modified_by.','
                  .    '`active`=0,'
                  .    '`admin_edit`=0,'
                  .    '`admin_view`=0,'
                  .    '`show_wysiwyg`=0,'
                  .    '`comments`=\'\'';
                break;
            case 'copy_droplet':
                $sql = 'INSERT INTO `'.TABLE_PREFIX.'mod_droplets` '
                  .        'SELECT '
                  .        'NULL,'
                  .        '\''.$oDb->escapeString($sGenericNewName).'\','
                  .        '`code`,`description`,'.$modified_when.','.$modified_by.','
                  .        '`active`,`admin_edit`,`admin_view`,`show_wysiwyg`,`comments`'
                 . 'FROM `'.TABLE_PREFIX.'mod_droplets` '
                     .     'WHERE `name`=\''.$oDb->escapeString($sOldName).'\'';
                break;
            case 'rename_droplet':
                if ($sNewName == $sOldName) {$sGenericNewName = $sOldName;}
                $sql = 'UPDATE `'.TABLE_PREFIX.'mod_droplets` '
                     . 'SET `name`=\''.$sGenericNewName.'\', '
                  .        '`modified_when`='.$modified_when.','
                  .        '`modified_by`='.$modified_by.' '
                     . 'WHERE `name`=\''.$sOldName.'\'';
                break;
            default: /* do nothing */  break;
        endswitch;
        if (!$oDb->query($sql))
        {
            if($oDb->is_error()) {
                msgQueue::add($oDb->get_error());
            }
        } else {
            msgQueue::add($TEXT['SUCCESS'], true );
        }
    }
}
