<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2012 Asad Haider <asad@asadhaider.co.uk>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package     FileEasy-Upload-Script
 * @author      Asad Haider <asad@asadhaider.co.uk>
 * @copyright   2012 Asad Haider.
 * @link        http://asadhaider.co.uk
 * @version     1.0.0
 */

session_start();
include("../utils/config.php");
include("../utils/functions.php");
include("../utils/admin_functions.php");

if(!isset($_SESSION['adminsession'])) {
  header("location: ./login.php");
}

$access_id = $_GET['access_id'];

if(!isset($access_id)) {
  $error = "You have not specified a valid file ID.";
}else if(!mysql_num_rows(mysql_query("SELECT * FROM ".$table_prefix."files WHERE (`expiry` = '0' OR `expiry` >  '" . time() . "') AND (`deleted` = '0') AND (`access_id` = '".mysql_real_escape_string($access_id)."')"))) {
  $error = "The specified file does not exist.";
}else{
  if(isset($_POST['deletefile'])) {
    // Delete file from server
    if( unlink("../uploads/" . $access_id) ) {
      // Update file record, set deleted to true
      mysql_query("UPDATE `" . $table_prefix . "files` SET `deleted` = '1' WHERE `access_id` = '" . $access_id . "';");
      
      // Redirect user
      header("Location: ./manage_files.php?action=deleted");
    }else{
      $error = "There was an error deleting the file.";
    }
  }
}

$pagename = "Delete File";
include("./admin_header.php");
?>
  <div id="content">
    <ul id="topTabs">
      <li><a href="./index.php">Dashboard</a></li>
      <li><a href="./site_settings.php">Site Settings</a></li>
      <li><a href="./admin_settings.php">Admin Settings</a></li>
      <li class="selected"><a href="./manage_files.php">Manage Files</a></li>
    </ul>
    <div class="clear"></div>
<?php
if(isset($error)) {
  print("    <h2>File Not Found</h2>\n");
  print("    <p>".$error." <a href=\"./manage_files.php\">Click here</a> to go back.</p>\n");
}else{
?>
    <h2>Delete File #<?php echo $access_id; ?></h2>
    <p>You are deleting file #<?php echo $access_id; ?>. To confirm this, click the delete button below, or otherwise, <a href="./manage_files.php">click here</a> to go back.</p>
    <form method="post" action="./delete_file.php?access_id=<?php echo $access_id; ?>" id="deletefile">
      <input type="hidden" name="deletefile" value="1" />
      <input type="submit" name="submit" value="Yes, Delete the File" class="submit" />
    </form>
<?php
}
?>
  </div>
<?php
include("./admin_footer.php");
?>
