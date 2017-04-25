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
 * @version         $Id: uninstall.php 65 2017-03-03 21:38:16Z manu $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2.10/branches/wb/modules/droplets/uninstall.php $
 * @lastmodified    $Date: 2017-03-03 22:38:16 +0100 (Fr, 03. Mrz 2017) $
 *
 */
if(defined('WB_PATH'))
{
    // delete tables from sql dump file
    if (is_readable(__DIR__.'/install-struct.sql')) {
        $database->SqlImport(__DIR__.'/install-struct.sql', TABLE_PREFIX, __FILE__ );
    }
}
