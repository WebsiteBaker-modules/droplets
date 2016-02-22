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
 * @version         $Id: save_droplet.php 1503 2011-08-18 02:18:59Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/save_droplet.php $
 * @lastmodified    $Date: 2011-08-18 04:18:59 +0200 (Do, 18. Aug 2011) $
 *
 */

require( dirname(dirname(dirname((__DIR__)))).'/config.php' );
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
// Include WB admin wrapper script
$admintool_link = ADMIN_URL .'/admintools/index.php';
$ToolUrl = ADMIN_URL .'/admintools/tool.php?tool=droplets';

$admin = new admin('admintools', 'admintools',false);

$droplet_id = intval($admin->checkIDKEY('droplet_id', false, 'post' ));

if( !$admin->checkFTAN() || !$droplet_id ) {
    $admin->print_header();
    $admin->print_error( $droplet_id.' ) '. $MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl );
}
$admin->print_header();

// Validate all fields
if($admin->get_post('title') == '') {
    $admin->print_error($MESSAGE['GENERIC_FILL_IN_ALL'].' ( Droplet Name )', $ToolUrl );
} else {
    $title = $admin->add_slashes($admin->get_post('title'));
    $active = (int) $admin->get_post('active');
    $admin_view = (int) $admin->get_post('admin_view');
    $admin_edit = (int) $admin->get_post('admin_edit');
    $show_wysiwyg = (int) $admin->get_post('show_wysiwyg');
    $description = $admin->add_slashes($admin->get_post('description'));
    $tags = array('<?php', '?>' , '<?');
    $content = $admin->add_slashes(str_replace($tags, '', $_POST['savecontent']));
    $comments = trim($admin->add_slashes($admin->get_post('comments')));
    $modified_when = time();
    $modified_by = (int) $admin->get_user_id();
}

// Update row
$sql = 'UPDATE `'.TABLE_PREFIX.'mod_droplets` SET '
    . '`name` = \''.$title.'\', '
    . '`active` = '.$active.', '
    . '`admin_view` = '.$admin_view.', '
    . '`admin_edit` = '.$admin_edit.', '
    . '`show_wysiwyg` = '.$show_wysiwyg.', '
    . '`description` = \''.$description.'\', '
    . '`code` = \''.$content.'\', '
    . '`comments` = \''.$comments.'\', '
    . '`modified_when` = '.$modified_when.', '
    . '`modified_by` = '.$modified_by.' '
    . 'WHERE `id` = '.$droplet_id;
$database->query($sql);

// Check if there is a db error, otherwise say successful
if($database->is_error()) {
    $admin->print_error($database->get_error(), $ToolUrl );
} else {
    $admin->print_success( $TEXT['SUCCESS'], $ToolUrl );
}

// Print admin footer
$admin->print_footer();
