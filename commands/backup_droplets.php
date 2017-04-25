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
 * @version         $Id: backup_droplets.php 65 2017-03-03 21:38:16Z manu $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2.10/branches/wb/modules/droplets/commands/backup_droplets.php $
 * @lastmodified    $Date: 2017-03-03 22:38:16 +0100 (Fr, 03. Mrz 2017) $
 *

print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $aFilesInDir ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */

$sOverviewDroplets = $TEXT['LIST_OPTIONS'];

// suppress to print the header, so no new FTAN will be set
//$oApp = new admin('Addons', 'templates_uninstall', false);
if( !$oApp->checkFTAN() ){
    $oApp->print_error($oTrans->MESSAGE_GENERIC_SECURITY_ACCESS, $ToolUrl );
    exit();
}
// After check print the header

if (!function_exists( 'make_dir' ) ) { require($oReg->AppPath.'/framework/functions.php');  }
if (!function_exists('insertDropletFile')) { require($sAddonPath.'/droplets.functions.php'); }
//$oApp->print_header();
// create backup filename with pre index
$sBackupDir = $sAddonRel.'/data/archiv/';
make_dir( $oReg->AppPath.$sBackupDir );

$sDropletTmpDir = $sAddonRel.'/data/tmp/';
$sDropletTmpDir = 'temp/modules/'.$sAddonName.'/tmp/';
rm_full_dir($oReg->AppPath.$sDropletTmpDir, true);
make_dir( $oReg->AppPath.$sDropletTmpDir );

$sTimeStamp = '_'.strftime('%Y%m%d_%H%M%S', time() + $oReg->Timezone ).'.zip';

$FilesInDB = '*';
$aFullList = glob($sAddonPath.'/data/archiv/*.zip', GLOB_NOSORT);

if( isset( $aRequestVars['cb'] ) && sizeof( $aRequestVars['cb']) == 1  ) {
    $FilesInDB  = '';
    foreach( $aRequestVars['cb'] as $FileName ) {
        $sSearchFor = $FileName;
        $FilesInDB .= '\''.$FileName.'\'';
    }
    $sBackupName = 'Droplet_'.$FileName.$sTimeStamp;
} elseif( isset( $aRequestVars['cb'] ) && sizeof( $aRequestVars['cb'] > 1 ) ) {
    $FilesInDB  = '';
    foreach( $aRequestVars['cb'] as $FileName ) {
        $sSearchFor = $FileName;
        $FilesInDB .= '\''.$FileName.'\',';
    }
    $sBackupName = 'DropletsBackup'.$sTimeStamp;
} else {
    $sSearchFor  = 'DropletsFullBackup';
    $sBackupName = 'DropletsFullBackup'.$sTimeStamp;
}

$aFilesInDir = array();
foreach ($aFullList as $index =>$sItem) {
    if (preg_match('/[0-9]+_('.$sSearchFor.'_[^\.]*?)\.zip/si', $sItem, $aMatch)) {
        $aFilesInDir[$index+1] = $aMatch[1];
    }
}

unset($aFullList);

$sZipFile = $sBackupDir.$sBackupName;
if( !class_exists('PclZip',false) ) { require( $oReg->AppPath.'/include/pclzip/pclzip.lib.php'); }
$aFilesToZip = backupDropletFromDatabase( $oReg->AppPath.$sDropletTmpDir, $FilesInDB, $oDb );

$oArchive = new PclZip( $oReg->AppPath.$sZipFile );
$archiveList = $oArchive->create(
                   $aFilesToZip
                  ,PCLZIP_OPT_REMOVE_ALL_PATH
              );
if ($archiveList == 0){
    echo 'Packaging error: '.$oArchive->errorInfo(true);
    msgQueue::add("Error : ".$oArchive->errorInfo(true));
} elseif(is_readable($oReg->AppPath.$sBackupDir)) {
?>

<header class="droplets"><h4 >Create archive: <?php echo basename($sZipFile); ?></h4></header>

<section class="droplets drop-outer">
<ol>
<?php
    foreach($archiveList AS $key=>$aDroplet ) {
?>
    <li>Backup <strong> <?php echo $aDroplet['stored_filename']; ?></strong></li>
<?php } ?>

</ol>
<div class="drop-backup">
<h2>Backup created - <a class="btn" href="<?php echo $oReg->AppUrl.$sBackupDir.$sBackupName; ?>"><?php echo $oTrans->DROPLET_MESSAGE_GENERIC_LOCAL_DOWNLOAD; ?></a>
                  <button style="padding: 0.2825em 0.8525em; " name="cancel" class="btn" type="button" onclick="window.location='<?php echo $ToolUrl; ?>';"><?php echo $oTrans->TEXT_CANCEL; ?></button>

</h2>
</div>
</section>
<?php  } else {
    msgQueue::add('Backup not created - '.$oTrans->TEXT_BACK.'');
}

