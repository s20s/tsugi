<?php
// In the top frame, we use cookies for session.
if ( ! defined('COOKIE_SESSION') ) define('COOKIE_SESSION', true);
require_once("../../config.php");
require_once("../../admin/admin_util.php");
require_once("expire_util.php");

use \Tsugi\UI\Table;
use \Tsugi\Util\U;
use \Tsugi\Core\LTIX;

\Tsugi\Core\LTIX::getConnection();

session_start();

require_once("../gate.php");
if ( $REDIRECTED === true || ! isset($_SESSION["admin"]) ) return;

if ( ! ( isset($_SESSION['id']) || isAdmin() ) ) {
    $_SESSION['login_return'] = LTIX::curPageUrlFolder();
    header('Location: '.$CFG->wwwroot.'/login');
    return;
}

$tenant_count = get_count_table('lti_key');
$context_count = get_count_table('lti_context');
$user_count = get_count_table('lti_user');

$tenant_days = U::get($_GET,'tenant_days',1000);
$context_days = U::get($_GET,'context_days',500);
$user_days = U::get($_GET,'user_days',500);

$user_expire =  get_expirable_records('lti_user', $user_days);
$context_expire =  get_expirable_records('lti_context', $context_days);
$tennant_expire =  get_expirable_records('lti_key', $tenant_days);

$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();
$OUTPUT->flashMessages();
?>
<h1>Manage Data Expiry</h1>
<form>
<ul>
<li>User count: <?= $user_count ?>  <br/>
Users with no activity in 
<input type="text" name="user_days" size=5 value="<?= $user_days ?>"> days:
<?= $user_expire ?> 
</li>
<li>Context count: <?= $context_count ?>  <br/>
Contexts with no activity in 
<input type="text" name="context_days" size=5 value="<?= $context_days ?>"> days:
<?= $context_expire ?> 
</li>
<li>Tenant count: <?= $tenant_count ?>  <br/>
Tenants with no activity in 
<input type="text" name="tenant_days" size=5 value="<?= $tenant_days ?>"> days:
<?= $tenant_expire ?> 
</li>
</ul>
<input type="submit" value="Update">
</form>
<?php
$OUTPUT->footer();
