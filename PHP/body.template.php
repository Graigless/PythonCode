<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
define('REPORTING_MONTHS_SELECT', 36);
define('REPORTING_MONTHS_SELECT_DEFAULT_YEAR', 12);
define('REPORTING_MONTHS_SELECT_DEFAULT_QUARTER', 3);
define('REPORTING_DAYS_SELECT', 60);

/**
 * build_logins_body()
 *
 * @param mixed $pager
 * @param mixed $logins
 * @param mixed $err
 * @return
 */
function build_logins_body(&$pager, $logins, $err, $roles, $alllogins) {
  $link = new Link();
  $util = new Utility();
  $currentKeyName = 'memberid';

  ?>
  <td>
  </form>
  <form name="manageSchedule" method="post" action="license_mgr_update.php" onsubmit="return checkLoginFormAndCR();">
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
  <td class="tableBorder">
  <table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
  <td colspan="6" class="tableTitle">&#8250; <?php echo "User List" ?></td>
  </tr>
  <?php
  natcasesort($alllogins['fname']);
  $arr_fname = array_unique($alllogins['fname']);
  $fname_select = "<br><select id='fname_filter' onchange=\"filterUsers('fname_filter','fname')\"><option value=''></option>";
  foreach ($arr_fname as $option) {
    if (isset($_GET['fname']) && $_GET['fname'] == $option) {
      $fname_select .= "<option value='$option' selected='selected'>$option</option>";
    } else {
      $fname_select .= "<option value='$option'>$option</option>";
    }
  }
  $fname_select .= "</select>";

  natcasesort($alllogins['lname']);
  $arr_lname = array_unique($alllogins['lname']);
  $lname_select = "<br><select id='lname_filter' onchange=\"filterUsers('lname_filter','lname')\"><option value=''></option>";
  foreach ($arr_lname as $option) {
    if (isset($_GET['lname']) && $_GET['lname'] == $option) {
      $lname_select .= "<option value='$option' selected='selected'>$option</option>";
    } else {
      $lname_select .= "<option value='$option'>$option</option>";
    }
  }
  $lname_select .= "</select>";

  natcasesort($alllogins['email']);
  $arr_email = array_unique($alllogins['email']);
  $email_select = "<br><select id='email_filter' onchange=\"filterUsers('email_filter','email')\"><option value=''></option>";
  foreach ($arr_email as $option) {
    if (isset($_GET['email']) && $_GET['email'] == $option) {
      $email_select .= "<option value='$option' selected='selected'>$option</option>";
    } else {
      $email_select .= "<option value='$option'>$option</option>";
    }
  }
  $email_select .= "</select>";
  echo "
        <tr>
          <td class='tableTitle'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'email'), 'eMail') . $email_select . "</td>
          <td width=\"20%\" class='tableTitle'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'fname'), 'First Name') . $fname_select . "</td>
          <td width=\"20%\" class='tableTitle'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'lname'), 'Last Name') . $lname_select . "</td>
          <td width=\"15%\" class='tableTitle'>Role</td>
          <td width=\"5%\" class='tableTitle'>Edit</td>" .
  (!empty($_SESSION['canDeleteUsers']) ? "<td width=\"5%\" class='tableTitle'>Delete</td> " : "") . "
        </tr>
    ";

  if (!$logins)
    echo '<tr class="cellColor0"><td colspan="9" style="text-align: center;">' . $err . '</td></tr>' . "\n";

  for ($i = 0; is_array($logins) && $i < count($logins); $i++) {
    $cur = $logins[$i];
    $currentKeyValue = $cur[$currentKeyName];
    echo "<tr class=\"cellColor" . ($i % 2) . "\" align=\"left\" id=\"tr$i\">\n";
    echo '<td style="text-align:left">' . $cur['email'] . "</td>\n";
    echo '<td style="text-align:left">' . utf8_decode($cur['fname']) . "</td>\n";
    echo '<td style="text-align:left">' . utf8_decode($cur['lname']) . "</td>\n";
    echo '<td id="' . $cur['memberid'] . '">';
    foreach ($roles as $role) {
      if (!empty($cur['roles'][$role['roles_uid']])) {
        echo $role['roles_name'];
      } else {
        echo "";
      }
    }
    echo '</td>';

    echo '<td style="text-align:center">' . $link->getLink($_SERVER['PHP_SELF'] . '?' . preg_replace("/&" . $currentKeyName . "=[\d\w]*/", "", $_SERVER['QUERY_STRING']) . '&amp;' . $currentKeyName . '=' . $currentKeyValue . ((strpos($_SERVER['QUERY_STRING'],
        $pager->getLimitVar()) === false) ? '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() : ''), 'Edit', '', '', 'Edit data for' . $currentKeyValue) . "</td>\n"
    . (!empty($_SESSION['canDeleteUsers']) ? '<td style="text-align:center">' . "<input type=\"checkbox\" name=\"" . $currentKeyName . "[]\" value=\"" . $currentKeyValue . "\" onclick=\"adminRowClick(this,'tr$i',$i);\"/></td>\n" : "")
    . "</tr>\n";
  }

  // Close table

  ?>
  </table>
  </td>
  </tr>
  </table>
  <br />
  <?php
  if (!empty($_SESSION['canDeleteUsers'])) {
    echo submit_button('Delete', $currentKeyName) . hidden_fn('delLogin');
  }

  ?>
  </form><br>
  <form name="invBuildReporting" method="post" action="report_mgr.php" onsubmit="return checkUserBuildReporting();">
    <?php
    echo submit_button('Get User Report') . hidden_fn('reportOnUserRightsInformartion');

    ?>
  </form>
  </td>
  <?php
}

/**
 * build_logins_edit()
 *
 * @param mixed $rs
 * @param mixed $edit
 * @param mixed $pager
 * @return
 */
function build_logins_edit($rs, $edit, &$pager, $roles, $logins, $activity) {
  $currentKeyName = 'memberid';
  if ($edit) {
    foreach ($logins as $login)
      if ($login['memberid'] == $_GET['memberid']) {
        $cur = $login;
      }
  }

  ?>
  <form name="addLogin" method="post" action="license_mgr_update.php" onsubmit="return checkAddLogin();" >
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
  <td class="tableBorder">
  <table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
  <td width="200" class="formNames">* eMail</td>
  <?php
  if (!$edit) {

    ?>
    <td class="cellColor"><input type="text" name="email" id="email" class="textbox" onblur="email2userid()" onkeypress="email2userid()" value="<?php echo isset($rs['email']) ? $rs['email'] : '' ?>" /></td>
    <?php
  } else {

    ?>
    <td class="cellColor"><input type="text" name="email" class="textbox" disabled="disabled" value="<?php echo isset($rs['email']) ? $rs['email'] : '' ?>" /></td>
    <input type="hidden" name="email_value" value="<?php echo isset($rs['email']) ? $rs['email'] : '' ?>">
    <?php
  }

  ?>
  </tr>
  <tr>
  <td width="200" class="formNames">* First Name</td>
  <td class="cellColor"><input type="text" name="fname" class="textbox" value="<?php echo isset($rs['fname']) ? utf8_decode($rs['fname']) : '' ?>" /></td>
  </tr>
  <tr>
  <td width="200" class="formNames">* Last Name</td>
  <td class="cellColor"><input type="text" name="lname" class="textbox" value="<?php echo isset($rs['lname']) ? utf8_decode($rs['lname']) : '' ?>" /></td>
  </tr>
  <tr>
    <td width="200" class="formNames">* Role</td>
  <td class="cellColor">
    <?php
    echo '<select name="role" ><option value=""></option>';
    foreach ($roles as $role) {
      if (!empty($cur['roles'][$role['roles_uid']])) {
        echo "<option value=" . $role['roles_uid'] . " selected='selected'>" . $role['roles_name'] . "</option>";
      } else {
        echo "<option value=" . $role['roles_uid'] . ">" . $role['roles_name'] . "</option>";
      }
    }
    echo '</select></td></tr>';
    echo '<input type="hidden" name="password" class="textbox" value="dontcarefornow" />';
    echo '<input type="hidden" name="password2" class="textbox" value="dontcarefornow" />';

    ?>

  </table>
  </td>
  </tr>
  </table>
  <br />
  <?php
// Print out correct buttons
  if (!$edit) {
    echo submit_button('Add Login', 'email') . hidden_fn('addLogin')
    . ' <input type="reset" name="reset" value="' . 'Clear' . '" class="button" />' . "\n";
  } else {
    echo submit_button('Edit Login', 'email') . cancel_button($pager) . hidden_fn('editLogin')
    . '<input type="hidden" name="' . $currentKeyName . '" value="' . $rs[$currentKeyName] . '" />' . "\n";
    // Unset variables
  }
  echo "</form>\n";

  ?>
  <br>
  <table class='sort'>
  <thead>
  <tr><th colspan='3'>Last 15 minutes activity</th></tr>
  <tr class="rowHeaders"><td>User</td><td>Activity</td><td>Time</td></tr>
  </thead>
  <?php
  foreach ($activity as $line) {
    echo "<tr><td><a href='mailto:" . $line['email'] . "' >" . $line['name'] . "</a></td><td>" . $line['operation'] . "</td><td>" . $line['time'] . "</td></tr>";
  }

  echo "</table>";
//var_dump($activity);
  unset($rs);
}

function cmpFileByDate($a, $b) {
  return $a['timestamp'] < $b['timestamp'];
}

/**
 * build_admin_tools_body()
 *
 * @return
 */
function build_admin_tools_body($toolType, $data, $tests) {
  $conf = config::getConfig();
  $link = new Link();
  $util = new Utility();
  $nbOptions = 0;

  $popup_msg = '';
  if (isset($data) && isset($data['success'])) {
    if ($data['success'] == true)
      $popup_msg = 'Operation success';
    else
      $popup_msg = 'Operation failure';
  }
  if (strlen($popup_msg) > 0)
    echo "<script>alert(\"" . $popup_msg . "\");</script>";

  ?>
  <td>
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
  <table>
  <tr>
  <td>Tools:</td>
  <td>
  <select id="newUrl" onchange="changeUrl('newUrl');">
    <?php
    //...............................................................................
    echo "<option value=\"" . ($toolType == '' ? 'selected' : '') . "\"></option>";

    if (!empty($_SESSION['canDownloadArchivedLicenses'])) {
      if (!empty($_SESSION['canDownloadArchivedLicenses'])) {
        echo "<option value=\"index.php?operation=adminTools&toolType=browseArchivedLicenses\"" . ($toolType == 'browseArchivedLicenses' ? 'selected' : '') . ">Browse Archived Licenses</option>";
        $nbOptions++;
      }
      echo "</optgroup>";
    }

    ?>
    <?php
    //...............................................................................
    echo "<optgroup label=\"Validation\">";
    echo "<option value=\"index.php?operation=adminTools&toolType=compareLicenseFiles\"" . ($toolType == 'compareLicenseFiles' ? 'selected' : '') . ">Compare License Files</option>";
    $nbOptions++;
    if (!empty($_SESSION['canManageTests']) || !empty($_SESSION['canViewCurrentLicenses'])) {
      if (!empty($_SESSION['canManageTests'])) {
        echo "<option value=\"index.php?operation=adminTools&toolType=processRegressionTests\"" . ($toolType == 'processRegressionTests' ? 'selected' : '') . ">Manage/Run Regression Tests</option>";
        $nbOptions++;
      }
    }
    echo "</optgroup>";

    ?>
    <?php
    //...............................................................................
    if (!empty($_SESSION['canDeleteLicenseReported']) || !empty($_SESSION['canManageReleases'])) {
      echo "<optgroup label=\"Provisioning\">";
      if (!empty($_SESSION['canManageReleases'])) {
      echo "<option value=\"index.php?operation=adminTools&toolType=manageReleases\"" . ($toolType == 'manageReleases' ? 'selected' : '') . ">Add a Release</option>";
        $nbOptions++;
      }
      echo "</optgroup>";
    }

    ?>
    <?php
    //...............................................................................
    if (!empty($_SESSION['canManageUsers'])) {
      echo "<optgroup label=\"Misc\">";
      if (!empty($_SESSION['canManageUsers'])) {
        echo "<option value=\"index.php?operation=adminTools&toolType=emailUsers\"" . ($toolType == 'emailUsers' ? 'selected' : '') . ">Email Users</option>";
        $nbOptions++;
      }
      if (!empty($_SESSION['canManageSOXControls'])) {
      echo "<option value=\"index.php?operation=adminTools&toolType=soxControls\"" . ($toolType == 'soxControls' ? 'selected' : '') . ">SOX Controls</option>";
      $nbOptions++;
    }
    echo "</optgroup>";
  }

    ?>
    <?php
    //...............................................................................
    if ($nbOptions == 0) {
      echo "<option><i>none</i></option>";
    }

    ?>
  </select>
  </td>
  </tr>
  </table>
  <table width=70%>
    <?php
    // .........................................................................................................
    if ($toolType == 'browseArchivedLicenses' && !empty($_SESSION['canDownloadArchivedLicenses'])) {
      // Explore the files via a web interface.
      $browseUrl = 'index.php?operation=adminTools&toolType=browseArchivedLicenses';
      $streamUrl = 'index.php?operation=streamFile';
      $confBaseDir = $conf->getLicArchivePath();
    $path = $confBaseDir . (!empty($_REQUEST['path']) ? '/' . $_REQUEST['path'] : ''); // the path the script should access
      // abort if not under the right dir
      if (!is_dir($path)) {
        echo "<p class=\"error\">Directory is not accessible.</p>";
      } else {
        echo "<br><h1>Archived License Files Browser</h1>";
        echo "<p>Browsing Location: " . "<a href=\"{$browseUrl}\">$confBaseDir</a>";

        $tmpDir = $confBaseDir;
        $tmpItemDir = preg_replace('/\\' . preg_quote(DIRECTORY_SEPARATOR) . "/", "_", $tmpDir);
        if (isset($_REQUEST['path']) > 0) {
          foreach (explode(DIRECTORY_SEPARATOR, $_REQUEST['path']) as $part) {
            if (!strlen($part))
              continue;
            $tmpDir .= DIRECTORY_SEPARATOR . $part;
            $tmpItemDir = preg_replace('/\\' . preg_quote(DIRECTORY_SEPARATOR) . "/", "_", $tmpDir);
            $dirTmp = substr($tmpDir, strlen($confBaseDir) + 1);
            echo DIRECTORY_SEPARATOR . "<a href=\"{$browseUrl}&path={$dirTmp}\">{$part}</a>";
          }
        }
        echo "</p>";

        echo '<p>Search Archive <input type="text" id="searchFilter"/> <input type="button" class="button" name="submitSearch" value="submit" onClick="filterSearch();"/></p>';

        if (!isset($_GET['searchFilter'])) {
          $directories = array();
          $files = array();

          // Check we are focused on a dir
          $tmpBrowseDir = preg_replace('/\\' . preg_quote(DIRECTORY_SEPARATOR) . "/", "_", $path);
          if (!preg_match('/' . $tmpItemDir . '/', $tmpBrowseDir))
            echo "<p class=\"error\">Directory is not accessible.</p>";
          elseif (is_dir($path)) {
            chdir($path); // Focus on the dir
            if ($handle = opendir('.')) {
              while (($item = readdir($handle)) !== false) {
                // Loop through current directory and divide files and directories
                if (is_dir($item)) {
                  $tmpItemDir = preg_replace('/\\' . preg_quote(DIRECTORY_SEPARATOR) . "/", "_", $path . '/' . $item);
                  if (!preg_match('/[.]/', $item))
                    array_push($directories, $path . '/' . $item);
                } else
                  array_push($files, $path . '/' . $item);
              }
              closedir($handle); // Close the directory handle
            } else
              echo "<p class=\"error\">Directory handle could not be obtained.</p>";
          } else
            echo "<p class=\"error\">Path is not a directory</p>";

          // There are now two arrays that contains the contents of the path.
          // List the directories as browsable navigation
          asort($directories);
          echo "<h2>Navigation</h2>";
          echo "<ul>";
          foreach ($directories as $directory) {
            $dirTmp = substr($directory, strlen($confBaseDir) + 1);
            if (strlen($directory) > strlen($path))
              echo "<li><a href=\"{$browseUrl}&path={$dirTmp}\">{$dirTmp}</a></li>";
          }
          echo "</ul>";

          echo "<h2>Files</h2>";
          echo "<ul>";
          asort($files);
          if (count($files) == 0)
            echo "<i>none</i>";
          else
            foreach ($files as $file) {
              // Comment the next line out if you wish see hidden files while browsing
              if (preg_match("/^\./", $file) || $file == $browseUrl): continue;
              endif; // This line will hide all invisible files.
              echo "<li><a href=\"{$streamUrl}&file={$file}\" target=\"blank\">" . basename($file) . "</a></li>";
            }
          echo "</ul>";
        } else {
          $dirFileList = $util->dirToArrayWithFileFilter($confBaseDir, $_GET['searchFilter']);
          echo "<h2>Search by Filter Result (" . $_GET['searchFilter'] . ")</h2>";
          echo "<ul>";
          rsort($dirFileList, SORT_NUMERIC);
          foreach ($dirFileList as $year => $yearData) {
            $yearDataSorted = $yearData['value'];
            rsort($yearDataSorted);
            foreach ($yearDataSorted as $month => $files) {
              usort($files['value'], "cmpFileByDate");
              for ($i = 0; $i < count($files['value']); $i++) {
                $file = $yearData['dirName'] . '/' . $files['dirName'] . '/' . basename($files['value'][$i]['fileName']);
                $fileFullPath = $confBaseDir . '/' . $file;
                if (!preg_match('/md5$/', $file)) {
                  echo "<li><a href=\"{$streamUrl}&file={$fileFullPath}\" target=\"blank\">" . $file . "</a>&nbsp;&nbsp;&nbsp;" . date("F d Y H:i:s",
                    $files['value'][$i]['timestamp']) . "</li>";
                }
              }
            }
          }
          echo "</ul>";
        }
      }
    }
    // .........................................................................................................
    elseif ($toolType == 'emailUsers') {
      build_email_users_form($data);
    } elseif ($toolType == 'soxControls') {
    build_sox_controls_form($data);
  }
  // .........................................................................................................
    elseif ($toolType == 'manageReleases') {

      ?>
    <form name="manageReleases" method="post" action="generateLicense.php" onsubmit="return checkNewRelease();">
    <tr>
    <td><h1>Release Management Tool</h1></td>
    </tr>
    <tr>
    <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
    <tr>
    <td width="15%">New Release Name: </td>
    <td align="left"><input type="text" name="new_release_name" id="new_release_name" size="10"></td>
    </tr>
    <tr>
    <td>Source Release: </td>
    <td align="left">
    <select name="source_rel" >
      <?php
      for ($i = 0; $i < count($data); $i++) {
        echo '<option value="' . $data[$i] . '">' . $data[$i] . '</option>';
      }

      ?>
    </select>
    </td>
    </tr>
    <tr>
    <td colspan="2">Note: All Product Profile Settings for Pack Names, Relative Quantities, etc from the <b>Source Release</b> will be cloned when creating the new release.</td>
    </tr>
    <tr>
    <td><?php
      echo submit_button('Add New Release', 'addNewRelease');

      ?></td>
    </tr>
    </table>
    </form>
    <?php
  }
  // .........................................................................................................
  elseif ($toolType == 'processRegressionTests') {

    ?>

    <form name="processRegressionTests" method="post" action="generateLicense.php" onSubmit="return isLocked(event)">
    <tr>
    <td><h1>Regression Test Tool</h1></td>
    </tr>
    <tr>
    <td><input type="submit" name="submit" value="Run All Tests" class="button"/>
    <input type="hidden" name="get" value="runRegressionTests" />
    <input type="button" class="button" name="show_past_tests" value="Show Past Tests" onClick="ShowTestResults()"/>
        </td>
    </tr>
    <tr>
        <td><?php
        // TODO: The estimation could be calculated with latest runs
        echo "<i>(Estimated execution time is " . number_format((count($data['clusters']) + count($data['groups'])) * 0.05, 0) . " seconds for all the test cases)</i>"

    ?></td>
    </tr>
    <tr>
    <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
    <tr>
    <td class="tableBorder">
    <table width="100%" border="0" cellspacing="1" cellpadding="0">
    <tr>
    <td colspan="4" class="tableTitle">&#8250; Test Snapshots (regression test cases)</td>
    </tr>
    <tr>
    <td colspan="4" class="tableTitle">&#8250; Clusters</td>
    </tr>

    <tr class="rowHeaders">
    <td style="text-align:center" width="30%">Customer Name</td>
    <td style="text-align:center" width="30%">Cluster Name</td>
    <td style="text-align:center" width="10%">Release</td>
    <td style="text-align:center" width="30%">Snapshot timestamp</td>
    </tr>
    <?php
    $i = 0;

    if (isset($data['clusters']) && is_array($data['clusters'])) {
      $testFiles = glob($conf->getTestLicenseSnapshotsPath("/license.*"));
      foreach ($data['clusters'] as $cluster) {
        echo "<tr class=\"cellColor" . ($i++ % 2) . "\">";
        echo "<td style=\"text-align:center\">" . $cluster['customer_name'] . "</td>";
        echo "<td style=\"text-align:center\">" . $link->getLink($_SERVER['PHP_SELF'] . '?operation=licinventory&customer_filter=' . $cluster['customer_id'] . '&inv_action=cluster_licenses&as_cluster_uid=' . $cluster['as_cluster_uid'],
          $cluster['as_cluster_name']) . "</td>";
        echo "<td style=\"text-align:center\">" . $cluster['software_release'] . "</td>";
        $testFilename = $conf->getTestLicenseSnapshotsPath("/" . $cluster['test_snapshot_filename']);
        if (!in_array($testFilename, $testFiles)) {
          echo "<td style=\"text-align:center;color:red;\">" . preg_replace('/.*[.]/', '', preg_replace('/[.]txt/', '', $cluster['test_snapshot_filename'])) . " - file missing</td>";
        } else {
          echo "<td style=\"text-align:center\">" . preg_replace('/.*[.]/', '', preg_replace('/[.]txt/', '', $cluster['test_snapshot_filename'])) . "</td>";
        }
        echo "</tr>";
      }
    }

    ?>
    <tr>
    <td colspan="4" class="tableTitle">&#8250; Groups</td>
    </tr>

    <tr class="rowHeaders">
    <td style="text-align:center" width="30%">Customer Name</td>
    <td style="text-align:center" width="30%">Group Name</td>
    <td style="text-align:center" width="10%">Release</td>
    <td style="text-align:center" width="30%">Snapshot timestamp</td>
    </tr>
    <?php
    $i = 0;

    if (isset($data['groups']) && is_array($data['groups'])) {
      $testFiles = glob($conf->getTestLicenseSnapshotsPath("/group_license.*"));
      foreach ($data['groups'] as $group) {
        echo "<tr class=\"cellColor" . ($i++ % 2) . "\">";
        echo "<td style=\"text-align:center\">" . $group['customer_name'] . "</td>";
        echo "<td style=\"text-align:center\">" . $link->getLink($_SERVER['PHP_SELF'] . '?operation=licinventory&customer_filter=' . $group['customer_id'] . '&inv_action=group_licenses&group_uid=' . $group['group_uid'],
          $group['group_name']) . "</td>";
        echo "<td style=\"text-align:center\">" . $group['group_software_release'] . "</td>";
        $testFilename = $conf->getTestLicenseSnapshotsPath("/" . $group['snapshot']);
        if (!in_array($testFilename, $testFiles)) {
          echo "<td style=\"text-align:center;color:red;\">" . preg_replace('/.*[.]/', '', preg_replace('/[.]txt/', '', $group['snapshot'])) . " - file missing</td>";
        } else {
          echo "<td style=\"text-align:center\">" . preg_replace('/.*[.]/', '', preg_replace('/[.]txt/', '', $group['snapshot'])) . "</td>";
        }
        echo "</tr>";
      }
    }

    ?>
    </table>
    </td>
    </tr>
    </table>
    </form>
    <?php
    if (!empty($tests)) {

      echo "<div id='testresults' class='overlay'><div id='testresults_child'><table class='sort' style='width:100%;'><thead><tr><th colspan='3'><h5>Regression Tests</h5></th></tr>";
      echo "<tr><th>Test (date)</th><th>Passed</th><th>Failed</th></tr></thead><tbody>";
      foreach ($tests as $test_uid => $fields) {
        echo "<tr><td> <a href='index.php?operation=adminTools&toolType=showRegressionTests&test_uid=$test_uid' target='_blank'>" . $fields['reg_date'] . "</a></td><td>" . $fields['reg_passed'] . "</td><td>" . $fields['reg_failed'] . "</td></tr>";
      }
      echo '</tbody></table><br>
  <input type="button" class="button" name="close_past_tests" value="Close Window" onClick="overlay(\'testresults\')"/>
  </div></div>';
    }
  }
  // .........................................................................................................
  elseif ($toolType == 'compareLicenseFiles') {

    ?>
    <form name="compareLicenseFiles" enctype="multipart/form-data" method="post" action="generateLicense.php" onsubmit="return checkCompareTwoFilesForm();">
    <tr>
    <td><h1>Compare License Files Tool</h1></td>
    </tr>
    <tr>
    <td width="100%">Please specify the first license file:<br><input type="file" name="licensefile1" size="40" class="button"></td>
    </tr>
    <tr>
    <td width="100%">Please specify the second license file:<br><input type="file" name="licensefile2" size="40" class="button"></td>
    </tr>
    <tr>
    <td width="100%"><br>Filter out equal values?<input type="checkbox" name="filterOutEqualValues" /></td>
    </tr>
    <tr>
    <td><br><?php
      echo submit_button('Compare Licenses Files', 'compareLicenseFiles');

      ?><td>
    </tr>
    </form>
    <?php
  }
  ?>
    </tr>
  </table>
  </td>
  <?php
}
/**
  ######  ####### ######  ####### ######  #######  #####
  #     # #       #     # #     # #     #    #    #     #
  #     # #       #     # #     # #     #    #    #
  ######  #####   ######  #     # ######     #     #####
  #   #   #       #       #     # #   #      #          #
  #    #  #       #       #     # #    #     #    #     #
  #     # ####### #       ####### #     #    #     #####
 */

/**
 * build_reporting_body()
 *
 * @param BSFTScriptContext $context
 * @param mixed $report_months
 * @param string $report_type
 * @param mixed $serverTypes
 * @return
 */
function build_reporting_body($context, $report_months, $report_type = '', $serverTypes, $licensableEntities, $rs) {
  $util = new Utility();
  $repTools = new ReportingTools();
  // reports are built as group, reportType, reportName
  $reports = array(
    array('Basic Reports', 'inventoryData', 'Inventory Data'),
    array('Basic Reports', 'userInformation', 'User Information'),
    array('Basic Reports', 'journalingReports', 'Journal File Data'),
    array('Basic Reports', 'buildReporting', 'Raw AS Monthly Data'),
    array('Basic Reports', 'unknownHostIds', 'Unknown Host Ids Data'),
    array('Basic Reports', 'bwClusterStatus', 'Cluster Status'),
    array('Basic Reports', 'permissionsmatrix', 'Permissions Matrix'),
    array('Auditing Reports', 'bwClusterNotReporting', 'Clusters Not Reporting'),
    array('Auditing Reports', 'bwClusterExpiring', 'Clusters/Groups Expiring'),
    array('Auditing Reports', 'bwDecomClusterReporting', 'Decommissioned Clusters Reporting'),
    array('Auditing Reports', 'bwUsageReporting', 'Group Usage Report'),
    array('Auditing Reports', 'bwAuditReporting', 'Inventory Shipped Audit Report'),
    array('Auditing Reports', 'bwAuditServersDeliveredByProduct', 'Servers Delivered by Product'),
    array('Auditing Reports', 'bwMatrixServersDeliveredByProduct', 'Servers Delivered by Product Matrix'),
    array('Auditing Reports', 'reportOnDecomInventory', 'Decommissioned Inventory Report'),
    array('Licensing Reports', 'bwLicensableEntityReport', 'Licensable Entity Settings'),
    array('Licensing Reports', 'bwProductProfiles', 'Product Profiles'),
    array('Licensing Reports', 'bwLEAttributes', 'Licensable Entities Attributes'),
    array('Licensing Reports', 'bwLEcompatibility', 'Licensable Entities Server Compatibility'),
    array('Licensing Reports', 'bwLicenseAllocationReport', 'License Allocation Summary'),
    array('Licensing Reports', 'bwLicenseTeasersReport', 'License Teasers Assignments'),
    array('Licensing Reports', 'bwLicenseTeasersProfilesReport', 'License Teasers Profiles'),
    array('Licensing Reports', 'bwLicenseProductsReport', 'Licensed Products Inventory'),
    array('Licensing Reports', 'bwLicensingProgressSummaryReport', 'Licensing Progress Summary'),
    array('Licensing Reports', 'bwLicensingHistory', 'Licensing History'),
    array('Licensing Reports', 'bwLEMatrix', 'Licensable Entity Compatibility Matrix'),
    array('Licensing Reports', 'bwResetCustomers', 'Customers Ready for Reset'),
    array('Licensing Reports', 'bwManualOpsReport', 'Manual Opportunities'),
    array('Licensing Reports', 'bwUndeliveredReport', 'Undelivered Inventory'),
    array('SOX Reports', 'bwSOXUserAccessReport', 'User and Roles Provisioning'),
    array('SOX Reports', 'bwSOXCodeChangeLogs', 'Code Change Logs'),
    array('Trending Reports', 'bwClientReports', 'BroadWorks Clients'),
    array('Trending Reports', 'bwRelease', 'BroadWorks Releases Analysis'),
    array('Trending Reports', 'bwReleaseDsitribution', 'BroadWorks Releases Monthly Distribution'),
    array('Trending Reports', 'asTrending', 'Sell-through'),
    array('Trending Reports', 'asGroupTrending', 'Groups Sell-through'),
    array('Trending Reports', 'bwLicenseUpgradeHistoryReport', 'License Upgrade Historys'),
    array('Trending Reports', 'bwShippedInventoryReport', 'Shipped Inventory History'),
    array('Trending Reports', 'bwOpportunitiesProgressReport', 'Opportunities Progress History'),
    array('Trending Reports', 'bwSystemsProgressReport', 'Systems Progress Report')
  );
  $repTools->add_reports($reports);

  ?>
  <td>
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
  <table>
    <?php
    $repTools->buildHTMLTR(array(
      $repTools->buildHTMLTD("Report Type:"),
      $repTools->buildHTMLTD($repTools->render_report_selector($report_type)),
    ));

    ?>
  </table>
  <table width=70%>
    <?php
    /** RAW DATA REPORT * */
    if ($report_type == 'buildReporting') {
      echo $repTools->form_header($report_type);
      echo $repTools->full_width_title('Raw Licensing Information Reports', true);

      ?>
    <tr>
    <td width="3%"></td>
    <td width="22%">Type of Systems to report on:</td>
    <td width="15%"><input type=checkbox name="repProduction" checked>Production</input></td>
    <td width="15%"><input type=checkbox name="repLab">Lab</input></td>
    <td width="15%"><input type=checkbox name="repOthers">Others</input></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Additional Information:</td>
    <td width="15%"><input type="radio" name="repData" value="repNone" checked>None</input></td>
    <td width="15%"><input type="radio" name="repData" value="repServices">Services</input></td>
    <td width="15%"><input type="radio" name="repData" value="repTrunking">Trunking</input></td>
    <td width="15%"><input type="radio" name="repData" value="repLicensePack">License Pack</input></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%"></td>
    <td width="15%" align="center">Looked up data:</td>
    <td width="15%"><input type=checkbox name="repTotal" checked>Total</input></td>
    <td width="15%"><input type=checkbox name="repHostedTotal">Hosted</input></td>
    <td width="15%"><input type=checkbox name="repTrunkingTotal">Trunking</input></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Report Month:</td>
    <td width="15%">
    <select name="rep_start_month">
      <?php
      for ($i = 0; is_array($report_months) && $i < count($report_months); $i++) {
        $monthYear = $report_months[$i]['report_month'] . "/" . $report_months[$i]['report_year'];
        echo '<option value="' . $monthYear . '">' . $monthYear . '</option>';
      }

      ?>
    </select>
    </td>
    <td width="15%"></td>
    <td width="15%"></td>
    </tr>
    <?php
    $repTools->spacing_tr(true);
    $repTools->outPutColspanLine(submit_button('Get Spreadsheet') . hidden_fn('reportOnLicensingInformation'), 5);
    $repTools->closeForm(true);
    /** INVENTORY DATA REPORT * */
  } else if ($report_type == 'bwUsageReporting') {
    echo $repTools->form_header('invBuildReporting');
    echo $repTools->full_width_title('Group Usage Report', true);

    ?>
    <tr>
    <td width="3%"></td>
    <td width="22%">Report Month:</td>
    <td width="15%">
    <select name="rep_start_month">
      <?php
      for ($i = 0; is_array($report_months) && $i < count($report_months); $i++) {
        $monthYear = $report_months[$i]['report_month'] . "/" . $report_months[$i]['report_year'];
        echo '<option value="' . $monthYear . '">' . $monthYear . '</option>';
      }

      ?>
    </select>
    </td>
    <td width="15%"></td>
    <td width="15%"></td>
    </tr>
    <tr>
    <td colspan=5>a cluster name in red means the license cannot generate for that cluster</td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Customer to report on:</td>
    <td colspan=3>
    <select  name="customer_id" id="customer_id" size="15">
      <?php
      $groupCustomers = $rs['groups_customers'];

      foreach ($groupCustomers as $customer_id => $customer_name) {
        echo '<option value="' . $customer_id . '"' . ((isset($rs['customer_id']) && ($rs['customer_id'] == $customer_id)) ? (' selected="selected"') : '') . '>' . $customer_name . ' (' . $customer_id . ')</option>' . "\n";
      }

      ?>
    </select>
    </td>
    </tr>
    <?php
    $repTools->spacing_tr(true);
    $repTools->outPutColspanLine(submit_button('Get Spreadsheet') . hidden_fn('reportGroupUsage'), 5);
    $repTools->closeForm(true);
    /** AUDIT REPORT * */
  } else if ($report_type == 'bwAuditReporting') {
    echo $repTools->form_header($report_type);
    echo $repTools->spacing_tr();
    echo $repTools->full_width_title('Inventory Shipped Auditing Report');

    ?>
    <tr><td>the report will take on average one minute per month to run.</td></tr>
    <?php echo $repTools->spacing_tr(); ?>
    <tr><td width="20%">From <input type='text' class='textbox' style='width: 75px;' id='from' name='from' value='<?php
        echo date("Y-m-d", mktime(0, 0, 0, date("m") - 3, date("d"), date("Y")));

        ?>'/></td>
    <td width="20%">To <input type='text'  class='textbox'  style='width: 75px;' id='to' name='to' value='<?php
      echo date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));

      ?>'/></td><td></td><td></td><td></td></tr>
    <?php
    $repTools->spacing_tr(true);
    $repTools->outPutColspanLine(submit_button('Get Spreadsheet') . hidden_fn('reportOnAuditReporting'), 2);
    $repTools->closeForm(true);

    ?>
    <script type="text/javascript" src="javascripts/manual_op_report.js"></script>
    <?php
    /** EXPORT PRODUCT PROFILES CSV * */
  } else if ($report_type == 'bwProductProfiles') {
    echo $repTools->form_header($report_type, "", "get");
    echo $repTools->spacing_tr();
    echo $repTools->full_width_title('Product Profiles Report');
    $formTools = new FormSelectors();
    $formTools->setSelectName('product_code');
    $formTools->setSelectSort();
    foreach ($rs['products'] as $productUid => $product) {
      $formTools->addOption("{$product['name']} ({$product['product_code']})", $product['product_code']);
    }
    $prodProductSelect = $formTools->renderSelect();
    echo $repTools->spacing_tr();
    echo $repTools->full_width_title("Select  product: " . $prodProductSelect);
    //add product selectpr

    ?>
    <?php echo $repTools->spacing_tr(); ?>
    <tr><td valign="top" align="center" colspan=2>
    <?php echo submit_button('Get Spreadsheet') . hidden_fn('reportOnProductProfiles'); ?></td></tr>
    <?php
    $repTools->closeForm(true);
    /** EXPORT LICENSABLES AND THEIR ATTRIBUTES * */
  } else if ($report_type == 'bwLEAttributes') {
    echo $repTools->form_header($report_type);
    echo $repTools->spacing_tr();
    echo $repTools->full_width_title('Licensable Entities with Attributes Report');

    ?>
    <?php echo $repTools->spacing_tr(); ?>
    <tr><td valign="top" align="center" colspan=2>
    <?php echo submit_button('Get Spreadsheet') . hidden_fn('reportOnLicensableAttributes'); ?></td></tr>
    <?php
    $repTools->closeForm(true);
    /** EXPORT LICENSABLES AND THEIR SERVERS COMPATIBILITY * */
  } else if ($report_type == 'bwLEcompatibility') {
    echo $repTools->form_header($report_type);
    echo $repTools->spacing_tr();
    echo $repTools->full_width_title('Licensable Entities Server Compatibility Report');

    ?>
    <?php echo $repTools->spacing_tr(); ?>
    <tr><td valign="top" align="center" colspan=2>
    <?php echo submit_button('Get Spreadsheet') . hidden_fn('reportOnLicensableCompatibility'); ?></td></tr>
    <?php
    $repTools->closeForm(true);
    /** AUDIT SERVERS DELIVERED BY PRODUCT REPORT * */
  } else if ($report_type == 'bwAuditServersDeliveredByProduct') {
    echo $repTools->form_header($report_type);
    echo $repTools->spacing_tr();
    echo $repTools->full_width_title('Servers Delivered by Product Report');

    ?>
    <tr><td colspan="3">This report shows all the customer product missing one or more servers.</td></tr>
    <tr><td colspan="3">Customer Release is based on the highest release in the selected system types.</td></tr>
    <tr><td colspan="3">Only Active Customers are considered in the report (Active, Jeopardy statues in Salesforce).</td></tr>
    <tr><td colspan="3">Clusters of type ls,ccrs and cds are ignored.</td></tr>
    <?php
    echo $repTools->spacing_tr();
    echo $repTools->get_type_of_systems_tr(2);
    echo $repTools->spacing_tr();

    ?>
    <tr><td valign="top" align="center" colspan=2>
    <?php echo submit_button('Get Spreadsheet') . hidden_fn('reportOnAuditServersDeliveredByProduct'); ?></td></tr>
    <?php
    $repTools->closeForm(true);
    /** UNKNOWN HOSTIDS DATA REPORT * */
  } else if ($report_type == 'bwMatrixServersDeliveredByProduct') {
    echo $repTools->form_header($report_type);
    echo $repTools->spacing_tr();
    echo $repTools->full_width_title('Matrix for Servers Delivered by Product Report');

    ?>
    <tr><td colspan="3">This report shows the required servers by product, using the Licensable Entities compatibility.</td></tr>
    <tr><td colspan="3">Clusters of type ls,ccrs and cds are ignored.</td></tr>
    <?php
    echo $repTools->spacing_tr();
    echo $repTools->get_type_of_systems_tr(2);
    echo $repTools->spacing_tr();
    echo $repTools->get_type_of_servers_tr($rs, array("ignore", "cmas", "cmps"), array("ams", "as", "ns", "ms", "xsp", "ps", "dbs", "ems", "scf", "ums", "uss", "wrs"));
    echo $repTools->full_width_title("<input type='button' value='Check All' class='button' onclick='checkAll(\"cbxServers\")'>", false, 5, false);
    echo $repTools->spacing_tr();
    echo $repTools->get_multiselect_releases_tr($rs);
    echo $repTools->spacing_tr();

    ?>
    <tr><td valign="top" align="center" colspan=2>
    <?php echo submit_button('Get Spreadsheet') . hidden_fn('reportOnMatrixServersDeliveredByProduct'); ?></td></tr>
    <?php
    $repTools->closeForm(true);
    /** UNKNOWN HOSTIDS DATA REPORT * */
  } else if ($report_type == 'reportOnDecomInventory') {
    echo $repTools->form_header($report_type);
    echo $repTools->spacing_tr();
    echo $repTools->full_width_title('Decommissioned Systems / Servers with Inventory');
    $repTools->outPutColspanLine("This report shows the servers with inventory still setup and either the system or server is decommissioned.", 3);
    echo $repTools->spacing_tr();
    $repTools->outPutColspanLine(submit_button('Get Spreadsheet') . hidden_fn('reportOnDecomInventory'), 2);
    $repTools->closeForm(true);
    /** UNKNOWN HOSTIDS DATA REPORT * */
  } else if ($report_type == 'inventoryData') {
    $show_decom = new ReportingTools();
    echo $repTools->form_header('invBuildReporting');
    echo $repTools->full_width_title('Provisioned Information Reports', true);

    ?>
    <tr>
    <td width="3%"></td>
    <td width="22%">Type of Systems to report on:</td>
    <td width="15%"><input type=checkbox name="invProduction" checked>Production</input></td>
    <td width="15%"><input type=checkbox name="invLab">Lab</input></td>
    <td width="15%"><input type=checkbox name="invOthers">Others</input></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td colspan="4">
      <?php
      echo $show_decom->empty_clusters_selector();

      ?>
    </td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td colspan="4">
      <?php
      echo $show_decom->decom_hostids_selector();

      ?>
    </td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td colspan="4">
      <?php
      echo $show_decom->decom_clusters_selector();

      ?>
    </td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td colspan="4">
      <?php
      echo $show_decom->decom_systems_selector();

      ?>
    </td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Customer to report on:</td>
    <td colspan=3>
    <select multiple name="customerInvRepList[]" id="customerInvRepListId" size="15">
      <?php
      $customerNameList = $rs['customerNameList'];
      $customerIdList = $rs['customerIdList'];
      for ($i = 0; $i < count($customerNameList); $i++) {
        echo '<option value="' . $customerIdList[$i] . '"' . ((isset($rs['customer_id']) && ($rs['customer_id'] == $customerIdList[$i])) ? (' selected="selected"') : '') . '>' . $customerNameList[$i] . ' (' . $customerIdList[$i] . ')</option>' . "\n";
      }
      ?>
    </select>
    </td>
    </tr>
                        <tr>
                            <td width="3%"></td>
                            <td width="22%"></td>
                            <td colspan=3>
                                <input type="checkbox" id ="select_all_customers"><label for="select_all_customers">All Customers</label></input>
                                <script>
                                      $('#select_all_customers').click(function() {
                                        if (this.checked) {
                                              $('#customerInvRepListId option').prop('selected', false);
                                            }
                                          });
                                    </script>
                            </td>
                        </tr>
    <?php echo $repTools->spacing_tr(); ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnProvisionnedInformartion');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** UNKNOWN HOSTIDS DATA REPORT * */
  } else if ($report_type == 'userInformation') {
    echo $repTools->form_header('invBuildReporting');
    echo $repTools->full_width_title('LRS User List Report', true);

    ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnUserRightsInformartion');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** UNDELIVERED INVENTORY DATA REPORT * */
  } else if ($report_type == 'bwUndeliveredReport') {
    echo $repTools->form_header('bwUndeliveredReport');
    echo $repTools->full_width_title('Undelivered Inventory Report', true);

    ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnUndeliveredInventory');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** SKUs DATA REPORT * */
  } else if ($report_type == 'bwClusterStatus') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Cluster Status Report', true);

    ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnClusterStatus');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** SKUs DATA REPORT * */
  } else if ($report_type == 'bwClusterNotReporting') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Clusters Not Reporting Report', true);

    ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnNonReportingClusters');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** SKUs DATA REPORT * */
  } else if ($report_type == 'bwClusterExpiring') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Clusters and Groups with Expiring(ed) Licenses Report', true);
    ?>
        <tr>
            <td>Expiring in <?php
            echo $repTools->getTextInput("days_interval", "text", "days_interval", "10", "textAlignRight", "30");

                ?>
        days from now
        </td>
        </tr>
        <tr>
        <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnExpiringClusters');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** SKUs DATA REPORT * */
  } else if ($report_type == 'bwDecomClusterReporting') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Clusters Reporting when flagged as decommissioned Report', true);

    ?>
    <tr>
    <td>Report on the last <select name="days_interval"><?php
        for ($i = 1; $i <= REPORTING_DAYS_SELECT; $i++) {
          echo "<option value=$i" . ($i == 2 ? ' selected="selected"' : '') . ">$i</option>";
        }

        ?>
    </select> day(s)
    </td>
    </tr>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnDecomClustersReporting');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** SKUs DATA REPORT  * */
  } else if ($report_type == 'permissionsmatrix') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Permissions Matrix Report', true);

    ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnPermissions');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** LICENSE GENERATION HISTORY REPORT * */
  } else if ($report_type == 'bwLicensingHistory') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('License Generation History Report', true);

    ?>
    <tr>
    <td width="22%">Type of Systems to report on:</td>
    <td width="15%"><input type=checkbox name="system_type_production" checked>production</input></td>
    <td width="15%"><input type=checkbox name="system_type_lab" checked>lab</input></td>
    <td width="15%"><input type=checkbox name="system_type_demo" checked>demo</input></td>
    <td width="15%"><input type=checkbox name="system_type_trial" checked>trial</input></td>
    </tr>
    <tr>
    <td width="22%">Type of Servers to report on:</td>
    <?php
    // some server types to hide - requested by Joyce
    $rs['serverTypeList'] = array_diff($rs['serverTypeList'], array("ignore", "cmas", "cmps"));
    $i = 1;
    foreach ($rs['serverTypeList'] as $serverType) {
      echo '<td width="15%"><input type=checkbox name="serverType_' . $serverType . '" checked>' . $serverType . '</input></td>';
      if ($i++ % 4 == 0)
        echo '</tr><tr><td width="22%"></td>';
    }

    ?>
    </tr>
    <tr>
    <td width="22%">License Generation Reason to include in report:</td>
    <?php
    foreach ($rs['reasonList'] as $reason) {
      $checkedStr = ($reason == "Test LRS" ? "" : " checked");
      echo '<td width="15%" colspan=4><input type=checkbox name="reason_' . preg_replace('/ /', '~', $reason) . '" ' . $checkedStr . '>' . $reason . '</input></td>';
      echo '</tr><tr><td width="22%"></td>';
    }

    ?>
    </tr>
    <?php echo $repTools->spacing_tr(); ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnLicensingHistory');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** UNKNOWN HOSTIDS DATA REPORT * */
  } else if ($report_type == 'unknownHostIds') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Unknown Host Ids Information Reports', true);

    ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnUnknownHostIdsInformartion');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** BASIC DASHBOARD DATA REPORT * */
  } else if ($report_type == 'bwLEMatrix') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Licensable Entities Compatibility Matrix Report', true);

    ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnLEMatrix');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** Report searching for customers ready for Reset * */
  } else if ($report_type == 'bwResetCustomers') {
    echo $repTools->form_header('ResetCustomers');
    echo $repTools->full_width_title('Customers ready for a reset of all Opportunities', true);

    ?>
    <tr>
    <td width="22%">Show customers not currently locked:</td>
    <td width="15%"><input type=checkbox name="filter_locked" checked="checked"></input></td><td colspan="3"></td></tr>
    </tr>
    <?php echo $repTools->spacing_tr(); ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnResetCustomers');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** MANUAL OPPORTUNITIES REPORT * */
  } else if ($report_type == 'bwManualOpsReport') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Manual Opportunities Report', true);

    ?>
    <tr><td width="15%">From <input type='text' class='textbox' style='width: 75px;' id='from' name='from' value='<?php
        echo date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1));

        ?>'/></td>
    <td width="15%">To <input type='text'  class='textbox'  style='width: 75px;' id='to' name='to' value='<?php echo date("Y-m-d"); ?>'/></td><td></td><td></td><td></td></tr>
    <tr>
        <td colspan="2"><input type=checkbox name="filter_deleted" id="filter_deleted"></input><label for="filter_deleted">Show deleted opportunities</label></td>
        </tr>
            <?php echo $repTools->spacing_tr(); ?>
            <tr><td valign="top" align="center" colspan=2>
            <?php echo submit_button('Get Spreadsheet') . hidden_fn('reportOnManualOpportunities'); ?></td></tr></form>
    <script type="text/javascript" src="javascripts/manual_op_report.js"></script>
    <?php
    /** LICENSING TEASERS REPORT * */
      } else if ($report_type == 'bwLicenseUpgradeHistoryReport') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('License Upgrade History Report', true);

    ?>
    <tr>
    <td colspan="5">
    By Default this report will return a list of all clusters sending back information about their usage.(a.k.a phone home email) or clusters that went through tech support.<br>
    The usage report tells us about the current version of the license and that is how we detect if the cluster was upgraded.<br>
    <br>
    To see more clusters, the include clusters not reporting will add information about the latest license generated and the version associated to that license.<br>
    The Report Date for these clusters will show "N/A"<br>
    </td>
    </tr>

    <tr>
    <td align="center" colspan="5">
      <?php
      echo $repTools->all_clusters_selector('Include clusters not reporting');

      ?>
    </td>
    </tr>
    <?php echo $repTools->spacing_tr(); ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnLicenseUpgradeHistory');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** SHIPPED INVENTORY REPORT * */
  } else if ($report_type == 'bwShippedInventoryReport') {
    $drop_default = new ReportingTools();
    $drop_default->show_granularity_selector = true;
    $drop_default->default_granularity = GRANULARITY_MONTH;
    $drop_default->max_months = REPORTING_MONTHS_SELECT;
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Shipped Inventory History Report', true);

    ?>
    <tr>
    <td><?php echo $drop_default->render_date_range_selector(); ?></td>
    </tr>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnShippedInventoryHistory');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** OPPORTUNIES PROGRESS HISTORY REPORT * */
  } else if ($report_type == 'bwOpportunitiesProgressReport') {

    $drop_default = new ReportingTools();
    $drop_default->show_granularity_selector = true;
    $drop_default->default_granularity = GRANULARITY_MONTH;
    $drop_default->max_months = REPORTING_MONTHS_SELECT;
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Opportunities Progress History Report', true);

    ?>
    <tr>
    <td><?php echo $drop_default->render_date_range_selector(); ?></td>
    </tr>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnOpportunitiesHistory');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** SYSTEMS PROGRESS REPORT * */
  } else if ($report_type == 'bwSystemsProgressReport') {

    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Systems Progress History Report', true);

    ?>
    <tr>
    <td><?php echo $repTools->quarter_last_current_selector(); ?></td>
    </tr>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnSystemsHistory');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** LICENSING TEASERS REPORT * */
  } else if ($report_type == 'bwLicenseTeasersReport') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('License Teasers Assignement Reports', true);

    ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnLicenseTeasers');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** LICENSED TEASER PROFILES REPORT * */
  } else if ($report_type == 'bwLicenseTeasersProfilesReport') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('License Teasers Profiles Reports', true);

    ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnLicenseTeasersProfiles');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** LICENSED PRODUCTS REPORT * */
  } else if ($report_type == 'bwLicenseProductsReport') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Licensed Products for All Customers Report', true);

    ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo $repTools->create_radio_group('Show profiles used', array('yes' => 'Yes', 'no' => 'No'), 'addProfiles', 'no');
      echo "<br><br>";
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnLicensedProducts');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** LICENSING PROGRESS * */
  } else if ($report_type == 'bwLicensingProgressSummaryReport') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Licensing Progress Summary Report', true);

    ?>
    <tr>
    <td colspan=5 width="100%">Provides stats about customers with 1 or more clusters in any state besides "not started", grouped by Customer</td>
    </tr>
    <?php
    echo $repTools->spacing_tr();
    echo $repTools->get_type_of_systems_tr();

    ?>
    <tr>
    <td width="22%">Type of Customers to report on:</td>
    <td width="15%"><input type=checkbox name="customer_type_direct" checked>direct</input></td>
    <td width="15%"><input type=checkbox name="customer_type_indirect" checked>indirect</input></td>
    <td width="15%"><input type=checkbox name="customer_type_reseller" checked>reseller</input></td>
    </tr>
    <?php echo $repTools->spacing_tr(); ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnLicensingProgressSummary');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** LICENSABLE ENTITY REPORTING * */
  } else if ($report_type == 'bwLicensableEntityReport') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Licensable Entity Settings Report', true);

    ?>
    <tr>
    <td width="35%">Licensable Entity to report on:</td>
    <td>
    <select name="licensableEntity">
      <?php
      for ($i = 0; $i < count($licensableEntities); $i++) {
        echo '<option value="' . $licensableEntities[$i]['licensable_entity_uid'] . '">' . $licensableEntities[$i]['licensable_entity_name'] . '</option>';
      }

      ?>
    </select>
    </td>
    </tr>
    <?php echo $repTools->spacing_tr(); ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnLicensableEntitySettings');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** LICENSING INVENTORY REPORTING * */
  } else if ($report_type == 'bwLicenseAllocationReport') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('License Allocation Reports', true);

    ?>
    <tr>
    <td width="3%"></td>
    <td width="22%">Type of Systems to report on:</td>
    <td width="15%"><input type=checkbox name="relAllocProduction" checked>Production</input></td>
    <td width="15%"><input type=checkbox name="relAllocLab">Lab</input></td>
    <td width="15%"><input type=checkbox name="relAllocOthers">Others</input></td>
    </tr>
    <tr>
    <td></td>
    <td>Customer Name:</td>
    <td>
    <select name="relAllocCustomerId">
      <?php
      $customerNameList = $rs['customerNameList'];
      $customerIdList = $rs['customerIdList'];
      for ($i = 0; $i < count($customerNameList); $i++) {
        echo '<option value="' . $customerIdList[$i] . '"' . ((isset($rs['customer_id']) && ($rs['customer_id'] == $customerIdList[$i])) ? (' selected="selected"') : '') . '>' . $customerNameList[$i] . '</option>' . "\n";
      }

      ?>
    </select>
    </td>
    <td></td>
    <td></td>
    </tr>
    <?php echo $repTools->spacing_tr(); ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnLicenseAllocation');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** RELEASE REPORTING * */
  } else if ($report_type == 'bwRelease') {
    echo $repTools->form_header('releaseReporting');
    echo $repTools->full_width_title('BroadWorks Release Related Reports', true);

    ?>
    <tr>
    <td width="3%"></td>
    <td width="22%">Type of Systems to report on:</td>
    <td width="15%"><input type=checkbox name="relProduction" checked>Production</input></td>
    <td width="15%"><input type=checkbox name="relLab">Lab</input></td>
    <td width="15%"><input type=checkbox name="relOthers">Others</input></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Type of Cluster to report on:</td>
    <td width="15%">
    <select name="relRepServerType"">
      <?php
      for ($i = 0; $i < count($serverTypes); $i++) {
        echo '<option value="' . $serverTypes[$i] . '">' . $serverTypes[$i] . '</option>' . "\n";
      }

      ?>
    </select>
    </td>
    <td width="15%"></td>
    <td width="15%"></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Report Type:</td>
    <td width="15%"><input type="radio" name="bwRelReportTypeData" value="monthlySystemDistribution" checked>Monthly System Distribution</input></td>
    <td width="15%"><input type="radio" name="bwRelReportTypeData" value="monthlyClusterDistribution">Monthly Cluster Distribution</input></td>
    <td width="15%"></td>
    </tr>
    <?php echo $repTools->spacing_tr(); ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnBwReleaseInformartion');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** RELEASE DISTRIBUTION REPORTING * */
  } else if ($report_type == 'bwReleaseDsitribution') {
    echo $repTools->form_header('releaseMontlyReporting');
    echo $repTools->full_width_title('BroadWorks Montly Release Distribution Reports', true);

    ?>
    <tr>
    <td width="3%"></td>
    <td width="22%">Type of Systems to report on:</td>
    <td width="15%"><input type=checkbox name="relMonthlyProduction" checked>Production</input></td>
    <td width="15%"><input type=checkbox name="relMonthlyLab">Lab</input></td>
    <td width="15%"><input type=checkbox name="relMonthlyOthers">Others</input></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Type of Cluster to report on:</td>
    <td width="15%">
        <select name="relMontlyServerType">
        <?php
      for ($i = 0; $i < count($serverTypes); $i++) {
        echo '<option value="' . $serverTypes[$i] . '">' . $serverTypes[$i] . '</option>' . "\n";
      }

          ?>
    </select>
    </td>
    <td width="15%"></td>
    <td width="15%"></td>
    </tr>
        <tr>
        <td width="3%"></td>
        <td width="22%">Last x Years:</td>
        <td width="15%">
            <select name="relMontlyLastYears">
            <?php
            $years = array(
              "-1" => "All",
              "2" => "2 years",
              "5" => "5 years",
              "10" => "10 years"
            );
            foreach ($years as $value => $label) {
              echo "<option value='$value'" . ($value === 5 ? " selected='selected'" : "") . ">$label</option>\n";
            }

                ?>
        </select>
        </td>
        <td width="15%"></td>
        <td width="15%"></td>
        </tr>
        <?php echo $repTools->spacing_tr(); ?>
        <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnBwMonthlyReleaseInformartion');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** CLIENT REPORTS * */
  } else if ($report_type == 'bwClientReports') {
    echo $repTools->form_header('clientsReporting', 'checkBroadworksClients');
    echo $repTools->full_width_title('BroadWorks Client Reports', true);
    $drop_default = new ReportingTools();

    $drop_default->default_granularity = GRANULARITY_MONTH;
    $drop_default->default_month = REPORTING_MONTHS_SELECT_DEFAULT_YEAR;
    $drop_default->max_months = REPORTING_MONTHS_SELECT;
    $LE_services = array();

    foreach ($licensableEntities as $LE) {
      if ($LE['licensable_entity_type'] === 'service') {
        $name = $context->getReportingDB()->clientNameToExternalName($LE['licensable_entity_name']);
        $LE_services[] = array('id' => $LE['licensable_entity_name'], 'name' => $name);
      }
    }

    $services_select = $util->build_select_options($LE_services, 'LE_select', null, false, null, false, 'LE_select[]', 8, true);

    ?>
    <tr>
    <td width="3%"></td>
    <td width="22%">Type of Systems to report on:</td>
    <td width="15%"><input type=checkbox name="clientProduction" checked>Production</input></td>
    <td width="15%"><input type=checkbox name="clientLab">Lab</input></td>
    <td width="15%"><input type=checkbox name="clientOthers">Others</input></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Report Type:</td>
    <td width="15%"><input type="radio" name="bwClientReportTypeData" value="monthlyDistribution" checked>Monthly Distribution</input></td>
    <td width="15%"></td>
    <td width="15%"></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%"> <?php echo $drop_default->render_date_range_selector(); ?></td>
    <td colspan="3"></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Select Customer:</td>
    <td width="15%"><select name="customerid[]" id="customerid" multiple size='10'>
    <option value="" selected="selected">All</option>
    <?php
    foreach ($rs['customerNameList'] as $key => $value) {
      echo "<option value='" . $rs['customerIdList'][$key] . "'>" . $value . "</option>";
    }

    ?></select></td>
    <td width="15%"></td>
    <td width="15%"></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td colspan="4">Running more than 15 services for all customers will be slow (one minute per 10 services on average, split and aggregate will also take longer )</td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Service(s):</td>
    <td width="15%"><?php echo $services_select; ?></td>
    <td width="15%"></td>
    <td width="15%"></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Customers:</td>
    <td width="15%"><input type="radio" name="customersAggregation" value="sum" checked>Summed</input></td>
    <td width="15%"><input type="radio" name="customersAggregation" value="split" >Split</input></td>
    <td width="15%"><input type="radio" name="customersAggregation" value="aggregated" >Aggregated (all on one page)</input></td>
    </tr>
    <?php echo $repTools->spacing_tr(); ?>
    <tr>
    <td valign="top" align="center" colspan=5>

    <?php
    echo submit_button('Get Spreadsheet') . hidden_fn('reportOnClientInformartion');

    ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** SOX REPORT * */
  } else if ($report_type == 'bwSOXUserAccessReport') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('SOX User Access Report', true);

    ?>
    <tr>
    <td width="3%"></td>
    <td width="22%">Report On Last:</td>
    <td width="15%">
    <select name="userAccessReportReportingPeriod">
      <?php
      for ($i = 1; $i <= REPORTING_MONTHS_SELECT; $i++) {
        if ($i == REPORTING_MONTHS_SELECT_DEFAULT_QUARTER) {
          echo '<option value=' . $i . ' selected>' . $i . '</option>' . "\n";
        } else {
          echo '<option value=' . $i . '>' . $i . '</option>' . "\n";
        }
      }

      ?>
    </select> Months</td>
    <td width="15%"></td>
    <td width="15%"></td>
    </tr>
    <?php echo $repTools->spacing_tr(); ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Spreadsheet') . hidden_fn('reportOnSOXUserAccess');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** SOX REPORT * */
  } else if ($report_type == 'bwSOXCodeChangeLogs') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('SOX Code Change Logs', true);

    ?>
    <tr>
    <td width="3%"></td>
    <td width="22%">Report On Last:</td>
    <td width="15%">
    <select name="changeLogsReportingPeriod">
      <?php
      for ($i = 1; $i <= REPORTING_MONTHS_SELECT; $i++) {
        if ($i == REPORTING_MONTHS_SELECT_DEFAULT_QUARTER) {
          echo '<option value=' . $i . ' selected>' . $i . '</option>' . "\n";
        } else {
          echo '<option value=' . $i . '>' . $i . '</option>' . "\n";
        }
      }

      ?>
    </select> Months</td>
    <td width="15%"></td>
    <td width="15%"></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Output Format:</td>
    <td width="15%">
    <select name="outputFormat">
    <option value="xmlInLine">XML</option>
    <option value="htmlOutput">HTML</option>
    </select></td>
    <td width="15%"></td>
    <td width="15%"></td>
    </tr>
    <?php echo $repTools->spacing_tr(); ?>
    <tr>
    <td valign="top" align="center" colspan=5>
      <?php
      echo submit_button('Get Logs') . hidden_fn('reportOnSOXCodeChangeLogs');

      ?>
    </td>
    </tr>
    <?php
    $repTools->closeForm(true);
    /** SELL THROUGH REPORTS * */
  } else if ($report_type == 'asTrending') {
    echo $repTools->form_header('asTrendingReporting');
    echo $repTools->full_width_title('Application Server Trend Reports', true);
    $fs = new FormSelectors();
    $repTools->buildHTMLTR(array($repTools->buildHTMLTD("", 3),
      $repTools->buildHTMLTD("Type of Systems to report on:", 22),
      $repTools->buildHTMLTD($fs->checkbox("asTrendProduction", "", "asTrendProduction", null, false, "Production", null, true), 15),
      $repTools->buildHTMLTD($fs->checkbox("asTrendLab", "", "asTrendLab", null, false, "Lab"), 15),
      $repTools->buildHTMLTD($fs->checkbox("asTrendOthers", "", "asTrendOthers", null, false, "Others"), 15))
    );
    $tableContent = $repTools->openTable(false);
    $tableContent .= $repTools->buildHTMLTR(array($repTools->buildHTMLTD($repTools->create_radio_group("", array("PerCustomer" => "Per Customer"), "idPerCustomer", null,
          "asTrendPerCustomer"), 15),
      $repTools->buildHTMLTD($repTools->create_radio_group("", array("PerSystem" => "Per System"), "idPerSystem", null, "asTrendPerCustomer"), 15),
      $repTools->buildHTMLTD($repTools->create_radio_group("", array("PerCluster" => "Per Cluster"), "idPerCluster", "PerCluster", "asTrendPerCustomer"), 15),
      ), false);
    $tableContent .= $repTools->buildHTMLTR(array(
      $repTools->buildHTMLTD($fs->checkbox("asTrendNonReportCustomers", "", "asTrendNonReportCustomers", null, false, "Include Non Reporting Customers"), null, 3)
      ), false);

    $fs->resetSelectData();
    $monthSelect = $fs->addOptions(range(1, REPORTING_MONTHS_SELECT))
      ->setSelectDefault(REPORTING_MONTHS_SELECT_DEFAULT_YEAR)
      ->setSelectName("asTrendPeriod")
      ->setSelectNoEmpty()
      ->renderSelect();
    $tableContent .= $repTools->buildHTMLTR(array($repTools->buildHTMLTD("Report On Last " . $monthSelect . " Months", null, 3)), false);
    $tableContent .= $repTools->closeTable(false);

    $repTools->buildHTMLTR(array(
      $repTools->buildHTMLTD("", 3),
      $repTools->buildHTMLTD("Report Options", 22),
      $repTools->buildHTMLTD($tableContent, null, 3),
    ));
    $repTools->spacing_tr(true);
    $buttons = submit_button('Get Spreadsheet Report') . submit_button('Get Raw Data') . hidden_fn('reportOnAsTrendInformartion');
    $repTools->buildHTMLTR(array(
      $repTools->buildHTMLTD($buttons, null, 5, "reportButtons"),
    ));
    $repTools->closeForm(true);
    /** GROUP SELL THROUGH REPORTS * */
  } else if ($report_type == 'asGroupTrending') {
    echo $repTools->form_header('asGroupTrendingReporting', 'checkAsTrendingReporting');
    echo $repTools->full_width_title('AS Groups Trend Reports', true);

    ?>
    <tr>
    <td width="3%"></td>
    <td width="22%">Type of Systems to report on:</td>
    <td width="15%"><input type=checkbox name="asTrendProduction" checked>Production</input></td>
    <td width="15%"><input type=checkbox name="asTrendLab">Lab</input></td>
    <td width="15%"><input type=checkbox name="asTrendOthers">Others</input></td>
    </tr>
    <tr>
    <td width="3%"></td>
    <td width="22%">Report Options:</td>
    <td colspan=3>
    <table>
    <tr>
    <td width="15%"></td>
    <td width="15%"></td>
    <td width="15%"><input type="hidden" name="asTrendPerCustomer" value="PerCluster" checked></input></td>
    </tr>
    <tr>
    <td colspan=3><input type=checkbox name="asTrendNonReportCustomers">Include Non Reporting Customers</input></td>
    </tr>
    <tr>
    <td colspan=3>Report On Last
    <select name="asTrendPeriod">
      <?php
      for ($i = 1; $i <= REPORTING_MONTHS_SELECT; $i++) {
        if ($i == REPORTING_MONTHS_SELECT_DEFAULT_YEAR) {
          echo '<option value=' . $i . ' selected>' . $i . '</option>' . "\n";
        } else {
          echo '<option value=' . $i . '>' . $i . '</option>' . "\n";
        }
      }

      ?>
    </select> Months
    </td>
    </tr>
    </table>
    </td>
    </tr>
    <?php
    $repTools->spacing_tr(true);
    $repTools->outPutColspanLine(submit_button('Get Spreadsheet') . hidden_fn('reportOnAsGroupTrendInformartion'), 5);
    $repTools->closeForm(true);
    /* ?>
      /** JOURNAL FILE REPORTS * */
  } else if ($report_type == 'journalingReports') {
    echo $repTools->form_header($report_type);
    echo $repTools->full_width_title('Journal File Reports', true);

    ?>
    <tr><td width="5%">From:  </td>
    <td width="10%"><input type='text' class='textbox' style='width: 75px;' id='from' name='from' value='<?php
      echo $today = date("Y-m-d", strtotime("-3 months"));

      ?>'/></td>
    <td>To:</td><td width="20%"><input type='text' class='textbox' style='width: 75px;' id='to' name='to' value='<?php echo $today = date("Y-m-d"); ?>'/></td></tr>
    <tr><td width="10%">Action:</td><td width="15%"><?php
        $actionSelect = $util->build_select_options($rs['journalAction'], 'filterAction', null, false, null, true, 'filterAction[]', 7);
        echo $actionSelect;

        ?></td><td width="5%">Entity:</td>
    <td width="10%"><?php
      $entry_typeSelect = $util->build_select_options($rs['journalEntry_type'], 'filterEntry_type', null, false, null, true, 'filterEntry_type[]', 7);
      echo $entry_typeSelect;

      ?></td><td width="20%"></td></tr>
    <tr><td width="10%">Logins:</td><td width="15%"><?php
        $loginSelect = $util->build_select_options($rs['logins'], 'filterLogin', null, false, null, true, 'filterLogin[]', 7);
        echo $loginSelect;

        ?></td>
    <td width="5%">Customer:</td>
    <td width="10%"><select multiple size='7' name="customer_id[]">
    <option value="" ></option>
    <?php
    foreach ($rs['customerNameList'] as $key => $value) {
      echo "<option value='" . $rs['customerIdList'][$key] . "'>" . $value . "</option>";
    }

    ?></select></td><td width="20%"></td></tr>
    <tr>
      <?php
      $repTools->spacing_tr(true);
      $repTools->outPutColspanLine(submit_button('Get Spreadsheet') . submit_button('Get Raw Data') . hidden_fn('reportOnJournalFileInformation'));
    $repTools->closeForm(true);
  }
      ?>
        </table>
    </tr>
    </table>
    </td>
    <?php
}

    function startProvisioningHeaders($columns, $array, $err, $checkFunction, $tableLabel, $displayShowHideDecommissioned = true) {
  $nbColumns = count($columns);
  echo "<td>";
  if ($displayShowHideDecommissioned) {
    echo "<input type='button' name='' class='button' value='Show/Hide Decommissioned' onclick='hideDecommisionned();'>";
  }
  echo "<form name='manageSchedule' method='post' action='license_mgr_update.php' onsubmit='return $checkFunction();'>
    <table width='100%' border='0' cellspacing='0' cellpadding='1' align='center'>
      <tr>
        <td class='tableBorder'>
          <table id='records' width='100%' class='sort' border='0' cellspacing='1' cellpadding='0'>
            <tr>
              <td colspan='$nbColumns' class='tableTitle'>&#8250; $tableLabel</td>
            </tr>
            <tr class='rowHeaders'>";
  foreach ($columns as $columnName => $columnWidth) {
    echo "<td width='$columnWidth%'>$columnName</td>";
  }
  echo "</tr>";
  if (empty($array)) {
    echo "<tr class='cellColor0'><td colspan='$nbColumns' style='text-align: center;'>$err</td></tr>";
  }
}

function closeProvisioningTable() {
  echo "</table>
      </td>
    </tr>
  </table>
  <br />";
}

function startProvisioningRecordForm($formName, $checkFunction, $edit) {
  $onSubmit = $edit || empty($checkFunction) ? "" : "onsubmit='return $checkFunction();'";
  echo "<form name='$formName' id='$formName' method='post' action='license_mgr_update.php' $onSubmit>";
}

function startProvisioningRecordTable() {
  echo "<table width='100%' border='0' cellspacing='0' cellpadding='1' align='center'>
    <tr>
      <td class='tableBorder'>
        <table id='record' width='100%' border='0' class='sort leftAlign' cellspacing='1' cellpadding='0'>";
    }

function writeProvisioningRecordLine($label, $cellContent) {
  echo "<tr>
    <td width='200' class='formNames'>$label</td>
    <td class='cellColor'>$cellContent</td>
  </tr>";
}

    function getBulkChangesHint($action) {
      return "<span class='showInBulkChanges' style='display:none'>! Bulk changes only supports {$action} action</span>";
    }

    function getDefaultFieldValue($rs, $fieldName, $defaultValue = "") {
  return isset($rs[$fieldName]) ? $rs[$fieldName] : $defaultValue;
}

function printBulkChangesBtn($screen) {
      if (!empty($_SESSION["canBulkChanges"])) {
        $rp = new FormSelectors("button");
        echo $rp->button("btn_bulk_changes", "Bulk Changes", "btn_bulk_changes", "showBulkChanges(\"$screen\");", false);
        echo $rp->button("btn_save_bulk_changes", "Save", "btn_save_bulk_changes", "saveBulkChanges();", true, false);
        echo $rp->button("btn_cancel_bulk_changes", "Cancel", "btn_cancel_bulk_changes", "cancelBulkChanges();", true, false);
      }
    }

function closeProvisioningRecordTable() {
  echo "</table>
      </td>
    </tr>
  </table>
  <br/>
  <div id='buttons'>";
    }

function closeProvisioningRecordForm() {
  echo "</div>"
      . "</form>\n";
    }
    /**
  ####  #    #  ####  #####  ####  #    # ###### #####   ####
  #    # #    # #        #   #    # ##  ## #      #    # #
  #      #    #  ####    #   #    # # ## # #####  #    #  ####
  #      #    #      #   #   #    # #    # #      #####       #
  #    # #    # #    #   #   #    # #    # #      #   #  #    #
  ####   ####   ####    #    ####  #    # ###### #    #  ####
 */

/**
 * build_customers_body()
 *
 * @param mixed $pager
 * @param mixed $customers
 * @param mixed $err
 * @return
 */
function build_customers_body(&$pager, $customers, $err) {
  $link = new Link();
  $bw = new BaseWizard(2);
  echo $bw->outPutJs();
  $util = new Utility();
  $columns = array(
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_id'), 'Customer Id') => 7,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_name'), 'Customer Name') => 31,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_type'), 'Customer<br>Type') => 10,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'reseller_id'), 'Reseller Id') => 7,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'project_manager'), 'PM /<br> Owner') => 10,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'sfdc_status'), 'SFDC Status') => 10,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'sfdc_account_type'), 'SFDC Type') => 10,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_sfdc_only_inv'), 'SFDC Locked') => 10
  );
  if (!empty($_SESSION['canViewCurrentLicenses'])) {
    $columns["Has Licenses"] = 5;
  }
  if (!empty($_SESSION['canManageReleases'])) {
    $columns[$link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'c_reporting_enabled'), 'Use in Reports')] = 10;
  }
  $columns["# System"] = 5;
  $columns["# Cluster"] = 5;
  $columns["# HostId"] = 5;
  if (!empty($_SESSION['canDeleteObjects'])) {
    $columns["Delete"] = 7;
  }
  startProvisioningHeaders($columns, $customers, $err, "checkCustomerForm", "Customer List", false);
  for ($i = 0; is_array($customers) && $i < count($customers); $i++) {
    $cur = $customers[$i];
    echo "<tr class=\"cellColor" . ($i % 2) . "\" align=\"center\" id=\"tr$i\">\n";
    //one of these required to build the link 'canManageLicenseInventory', 'canGenerateLicenseSignature', 'canViewCurrentLicenses', 'canAdministerLicenses'
    if (!empty($_SESSION['canManageLicenseInventory']) || !empty($_SESSION['canGenerateLicenseSignature']) || !empty($_SESSION['canViewCurrentLicenses']) || !empty($_SESSION['canAdministerLicenses'])) {
      $licensingLinks = '<span style="float:right;text-align:right;">(<a href="index.php?operation=licinventory&customer_filter=' . urlencode($cur['customer_id']) . '" target="_blank">Inventory</a> | <a href="index.php?operation=licinventory&customer_filter=' . urlencode($cur['customer_id']) . '&inv_action=cluster_licenses" target="_blank">Licenses</a>)</span>';
    } else {
      $licensingLinks = '';
    }
    if (!empty($_SESSION['canManageEntries'])) {
      echo '<td class="def" name="customer_id" style="text-align:left">' . $link->getLink($_SERVER['PHP_SELF'] . '?' . preg_replace("/&customer_id=[\d\w]*/", "",
          $_SERVER['QUERY_STRING']) . '&amp;customer_id=' . $cur['customer_id'] . ((strpos($_SERVER['QUERY_STRING'], $pager->getLimitVar()) === false) ? '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() : ''),
        $cur['customer_id'], '', '') . "</td>\n";
      echo '<td style="text-align:left">' . $link->getLink($_SERVER['PHP_SELF'] . '?' . preg_replace("/&customer_name=[\d\w]*/", "", $_SERVER['QUERY_STRING']) . '&amp;customer_id=' . $cur['customer_id'] . ((strpos($_SERVER['QUERY_STRING'],
          $pager->getLimitVar()) === false) ? '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() : ''), $cur['customer_name'], '', '') . $licensingLinks . "</td>\n";
    } else {
      echo '<td style="text-align:left">' . $cur['customer_id'] . "</td>\n";
      echo '<td style="text-align:left">' . $cur['customer_name'] . $licensingLinks . "</td>\n";
    }
    echo '<td style="text-align:center">' . $cur['customer_type'] . "</td>\n";
    echo '<td style="text-align:center">' . (($cur['reseller_id'] == '0') ? "" : $cur['reseller_id']) . "</td>\n";
    echo '<td style="text-align:left"><a href="mailto:' . $cur['project_manager_email'] . '">' . (!empty($cur['project_manager']) ? $cur['project_manager'] : "nobody") . "</a> /<br>" . '<a href="mailto:' . $cur['c_account_owner_email'] . '">' . (!empty($cur['c_account_owner']) ? $cur['c_account_owner'] : "nobody") . "</a></td>\n";
    echo '<td style="text-align:center">' . $cur['sfdc_status'] . "</td>\n";
    echo '<td style="text-align:center">' . $cur['sfdc_account_type'] . "</td>\n";
    echo '<td style="text-align:center">' . (empty($cur['customer_sfdc_only_inv']) ? "None" : Time::formatDate($cur['customer_sfdc_only_inv'])) . "</td>\n";
    if (!empty($_SESSION['canViewCurrentLicenses'])) {
      echo '<td style="text-align:center">'
      . ($cur['has_license'] == true ? '<a href=index.php?operation=license&customer_filter=' . urlencode($cur['customer_id']) . '>true</a>' : 'false') . "</td>\n";
    }
    //TODO if permission make it a drop down
    if (!empty($_SESSION['canManageReleases'])) {
      $reportingOutput = $cur['c_reporting_enabled'];
      $form = new FormSelectors('textbox');
      $id = "report_enabled_" . $cur['customer_id'];
      $reportingOutput = $form->setSelectNoEmpty()->setSelectOnchange("toggle_modified(this,\"{$id}\")")->addOptions(array('Yes', 'No'))->setSelectName("c_reporting_enabled")->setSelectId($id)->setSelectDefault($cur['c_reporting_enabled'])->renderSelect();
      echo "<td style=\"text-align:center\">{$reportingOutput}</td>\n";
    }
    //add a simple class on it allowing to trigger on change update color
    echo '<td style="text-align:center">'
    . '<a href=index.php?operation=systems&customer_filter=' . urlencode($cur['customer_id']) . '>' . $cur['system_count'] . "</a>\n"
    . "</td>\n";
    echo '<td style="text-align:center">'
    . '<a href=index.php?operation=asclusters&customer_filter=' . urlencode($cur['customer_id']) . '>' . $cur['cluster_count'] . "</a>\n"
    . "</td>\n";
    echo '<td style="text-align:center">'
    . '<a href=index.php?operation=ashostids&customer_filter=' . urlencode($cur['customer_id']) . '>' . $cur['host_id_count'] . "</a>\n"
    . "</td>\n";
    if (!empty($_SESSION['canReloadPhonehome'])) {
      $button = $bw->insertWizardButton($cur['customer_id']);
      echo "<td><input type=\"checkbox\" name=\"customer_id[]\" value=\"{$cur['customer_id']}\" onclick=\"adminRowClick(this,'tr$i',$i);\"/>{$button}</td>\n";
    }
    echo "</tr>\n";
  }
  closeProvisioningTable();
  if (!empty($_SESSION['canDeleteObjects'])) {
    echo submit_button('Delete', 'customer_id') . hidden_fn('delCustomer');
  }
  $test = new FormSelectors('button');
  if (!empty($_SESSION['canDeleteObjects'])) {
    echo $test->button("saveCustomers", "Apply Changes", "saveCustomers", 'ajaxPost("/php/licenseReporting/ajax/customers/","changed","def")');
  }
  echo "</form>
    </td>";
  $repTool = new ReportingTools();
  $wizardContent = $bw->startPanel(1, 'Paste Report', 'leftAlign', true);
  $wizardContent .= $repTool->form_header("tech_support_wizard", "techSupportWizard", "post", "some_page");
  $wizardContent .= $repTool->outputLabelAndValue("Tech Support File: ", $test->uploadField("uploadTechField", null, array(".txt", ".log"), "return customerAjaxWizard()"),
    true);
  $wizardContent .= $repTool->outputLabelAndValue("Or Paste Report Content: ", $repTool->getTextArea("uploadTechTextArea", "uploadTechTextArea", 80, 15), true);

//show customer selected
//add select system, cluster
  $wizardContent .= $repTool->outputLabelAndValue("Validate: ", $test->button("PushTechReport", "Check Report Content", "PushTechReport", "return customerAjaxWizard()"), true);
  $wizardContent .= $repTool->outputLabelAndValue("Report Date: ", "<input type='text' id='datepicker'>", true);
  $wizardContent .= "</form>";
  $wizardContent .= $repTool->outputLabelAndValue("Analysis: ", $repTool->getTextArea("analysisTx", "analysisTx", 80, 5, null, null, null, true), true);
  $wizardContent .= $bw->closePanel(1);
  $wizardContent .= $bw->startPanel(2, 'Insert Report in LRS', null, true);
  $test->resetSelectData();
  $wizardContent .= hidden_fn("", "wiz_customer_id", "wiz_customer_id");
  $systemSelect = $test->setSelectId('wiz_system_uid')->setSelectName('wiz_system_uid')->allowEmptySelect()->renderSelect();
  $clusterSelect = $test->setSelectId('wiz_as_cluster_uid')->setSelectName('wiz_as_cluster_uid')->allowEmptySelect()->renderSelect();
  $wizardContent .= $repTool->outputLabelAndValue("System: ", $systemSelect, true);
  $wizardContent .= $repTool->outputLabelAndValue("Cluster: ", $clusterSelect, true);
  $test->resetSelectData('button');
  $insertBtn = $test->button("insert_tech_support", "Insert Report", "insert_tech_support", 'return insertTechSupportAjaxWizard()');
  $wizardContent .= $repTool->outputLabelAndValue("Output: ", $repTool->getTextArea("insertReportArea", "insertReportArea", 80, 10, null, null, null, true), true);
  $wizardContent .= $repTool->outputLabelAndValue("", $insertBtn, true);
  $wizardContent .= $bw->closePanel(2, null, 'divClose');
  $bw->insertInWizardDiv($wizardContent);
}

/**
 * build_customers_editSearch()
 *
 * @param mixed $rs
 * @param mixed $edit
 * @param mixed $pager
 * @return
 */
function build_customers_editSearch($rs, $edit, $autoFocus, &$pager) {
  $autoFocus = $autoFocus ? " autofocus" : "";
  startProvisioningRecordForm("addCustomer", "checkAddCustomer", $edit);
    startProvisioningRecordTable();
      $customerIdDisabled = $edit ? "disabled='disabled'" : "";
      writeProvisioningRecordLine("Customer ID",
        "<input type='text' name='customer_id' class='textbox' maxLength='20' $customerIdDisabled value='" . getDefaultFieldValue($rs, "customer_id") . "' />");
      writeProvisioningRecordLine("Customer Name",
        "<input type='text' name='customer_name' class='textbox' maxLength='80' $autoFocus value='" . getDefaultFieldValue($rs, "customer_name") . "' />");
      if (!empty($_SESSION['canManageEntries'])) {
        $customerTypeContent = "<select name='customer_type' class='textbox'>";
        foreach ($rs["customerTypeList"] as $customerType) {
          $customerTypeContent .= "<option value='$customerType'" . (getDefaultFieldValue($rs, "customer_type") == $customerType ? "selected='selected'" : "") . ">$customerType</option>\n";
        }
        $customerTypeContent .= "</select>";
        writeProvisioningRecordLine("Customer Type", $customerTypeContent);
      }
      $resellerContent = "<select name='reseller_id' class='textbox'><option value='' selected='selected'></option>\n";
      foreach ($rs["customerIdList"] as $customerId) {
        $resellerContent .= "<option value='$customerId'" . (getDefaultFieldValue($rs, "reseller_id") == $customerId ? "selected='selected'" : "") . ">$customerId</option>\n";
      }
      $resellerContent .= "</select> (list limited to Customers of type=Reseller)";
      writeProvisioningRecordLine("Reseller ID", $resellerContent);
      $returnDate = getDefaultFieldValue($rs, "customer_sfdc_only_inv", "None");
      $init_date = getDefaultFieldValue($rs, "customer_sfdc_only_inv", null);
      if ($returnDate != "None") {
        $returnDate = Time::formatDate($returnDate);
      }
      $inventoryLockedContent = "";
      if (!empty($_SESSION["canLockSFDC"])) {
        $inventoryLockedContent = "<input type='text' class='textbox' readonly='readonly' id='hdn_return_date' name='return_date' value='$returnDate'/>
  <input type='button' name='clearDate' value='Clear Date' class='button' onClick='clearReturnDate()'/>";
      } else {
        $inventoryLockedContent = $returnDate;
      }
      writeProvisioningRecordLine("SFDC Inventory Locked", $inventoryLockedContent);
      writeProvisioningRecordLine("Account Owner", "<em>" . getDefaultFieldValue($rs, "c_account_owner") . "</em>");
      writeProvisioningRecordLine("Account Owner Email", "<em>" . getDefaultFieldValue($rs, "c_account_owner_email") . "</em>");
      writeProvisioningRecordLine("Project Manager", "<em>" . getDefaultFieldValue($rs, "project_manager") . "</em>");
      writeProvisioningRecordLine("Project Manager Email", "<em>" . getDefaultFieldValue($rs, "project_manager_email") . "</em>");
      writeProvisioningRecordLine("SFDC Status", "<em>" . getDefaultFieldValue($rs, "sfdc_status") . "</em>");
      writeProvisioningRecordLine("SFDC Account Type", "<em>" . getDefaultFieldValue($rs, "sfdc_account_type") . "</em>");
      closeProvisioningRecordTable();
      // Print out correct buttons
      if (!$edit) {
        echo!empty($_SESSION['canManageEntries']) ? submit_button('Add Customer', 'customer_id') : "";
        echo submit_button('Search', 'customer_id') . hidden_fn('addCustomer');
        echo!empty($_SESSION['canManageEntries']) ? ' <input type="reset" name="reset" value="' . 'Clear' . '" class="button" />' . "\n" : "";
      } else {
        echo submit_button('Save Customer Info', 'customer_id') . cancel_button($pager) . hidden_fn('editCustomer')
        . '<input type="hidden" name="customer_id" value="' . $rs['customer_id'] . '" />' . "\n";
      }
      // Unset variables
      closeProvisioningRecordForm();
      unset($rs);
      if (!empty($_SESSION['canLockSFDC']) && !empty($_SESSION['canManageEntries'])) {
        print_jscalendar_return_date($init_date);
      }
    }
/**
  ####  #   #  ####  ##### ###### #    #  ####
  #       # #  #        #   #      ##  ## #
  ####    #    ####    #   #####  # ## #  ####
  #   #        #   #   #      #    #      #
  #    #   #   #    #   #   #      #    # #    #
  ####    #    ####    #   ###### #    #  ####
 */

/**
 * build_systems_body()
 *
 * @param mixed $pager
 * @param mixed $systems
 * @param mixed $err
 * @return
 */
function build_systems_body(&$pager, $systems, $err) {
  $link = new Link();
  $util = new Utility();
  $currentKeyName = 'system_uid';
  $columns = array(
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_name'), 'Customer Name') => 30,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'system_uid'), 'System Id') => 5,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'system_name'), 'System Name') => 20,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'system_type'), 'System Type') => 10,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'validated'), 'Validated') => 10,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'system_decommissioned'), 'Decommissioned') => 10
  );
  if (!empty($_SESSION['canViewCurrentLicenses'])) {
    $columns["Has Licenses"] = 5;
  }
  $columns["# Clusters"] = 5;
  $columns["# HostId"] = 5;
  if (!empty($_SESSION['canManageEntries'])) {
    $columns["Edit"] = 5;
  }
  if (!empty($_SESSION['canBulkChanges'])) {
    $columns["Bulk"] = 5;
  }
  startProvisioningHeaders($columns, $systems, $err, "checkSystemForm", "System List");
  for ($i = 0; is_array($systems) && $i < count($systems); $i++) {
    $cur = $systems[$i];
    $currentKeyValue = $cur['system_uid'];

    echo "<tr class=\"cellColor" . ($i % 2) . "\" align=\"center\" id=\"tr$i\">\n"
    . '<td style="text-align:left">' . $cur['customer_name'] . "</td>\n";
    //one of these required to build the link 'canManageLicenseInventory', 'canGenerateLicenseSignature', 'canViewCurrentLicenses', 'canAdministerLicenses'
    if (!empty($_SESSION['canManageLicenseInventory']) || !empty($_SESSION['canGenerateLicenseSignature']) || !empty($_SESSION['canViewCurrentLicenses']) || !empty($_SESSION['canAdministerLicenses'])) {
      $licensingLinks = '<span style="float:right;text-align:right;">(<a href="index.php?operation=licinventory&customer_filter=' . urlencode($cur['customer_id']) . '" target="_blank">Inventory</a> | <a href="index.php?operation=licinventory&customer_filter=' . urlencode($cur['customer_id']) . '&inv_action=cluster_licenses&system_name=' . urlencode($cur['system_name']) . '" target="_blank">Licenses</a>)</span>';
    } else {
      $licensingLinks = '';
    }
    echo '<td style="text-align:left">' . $cur['system_uid'] . "</td>\n";
    echo '<td style="text-align:left">' . $cur['system_name'] . $licensingLinks . "</td>\n";
    echo '<td style="text-align:left">' . $cur['system_type'] . "</td>\n";
    echo '<td style="text-align:center"><input type="checkbox" disabled ' . ($cur['system_validated'] ? 'checked' : '') . " /></td>\n";
    echo '<td style="text-align:center"><input type="checkbox" disabled ' . ($cur['system_decommissioned'] ? 'checked' : '') . " /></td>\n";

    if (!empty($_SESSION['canViewCurrentLicenses'])) {
      echo '<td style="text-align:center">'
      . ($cur['has_license'] == true ? '<a href=index.php?operation=license&customer_filter=' . urlencode($cur['customer_id']) . '&system_filter=' . urlencode($cur['system_name']) . '>true</a>' : 'false') . "</td>\n";
    }

    echo '<td style="text-align:center">'
    . '<a href=index.php?operation=asclusters&customer_filter=' . urlencode($cur['customer_id']) . '&system_filter=' . urlencode($cur['system_name']) . '>' . $cur['cluster_count'] . "</a>\n"
    . "</td>\n";
    echo '<td style="text-align:center">'
    . '<a href=index.php?operation=ashostids&customer_filter=' . urlencode($cur['customer_id']) . '&system_filter=' . urlencode($cur['system_name']) . '>' . $cur['host_id_count'] . "</a>\n"
    . "</td>\n";

    if (!empty($_SESSION['canManageEntries'])) {
      echo '<td>' . $link->getLink($_SERVER['PHP_SELF'] . '?' . preg_replace("/&" . $currentKeyName . "=[\d\w]*/", "", $_SERVER['QUERY_STRING']) . '&amp;' . $currentKeyName . '=' . $currentKeyValue . ((strpos($_SERVER['QUERY_STRING'],
          $pager->getLimitVar()) === false) ? '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() : ''), 'Edit', '', '', 'Edit data for' . $currentKeyValue) . "</td>\n";
    }
    if (!empty($_SESSION['canBulkChanges']) || !empty($_SESSION['canDeleteObjects'])) {
      echo "<td><input type=\"checkbox\" name=\"" . $currentKeyName . "[]\" value=\"" . $currentKeyValue . "\" onclick=\"adminRowClick(this,'tr$i',$i);\"/></td>\n";
    }
    echo "</tr>\n";
  }
  closeProvisioningTable();
  if (!empty($_SESSION['canDeleteObjects'])) {
    echo submit_button('Delete', $currentKeyName) . hidden_fn('delSystem');
  }
  echo "</form>
    </td>";
}

/**
 * build_systems_edit()
 *
 * @param mixed $rs
 * @param mixed $edit
 * @param mixed $pager
 * @return
 */
function build_systems_edit($rs, $edit, $autoFocus, &$pager) {
  $return_date = "None";
      $init_date = null;
      if (!empty($rs['decommissioned_date']) && $rs['decommissioned_date'] !== 0) {
        $init_date = $rs['decommissioned_date'];
        $return_date = $rs['decommissioned_date'];
      }
      $autoFocus = $autoFocus ? " autofocus" : "";
      startProvisioningRecordForm("addSystem", "checkAddSystem", $edit);
      if ($edit) {
        echo '<input type="hidden" name="customer_id_hidden" value="' . $rs['customer_id'] . '">' . "\n";
      }
      startProvisioningRecordTable();
      $systemDisabled = $edit ? "disabled='disabled'" : "";
      writeProvisioningRecordLine("System Id",
        "<input type='text' name='system_uid' class='textbox' maxLength='80' $systemDisabled value='" . getDefaultFieldValue($rs, "system_uid") . "' />");
      writeProvisioningRecordLine("System Name",
        "<input type='text' name='system_name' class='textbox' maxLength='80' $autoFocus value='" . getDefaultFieldValue($rs, "system_name") . "' />");
      $customerNameContent = "<select name='customer_id' class='textbox' $systemDisabled>";
      $customerNameList = $rs['customerNameList'];
      $customerIdList = $rs['customerIdList'];
      for ($i = 0; $i < count($customerNameList); $i++) {
        $customerNameContent .= "<option value='$customerIdList[$i]'" . (getDefaultFieldValue($rs, "customer_id") == $customerIdList[$i] ? "selected='selected'" : "") . ">$customerNameList[$i]</option>\n";
      }
      $customerNameContent .= "</select>";
      writeProvisioningRecordLine("Customer Name", $customerNameContent);
      $systemTypeContent = "<select name='system_type' class='textbox'>";
      foreach ($rs["systemTypes"] as $systemType) {
        $systemTypeContent .= "<option value='$systemType'" . (getDefaultFieldValue($rs, "system_type") == $systemType ? "selected='selected'" : "") . ">$systemType</option>\n";
      }
      $systemTypeContent .= "</select>";
      writeProvisioningRecordLine("System Type", $systemTypeContent);
      writeProvisioningRecordLine("Validated?",
        "<input type='checkbox' name='system_validated' " . (isset($rs["system_validated"]) && $rs["system_validated"] ? " checked" : "") . "/> " . getBulkChangesHint("validating"));
      if (!empty($_SESSION['canManageEntries'])) {
        writeProvisioningRecordLine("Decommissioned?",
          "<input type='checkbox' id='decommissioned_checkbox' name='decomissioned' " . (isset($rs["system_decommissioned"]) && $rs["system_decommissioned"] ? " checked" : "") . "/> " . getBulkChangesHint("decommissioning"));
        $return_date = $return_date == "None" ? $return_date : Time::formatDate($return_date);
        writeProvisioningRecordLine("Stop Reporting Date",
          "<input type='text' class='textbox' readonly='readonly' id='hdn_return_date' name='return_date' value='$return_date'/>
        <input type='button' name='clearDate' value='Clear Date' class='button' onClick='clearReturnDate();'/>");
      }
      closeProvisioningRecordTable();
      // Print out correct buttons
      if (!$edit) {
        echo submit_button('Add System', 'system_name') . hidden_fn('addSystem');
        echo submit_button('Search', 'system_uid');
        echo ' <input type="reset" name="reset" value="' . 'Clear' . '" class="button" />' . "\n";
        printBulkChangesBtn("system");
      } else {
        echo submit_button('Edit System Info', 'system_uid') . cancel_button($pager) . hidden_fn('editSystem')
        . '<input type="hidden" name="system_uid" value="' . $rs['system_uid'] . '" />' . "\n";
        // Unset variables
      }
      closeProvisioningRecordForm();
      unset($rs);

      print_jscalendar_return_date($init_date);
    }
/**
  ####  #      #    #  ####  ##### ###### #####   ####
  #    # #      #    # #        #   #      #    # #
  #      #      #    #  ####    #   #####  #    #  ####
  #      #      #    #      #   #   #      #####       #
  #    # #      #    # #    #   #   #      #   #  #    #
  ####  ######  ####   ####    #   ###### #    #  ####
 */

/**
 * build_as_clusters_body()
 *
 * @param mixed $pager
 * @param mixed $clusters
 * @param mixed $err
 * @return
 */
function build_as_clusters_body(&$pager, $clusters, $err) {
  $link = new Link();
  $util = new Utility();
  $currentKeyName = 'as_cluster_uid';
  $columns = array(
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_name'), 'Customer Name') => 20,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'system_name'), 'System Name') => 15,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'as_cluster_uid'), 'Cluster Id') => 4,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'as_cluster_name'), 'Cluster Name') => 15,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'server_type'), 'Cluster Type') => 4,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'release'), 'Current Release') => 5,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'outdated_license'), 'Outdated License') => 7,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'certificate_of_removal'), 'Certificate of Removal') => 7,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'status'), 'Status') => 8,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'ac_region'), 'Region') => 8,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'ac_user_type'), 'User Type') => 4,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'decommissioned_date'), 'Stop Report Date') => 8
  );
  if (!empty($_SESSION['canViewCurrentLicenses'])) {
    $columns["Has License"] = 5;
  }
  $columns["# HostId"] = 5;
  $columns["# Lic Nodes"] = 5;
  if (!empty($_SESSION['canManageEntries'])) {
    $columns["Edit"] = 5;
  }
  if (!empty($_SESSION['canBulkChanges'])) {
    $columns["Bulk"] = 5;
  }
  startProvisioningHeaders($columns, $clusters, $err, "checkASClusterForm", "Cluster List");
  for ($i = 0; is_array($clusters) && $i < count($clusters); $i++) {
    $cur = $clusters[$i];
    $currentKeyValue = $cur[$currentKeyName];
    echo "<tr class=\"cellColor" . ($i % 2) . "\" align=\"center\" id=\"tr$i\">\n"
    . '<td style="text-align:left">' . $cur['customer_name'] . "</td>\n";
    echo '<td style="text-align:left"><a href=index.php?operation=systems&customer_filter=' . urlencode($cur['customer_id']) . '>' . $cur['system_name'] . "</a></td>\n";
    //one of these required to build the link 'canManageLicenseInventory', 'canGenerateLicenseSignature', 'canViewCurrentLicenses', 'canAdministerLicenses'
    if (!empty($_SESSION['canManageLicenseInventory']) || !empty($_SESSION['canGenerateLicenseSignature']) || !empty($_SESSION['canViewCurrentLicenses']) || !empty($_SESSION['canAdministerLicenses'])) {
      $licensingLinks = '<span style="float:right;text-align:right;">(<a href="index.php?operation=licinventory&customer_filter=' . urlencode($cur['customer_id']) . '" target="_blank">Inventory</a> | <a href="index.php?operation=licinventory&customer_filter=' . urlencode($cur['customer_id']) . '&inv_action=cluster_licenses&system_name=' . urlencode($cur['system_name']) . '&as_cluster_uid=' . urlencode($cur['as_cluster_uid']) . '" target="_blank">Licenses</a>)</span>';
    } else {
      $licensingLinks = '';
    }
    echo '<td style="text-align:left">' . $cur['as_cluster_uid'] . "</td>\n";
    echo '<td style="text-align:left">' . $cur['as_cluster_name'] . $licensingLinks . "</td>\n";
    echo '<td style="text-align:center">' . $cur['server_type'] . "</td>\n";
    echo '<td style="text-align:center">' . $cur['software_release'] . "</td>\n";
    if ($cur['outdated_license'] == 'true') {
      echo '<td style="text-align:center;color:red;">' . $cur['outdated_license'] . "</td>\n";
    } else {
      echo '<td style="text-align:center">' . "</td>\n";
    }
    if ($cur['certificate_of_removal'] == 'true') {
      echo '<td style="text-align:center;color:red;">' . $cur['certificate_of_removal'] . "</td>\n";
    } else {
      echo '<td style="text-align:center">' . "</td>\n";
    }
    echo '<td style="text-align:center">' . $cur['status'] . "</td>\n";
    echo '<td style="text-align:center">' . $cur['ac_region'] . "</td>\n";
    if (in_array($cur['server_type'], array('as', 'ps-hss', 'psa'))) {
      echo '<td style="text-align:center"><select><option>' . $cur['ac_user_type'] . "</option></select></td>\n";
    } else {
      echo '<td style="text-align:center">' . "</td>\n";
    }
    echo '<td style="text-align:center">' . ((!isset($cur['decommissioned_date']) || $cur['decommissioned_date'] == NULL) ? 'None' : Time::formatDate($cur['decommissioned_date'])) . "</td>\n";
    if (!empty($_SESSION['canViewCurrentLicenses'])) {
      echo '<td style="text-align:center">'
      . ($cur['has_license'] == true ? '<a href=index.php?operation=license&customer_filter=' . urlencode($cur['customer_id']) . '&system_filter=' . urlencode($cur['system_name']) . '&cluster_filter=' . urlencode($cur['as_cluster_name']) . '>true</a>' : 'false') . "</td>\n";
    }
    echo '<td style="text-align:center">'
    . '<a href=index.php?operation=ashostids&customer_filter=' . urlencode($cur['customer_id']) . '&system_filter=' . urlencode($cur['system_name']) . '&cluster_filter=' . urlencode($cur['as_cluster_name']) . '>' . $cur['host_id_count'] . "</a>\n"
    . "</td>\n";
    echo '<td style="text-align:center">' . $cur['node_per_clusters'] . "</td>\n";
    if (!empty($_SESSION['canManageEntries'])) {
      echo '<td>' . $link->getLink($_SERVER['PHP_SELF'] . '?' . preg_replace("/&" . $currentKeyName . "=[\d\w]*/", "", $_SERVER['QUERY_STRING']) . '&amp;' . $currentKeyName . '=' . $currentKeyValue . ((strpos($_SERVER['QUERY_STRING'],
          $pager->getLimitVar()) === false) ? '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() : ''), 'Edit', '', '', 'Edit data for' . $currentKeyValue) . "</td>\n";
    }
    if (!empty($_SESSION['canBulkChanges']) || !empty($_SESSION['canDeleteObjects'])) {
      echo "<td><input type=\"checkbox\" name=\"" . $currentKeyName . "[]\" value=\"" . $currentKeyValue . "\" onclick=\"adminRowClick(this,'tr$i',$i);\"/></td>\n";
    }
    echo "</tr>\n";
  }
  closeProvisioningTable();
  if (!empty($_SESSION['canDeleteObjects'])) {
    echo submit_button('Delete', $currentKeyName) . hidden_fn('delASCluster');
  }
  echo "</form>
    </td>";
}

/**
 * build_as_clusters_edit()
 *
 * @param mixed $rs
 * @param mixed $edit
 * @param mixed $pager
 * @return
 */
function build_as_clusters_edit($rs, $edit, $regionValues, $autoFocus, &$pager) {
      $currentKeyName = 'as_cluster_uid';
      $serverTypes = $rs['cluster_type_list'];
      $autoFocus = $autoFocus ? " autofocus" : "";

      $return_date = "None";
      $init_date = null;
      if (!empty($rs['decommissioned_date']) && $rs['decommissioned_date'] !== 0) {
        $init_date = $rs['decommissioned_date'];
        $return_date = $rs['decommissioned_date'];
      }
      startProvisioningRecordForm("addElementDynamicSystemList", "checkAddASCluster", $edit);
      if ($edit) {
        echo '<input type="hidden" name="customer_id_hidden" value="' . $rs['customer_id'] . '">' . "\n";
        echo '<input type="hidden" name="system_name_hidden" value="' . $rs['system_name'] . '">' . "\n";
      }
      startProvisioningRecordTable();
      $return_date = $return_date == "None" ? $return_date : Time::formatDate($return_date);
      writeProvisioningRecordLine("Stop Reporting Date",
        "<input type='text' class='textbox' readonly='readonly' id='hdn_return_date' name='return_date' value='$return_date'/>
        <input type='button' name='clearDate' value='Clear Date' class='button' onClick='clearReturnDate();'/>");
      $clusterDisabled = $edit ? "disabled='disabled'" : "";
      writeProvisioningRecordLine("Cluster Id",
        "<input type='text' name='as_cluster_uid' class='textbox' maxLength='80' $clusterDisabled value='" . getDefaultFieldValue($rs, "as_cluster_uid") . "' />");
      writeProvisioningRecordLine("Cluster Name",
        "<input type='text' name='as_cluster_name' class='textbox' maxLength='20' $autoFocus value='" . getDefaultFieldValue($rs, "as_cluster_name") . "' />");
      $defaultReleaseValue = getDefaultFieldValue($rs, "software_release", " ");
      $softwareReleaseContent = "";
      // status not set for new cluster, version change only if status is not started or in progress
      if (!isset($rs['status']) || $rs['status'] == 'Not Started' || $rs['status'] == 'In Progresss') {
        $softwareReleaseContent = "<select name='bwVersion' class='textbox'>";
        foreach ($rs['relArr'] as $relArr) {
          $softwareReleaseContent .= "<option value='$relArr'" . ($relArr == $defaultReleaseValue ? "selected='selected'" : "") . ">$relArr</option>\n";
        }
        $softwareReleaseContent .= "</select>";
      } else {
        $softwareReleaseContent = $defaultReleaseValue;
      }
      writeProvisioningRecordLine("Software Release", $softwareReleaseContent);

      ?>
    <tr>
      <td width="200" class="formNames">Number of Licensing Nodes in the Cluster</td>
  <td class="cellColor">
  <input type="text" name="node_per_clusters" maxLength="20" onKeyPress="return numbersonly(this, event, false, 0, <?php echo $rs['max_lic_node_count'] ?>);" class="textbox" value="<?php echo isset($rs['node_per_clusters']) && $rs['node_per_clusters'] != "" ? $rs['node_per_clusters'] : $rs['max_lic_node_count'] ?>" />Max(<?php echo $rs['max_lic_node_count'] ?>)
  </td>
  </tr>
  <tr>
  <td width="200" class="formNames">Cluster Type</td>
  <td class="cellColor">
    <select name="server_type">
    <?php
    $currServerType = $serverTypes[0];
    if (isset($rs['server_type'])) {
      $currServerType = $rs['server_type'];
    }
    for ($i = 0; $i < count($serverTypes); $i++) {
      echo '<option value="' . $serverTypes[$i] . '"' . (($currServerType == $serverTypes[$i]) ? (' selected="selected"') : '') . '>' . $serverTypes[$i] . '</option>' . "\n";
    }

    ?>
  </select>
  </td>
  </tr>
  <tr>
  <td width="200" class="formNames">Customer Name</td>
  <td class="cellColor">
    <select name="customer_id" onchange="updateClusterListSection(false, 'customer_id', null, null)" <?php echo $clusterDisabled ?>>
    <?php
    $customerNameList = $rs['customerNameList'];
    $customerIdList = $rs['customerIdList'];
    for ($i = 0; $i < count($customerNameList); $i++) {
      echo '<option value="' . $customerIdList[$i] . '"' . ((isset($rs['customer_id']) && ($rs['customer_id'] == $customerIdList[$i])) ? (' selected="selected"') : '') . '>' . $customerNameList[$i] . '</option>' . "\n";
    }

    ?>
  </select>
  </td>
  </tr>
  <tr>
  <td width="200" class="formNames">System Name</td>
  <td class="cellColor">
  <select name="system_name">
  <option value="default">default</option>
  </select>
  </td>
  </tr>
  <tr>
    <td width="200" class="formNames">Region</td>
    <td class="cellColor">
      <select name="ac_region">
      <?php
      echo '<option value=""></option>' . "\n";
        for ($i = 0; $i < count($regionValues); $i++) {
        echo '<option value="' . $regionValues[$i] . '"' . ((isset($rs['ac_region']) && ($rs['ac_region'] == $regionValues[$i])) ? (' selected="selected"') : '') . '>' . $regionValues[$i] . '</option>' . "\n";
      }

        ?>
    </select>
    </td>
  </tr>
  <tr>
  <td width="200" class="formNames">Certificate of Removal</td>
  <td class="cellColor">
  <select name="certificate_of_removal">
    <?php
    if (!empty($rs['certificate_of_removal']) && $rs['certificate_of_removal'] == 'true') {
      echo '<option value="true" selected="selected">true</option>' . "\n";
      echo '<option value="false" >false</option>' . "\n";
    } else {
      echo '<option value="true">true</option>' . "\n";
      echo '<option value="false" selected="selected">false</option>' . "\n";
    }

    ?>
  </select>
  </td>
  </tr>
    <tr>
    <td width="200" class="formNames">Description</td>
    <td class="cellColor">
      <textarea rows="4" cols="50" name="as_cluster_description"><?php echo!empty($rs['as_cluster_description']) ? $rs['as_cluster_description'] : ""; ?></textarea>
      </td>
    </tr>
    <?php if (!empty($_SESSION['canManageEntries'])) { ?>
      <tr>
    <td width="200" class="formNames">Decommissioned?</td>
        <td class="cellColor"><input type="checkbox" id="decommissioned_checkbox" name="decomissioned" <?php echo isset($rs['status']) && $rs['status'] === "Decommissioned" ? ' checked disabled' : '' ?> /> <?php echo getBulkChangesHint("decommissioning") ?></td>
        </tr>
      <?php
      }
      closeProvisioningRecordTable();
      add_cluster_load_script(false, (isset($rs['system_name']) ? $rs['system_name'] : null), null);
      // Print out correct buttons
  if (!$edit) {
    echo submit_button('Add Cluster', 'as_cluster_name') . hidden_fn('addASCluster');
    echo submit_button('Search', 'as_cluster_uid');
    echo ' <input type="reset" name="reset" value="' . 'Clear' . '" class="button" />' . "\n";
    printBulkChangesBtn("as_cluster");
  } else {
    echo submit_button('Edit Cluster Info', 'system_uid') . cancel_button($pager) . hidden_fn('editASCluster')
    . '<input type="hidden" name="' . $currentKeyName . '" value="' . $rs[$currentKeyName] . '" />' . "\n";
    echo '<input type="hidden" name="old_system" value="' . $rs['system_name'] . '" />' . "\n";
    if (!empty($_SESSION['canGenerateGoldenLicense'])) {
      echo '<input type="button" name="generate Golden License" value="Generate Golden License" class="button" onclick="javascript: generateGoldenLicense(\'' . $rs['as_cluster_uid'] . '\');" /> ';
    }
    // Unset variables
  }
  closeProvisioningRecordForm();
    unset($rs);

  // Set up the javascript calendars
  print_jscalendar_return_date($init_date);
}
/**
  #    #  ####   ####  ##### # #####   ####
  #    # #    # #        #   # #    # #
  ###### #    #  ####    #   # #    #  ####
  #    # #    #      #   #   # #    #      #
  #    # #    # #    #   #   # #    # #    #
  #    #  ####   ####    #   # #####   ####
 */

/**
 * build_as_host_id_body()
 *
 * @param mixed $pager
 * @param mixed $systems
 * @param mixed $err
 * @return
 */
function build_as_host_id_body(&$pager, $hostIds, $err) {
  $link = new Link();
  $util = new Utility();
  $currentKeyName = 'host_id_uid';
  $columns = array(
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_name'), 'Customer Name') => 25,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'system_name'), 'System Name') => 15,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'as_cluster_name'), 'Cluster Name') => 20,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'host_id'), 'Host ID') => 15,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'decomissioned'), 'Decommissioned') => 15,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'isNodeUUID'), 'NFM Node') => 15,
    $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'description'), 'Description') => 25
  );
  if (!empty($_SESSION['canManageEntries'])) {
    $columns["Edit"] = 5;
  }
  if (!empty($_SESSION['canBulkChanges'])) {
    $columns["Bulk"] = 5;
  }
  startProvisioningHeaders($columns, $hostIds, $err, "checkASHostIdForm", "Hostid List");
  for ($i = 0; is_array($hostIds) && $i < count($hostIds); $i++) {
    $cur = $hostIds[$i];
    $currentKeyValue = $cur[$currentKeyName];
    echo "<tr class=\"cellColor" . ($i % 2) . "\" align=\"center\" id=\"tr$i\">\n"
    . '<td style="text-align:left">' . $cur['customer_name'] . "</td>\n";
    echo '<td style="text-align:left"><a href=index.php?operation=systems&customer_filter=' . urlencode($cur['customer_id']) . '>' . $cur['system_name'] . "</a></td>\n";
    //one of these required to build the link 'canManageLicenseInventory', 'canGenerateLicenseSignature', 'canViewCurrentLicenses', 'canAdministerLicenses'
    if (!empty($_SESSION['canManageLicenseInventory']) || !empty($_SESSION['canGenerateLicenseSignature']) || !empty($_SESSION['canViewCurrentLicenses']) || !empty($_SESSION['canAdministerLicenses'])) {
      $licensingLinks = '<span style="float:right;text-align:right;">(<a href="index.php?operation=licinventory&customer_filter=' . urlencode($cur['customer_id']) . '" target="_blank">Inventory</a> | <a href="index.php?operation=licinventory&customer_filter=' . urlencode($cur['customer_id']) . '&inv_action=cluster_licenses&system_name=' . urlencode($cur['system_name']) . '&as_cluster_uid=' . urlencode($cur['as_cluster_uid']) . '" target="_blank">Licenses</a>)</span>';
    } else {
      $licensingLinks = '';
    }
    echo '<td style="text-align:left"><a href=index.php?operation=asclusters&customer_filter=' . urlencode($cur['customer_id']) . '&system_filter=' . urlencode($cur['system_name']) . '>' . $cur['as_cluster_name'] . '</a>' . $licensingLinks . "</td>\n";
    echo '<td style="text-align:left">' . $cur['host_id'] . "</td>\n";
    echo '<td style="text-align:center"><input type="checkbox" disabled ' . ($cur['decomissioned'] ? 'checked' : '') . " /></td>\n";
    echo '<td style="text-align:center"><input type="checkbox" disabled ' . ($cur['isNodeUUID'] ? 'checked' : '') . " /></td>\n";
    echo '<td style="text-align:left">' . (isset($cur['description']) ? $cur['description'] : "") . "</td>\n";
    if (!empty($_SESSION['canManageEntries'])) {
      echo '<td>' . $link->getLink($_SERVER['PHP_SELF'] . '?' . preg_replace("/&" . $currentKeyName . "=[\d\w]*/", "", $_SERVER['QUERY_STRING']) . '&amp;' . $currentKeyName . '=' . $currentKeyValue . ((strpos($_SERVER['QUERY_STRING'],
          $pager->getLimitVar()) === false) ? '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() : ''), 'Edit', '', '', 'Edit data for' . $currentKeyValue) . "</td>\n";
    }
    if (!empty($_SESSION['canBulkChanges']) || !empty($_SESSION['canDeleteObjects'])) {
      echo "<td><input type=\"checkbox\" name=\"" . $currentKeyName . "[]\" value=\"" . $currentKeyValue . "\" onclick=\"adminRowClick(this,'tr$i',$i);\"/></td>\n";
    }
    echo "</tr>\n";
  }
  closeProvisioningTable();
  if (!empty($_SESSION['canDeleteObjects'])) {
    echo submit_button('Delete', $currentKeyName) . hidden_fn('delASHostId');
  }
  echo "</form>
    </td>";
}

/**
 * build_as_host_id_edit()
 *
 * @param mixed $rs
 * @param mixed $edit
 * @param mixed $pager
 * @return
 */
function build_as_host_id_edit($rs, $edit, $autoFocus, &$pager) {
  $currentKeyName = 'host_id_uid';
  $disable = ($edit ? "disabled" : "");
  $autoFocus = $autoFocus ? " autofocus" : "";
      startProvisioningRecordForm("addElementDynamicSystemList", null, $edit);
    if ($edit) {
        echo '<input type="hidden" name="host_id_uid" value="' . $rs['host_id_uid'] . '">' . "\n";
      echo '<input type="hidden" name="customer_id_hidden" value="' . $rs['customer_id'] . '">' . "\n";
      echo '<input type="hidden" name="system_name_hidden" value="' . $rs['system_name'] . '">' . "\n";
      echo '<input type="hidden" name="as_cluster_name_hidden" value="' . $rs['as_cluster_name'] . '">' . "\n";
      echo '<input type="hidden" name="host_id_hidden" value="' . $rs['host_id'] . '">' . "\n";
      echo '<input type="hidden" name="as_cluster_uid" value="' . $rs['as_cluster_uid'] . '">' . "\n";
    }
      startProvisioningRecordTable();

      ?>
      <?php if (!empty($_SESSION['canManageEntries'])) { ?>
        <tr>
    <td width="200" class="formNames">Customer Id</td>
    <td class="cellColor">
    <select id="hostIdCustomerID"  name="customer_id" onchange="updateClusterListSection(true, 'customer_id', null, null)" <?php echo $disable ?>>
      <?php
      $customerNameList = $rs['customerNameList'];
      $customerIdList = $rs['customerIdList'];
      for ($i = 0; $i < count($customerNameList); $i++) {
        echo '<option value="' . $customerIdList[$i] . '"' . ((isset($rs['customer_id']) && ($rs['customer_id'] == $customerIdList[$i])) ? (' selected="selected"') : '') . '>' . $customerNameList[$i] . '</option>' . "\n";
      }

      ?>
    </select>
    </td>
    </tr>
    <tr>
    <td width="200" class="formNames">System Name</td>
    <td class="cellColor">
    <select id="hostIdSystemName"  name="system_name"  onchange="updateClusterListSection(true, 'system_name', null, null)" >
    <option value="default">default</option>
    </select>
    </td>
    </tr>
    <tr>
    <td width="200" class="formNames">Cluster Name</td>
    <td class="cellColor">
    <select  id="hostIdAsClusterName" name="as_cluster_name"  />
    <option value="default">default</option>
    </select>
    </td>
    </tr>
    <tr>
    <td width="200" class="formNames"><?php echo ($edit ? "NFM Node" : "New licenseId/systemId"); ?></td>
    <td class="cellColor">
        <input type="checkbox" id="nfm_node_check" name="nfm_node_flag"<?php echo (isset($rs['isNodeUUID']) && $rs['isNodeUUID'] && $edit ? ' checked ' : ' ') . $disable ?> />
        </td>
    </tr>
  <?php } ?>
  <tr>
  <td width="200" class="formNames">Host ID</td>
  <td class="cellColor">
    <input type="text" id="host_id_field" name="host_id" class="textbox" size=40 maxLength="120" value="<?php echo isset($rs['host_id']) ? $rs['host_id'] : '' ?>" <?php echo $disable ?> <?php echo $autoFocus ?>/>
    </td>
  </tr>
  <?php if (!empty($_SESSION['canManageEntries'])) { ?>
    <tr>
    <td width="200" class="formNames">Decommissioned?</td>
        <td class="cellColor"><input type="checkbox" id="decommissioned_checkbox" name="decomissioned" <?php echo isset($rs['decomissioned']) && $rs['decomissioned'] ? ' checked ' : '' ?> /> <?php echo getBulkChangesHint("decommissioning") ?></td>
        </tr>
  <?php } ?>
  <tr>
  <td width="200" class="formNames">Description</td>
  <td class="cellColor"><input type="text" name="description" class="textbox" size=25 maxLength="40" value="<?php echo isset($rs['description']) ? $rs['description'] : '' ?>" /></td>
  </tr>
    <?php
    closeProvisioningRecordTable();
    add_cluster_load_script(true, (isset($rs['system_name']) ? $rs['system_name'] : null), (isset($rs['as_cluster_name']) ? $rs['as_cluster_name'] : null)
  );

  ?>
    <script type="text/javascript" src="javascripts/hostids.js"></script>
    <?php
    // Print out correct buttons
  if (!$edit) {
    echo!empty($_SESSION['canManageEntries']) ? submit_button('Add Host ID', 'host_id') . hidden_fn('addASHostId') : "";
    echo submit_button('Search', 'host_id', null, 'Search') . hidden_fn('addASHostIdSearch');
    if (!empty($_SESSION['canManageEntries'])) {
      $rp = new FormSelectors("button");
      echo ' <input type="reset" name="reset" value="' . 'Clear' . '" class="button" /> ';
      printBulkChangesBtn("as_host_id");
      echo $rp->button('genMultipleLicIds', 'Generate Multple License IDs', 'genMultipleLicIds');
      echo "<div id='diagMultiLicenseIDs' title='Enter Count of IDs'>Please Enter a number of License IDs to generate<br>"
      . "<input id='licenseIdsCount' type='text' value=5><br>"
      . "<span id='diagMultiLicenseIDsError' class='red'></span>"
      . "</div>";
    }
  } else {
    echo submit_button('Edit Host ID', 'host_id') . cancel_button($pager) . hidden_fn('editASHostId')
    . '<input type="hidden" name="' . $currentKeyName . '" value="' . $rs[$currentKeyName] . '" />' . "\n";
    // Unset variables
  }
  closeProvisioningRecordForm();
  unset($rs);
}

/**
 * build_unknwon_host_id_edit()
 *
 * @return
 */
function build_unknwon_host_id_edit() {
  ?>
    <form name="unknownHostidEditForm" method="post" action="license_mgr_update.php" onsubmit="return checkUnknownHostId();">
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
  <td class="tableBorder">
  <table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
  <td width="200" class="formNames">Host ID</td>
  <td class="cellColor">
  <input type="text" name="host_id" class="textbox" size=40 maxLength="120"/>
  </td>
  </tr>
  </table>
  </td>
  </tr>
  </table>
  <br />
  <?php
// Print out correct buttons
  echo submit_button('Search', 'host_id') . hidden_fn('unknownHostId')
  . ' <input type="reset" name="reset" value="' . 'Clear' . '" class="button" />' . "\n";
  echo "</form>\n";
}

/**
 * build_unknownhostids_body()
 *
 * @param mixed $pager
 * @param mixed $unknownHostIds
 * @param mixed $err
 * @return
 */
function build_unknownhostids_body(&$pager, $unknownHostIds, $err) {
  $link = new Link();
  $util = new Utility();
  $nbColumns = 7;
  if (empty($_SESSION['canManageEntries'])) {
    $nbColumns -= 1;
  }

  ?>
  <td>
  <form name="manageSchedule" method="post" action="license_mgr_update.php" onsubmit="return checkAdminForm();">
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
  <td class="tableBorder">
  <table width="100%" border="1" cellspacing="1" cellpadding="0">
  <tr>
  <td colspan=<?php echo '"' . $nbColumns . '"' ?> class="tableTitle">&#8250; <?php echo "Reports with Unknown Host IDs List" ?></td>
  </tr>
  <?php
  echo "
    <tr class=\"rowHeaders\">
      <td>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_name'), 'Customer Name') . "</td>
      <td width=\"14%\">" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'host_id1'), 'Host ID') . "</td>
      <td width=\"14%\">" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'host_id2'), 'Host ID') . "</td>
      <td width=\"14%\">" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'host_id3'), 'Host ID') . "</td>
      <td width=\"14%\">" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'host_id4'), 'Host ID') . "</td>
      <td width=\"10%\">" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'report_date'), 'Report Date') . "</td>
      <td width=\"5%\">Add</td>";
  if (!empty($_SESSION['canManageEntries'])) {
    echo '<td width=\"5%\">Delete<br>';
    echo "<input type=\"checkbox\" name=\"unknownHostSelectAll\" onclick=\"adminRowSelectAll(0," . count($unknownHostIds) . ");\"/>";
    echo '</td>';
  }
  echo "</tr>";



  if (!$unknownHostIds)
    echo '<tr class="cellColor0"><td colspan="' . $nbColumns . '" style="text-align: center;">' . $err . '</td></tr>' . "\n";

  for ($i = 0; $i < count($unknownHostIds) && ($unknownHostIds != null); $i++) {
    $cur = $unknownHostIds[$i];
    $temp = $cur['unprocessed_license_report_uid'];
    $url = "show_license_report.php?reportUID=" . $cur['unprocessed_license_report_uid'];
    echo "<tr class=\"cellColor" . ($i % 2) . "\" align=\"center\" id=\"tr$i\">\n"
    . '<td style="text-align:center"><a href="' . $url
    . '" target="_blank" onClick="javascript:openBlankWindow(\'' . $url . '\', \'TEST\');return false;">'
    . (isset($cur['customer_name']) && $cur['customer_name'] != "" ? $cur['customer_name'] : "NULL") . "</a></td>\n";
    echo '<td style="text-align:left">' . $cur['host_id1'] . "</td>\n";
    echo '<td style="text-align:left">' . $cur['host_id2'] . "</td>\n";
    echo '<td style="text-align:left">' . $cur['host_id3'] . "</td>\n";
    echo '<td style="text-align:left">' . $cur['host_id4'] . "</td>\n";
    $mtime = isset($cur['report_date']) ? explode(" ", $cur['report_date']) : array();
    echo '<td style="text-align:left">' . $mtime[0] . "</td>\n";
    $hosts = array(1 => $cur['host_id1'], 2 => $cur['host_id2'], 3 => $cur['host_id3'], 4 => $cur['host_id4']);
    echo '<td style="text-align:center"><a href="#" onclick=\'EditUnknownHostId(' . json_encode($hosts) . ',"' . (isset($cur['customer_name']) && $cur['customer_name'] != "" ? $cur['customer_name'] : "NULL") . '");\' >Add</a>' . "</td>\n";
    if (!empty($_SESSION['canManageEntries']))
      echo '<td style="text-align:center">' . "<input type=\"checkbox\" name=\"unknownHostLists[]\" value=\"" . $temp . "\" onclick=\"adminRowClick(this,'tr$i',$i);\"/></td>\n";
    echo "</tr>\n";
  }

  // Close table

  ?>
  </table>
  </td>
  </tr>

  </table>
  <br />
  <?php
  if (!empty($_SESSION['canManageEntries']))
    echo submit_button('Delete', 'unknownHostLists') . hidden_fn('deleteunknownHostId');

  ?>
  </form>
  <?php
}

function build_generic_landing_page_body($operation) {

  ?>
  <td>
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>

  </tr>
  </table>
  </td>
  <?php
}
/**
  #       ###  #####  ####### #     #  #####  #######
  #        #  #     # #       ##    # #     # #
  #        #  #       #       # #   # #       #
  #        #  #       #####   #  #  #  #####  #####
  #        #  #       #       #   # #       # #
  #        #  #     # #       #    ## #     # #
  ####### ###  #####  ####### #     #  #####  #######

  ######  ####### ######  ####### ######  #######
  #     # #       #     # #     # #     #    #
  #     # #       #     # #     # #     #    #
  ######  #####   ######  #     # ######     #
  #   #   #       #       #     # #   #      #
  #    #  #       #       #     # #    #     #
  #     # ####### #       ####### #     #    #

  #     # ######  #       #######    #    ######
  #     # #     # #       #     #   # #   #     #
  #     # #     # #       #     #  #   #  #     #
  #     # ######  #       #     # #     # #     #
  #     # #       #       #     # ####### #     #
  #     # #       #       #     # #     # #     #
  #####  #       ####### ####### #     # ######
 */

/**
 * build_license_report_upload()
 *
 * @return
 */
function build_license_report_upload() {

  ?>
  <td>
  <form enctype="multipart/form-data" method="post" action="license_mgr_update.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
  <table><tr>
  <td valign="center" align="right">*License Report File:</td>
  <td valign="center" align="left">  <input size="40" name="licenseReport" type="file"></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    </tr>
  <tr>
  <td valign="center" align="center" colspan="2">
    <?php
    echo submit_button('Upload', 'licenseReport') . hidden_fn('uploadLicenseReport')

    ?>
  </td>
  </tr>
  </table>
  </tr>
  </table>
  <br />
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
  <table>
  <tr><td valign="top" align="right">
  </td></tr></table>
  </tr>
  </table>
  </form>
  </td>
  <?php
}

  /**
 * build_journaling_body()
 *
 * @param mixed $pager
 * @param mixed $journalEntries
 * @param mixed $err
 * @param mixed $hasManagePermission
 * @return
 */
function build_journaling_body(&$pager, $journalEntries, $err, $filterLists) {
  $link = new Link();
  $util = new Utility();

  $login_select = $util->build_select_options($filterLists['logins'], 'login_filter', isset($_GET['filterLogin']) ? $_GET['filterLogin'] : null, true,
    "onchange=\"filterClusters(this,'filterLogin')\"", true);
  $entry_type_select = $util->build_select_options($filterLists['entry_type'], 'entry_type_filter', isset($_GET['filterEntry_type']) ? $_GET['filterEntry_type'] : null, true,
    "onchange=\"filterClusters(this,'filterEntry_type')\"", true);
  $action_select = $util->build_select_options($filterLists['action'], 'action_filter', isset($_GET['filterAction']) ? $_GET['filterAction'] : null, true,
    "onchange=\"filterClusters(this,'filterAction')\"", true);

  ?>
  <td>
  <form name="manageJournalFile" method="post" action="license_mgr_update.php" onsubmit="return checkJournalingForm();">
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
  <td class="tableBorder">
  <table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
  <td colspan="5" class="tableTitle">&#8250; <?php echo "Journal File" ?></td>
  </tr>
  <?php
  echo "
        <tr class=\"rowHeaders\">
          <td width=\"25%\">" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'journal_file_entry_timestamp'), 'Timestamp') . "<br>"
  . "From <input type='text' class='textbox' style='width: 75px;' id='from' name='from' " . (!empty($_GET['filterFrom']) ? "value='" . $_GET['filterFrom'] . "'" : '') . "/>
              To <input type='text'  class='textbox'  style='width: 75px;' id='to' name='to' " . (!empty($_GET['filterTo']) ? "value='" . $_GET['filterTo'] . "'" : '') . "/></td>
          <td width=\"20%\">" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'login'), 'Admin Id') . $login_select . "</td>
          <td width=\"8%\">" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'action'), 'Action') . $action_select . "</td>
          <td width=\"8%\">" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'entry_type'), 'Entity') . $entry_type_select . "</td>
          <td width=\"25%\">Additional Data</td>
        </tr>
    ";

  if (!$journalEntries) {
    echo '<tr class="cellColor0"><td colspan="9" style="text-align: center;">' . $err . '</td></tr>' . "\n";
  }

  for ($i = 0; is_array($journalEntries) && $i < count($journalEntries); $i++) {
    $entry = $journalEntries[$i];
    echo "<tr class=\"cellColor" . ($i % 2) . "\" align=\"center\" id=\"tr$i\">\n"
    . '<td style="text-align:left">' . $entry['journal_file_entry_timestamp'] . "</td>\n";
    echo '<td style="text-align:center">' . $entry['login'] . "</td>\n";
    echo '<td style="text-align:center">' . $entry['action'] . "</td>\n";
    echo '<td style="text-align:center">' . $entry['entry_type'] . "</td>\n";
    echo '<td style="text-align:center">' . "\n";
    echo "<table border=0 width=100%>\n";
    for ($j = 0; is_array($entry['data']) && $j < count($entry['data']); $j++) {
      echo "<tr>\n";
      echo '<td width="50%" style="text-align:right">' . $entry['data'][$j]['journal_file_data_key'] . " =</td>\n";
      echo '<td width="50%" style="text-align:left">' . $entry['data'][$j]['journal_file_data_value'] . "</td>\n";
      echo "</tr>\n";
    }
    echo "</table>\n";
  }

  // Close table

  ?>
  </table>
  </td>
  </tr>
  </table>
  <br />
  <?php
  $pager->printPages();

  ?>
  </form>
  <script>
    $(function () {
      $("#from").datepicker({
        defaultDate: "-1m",
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        showOn: "button",
        buttonImage: "img/calendar.gif",
        buttonImageOnly: true,
        onSelect: function () {
          filterDate('from', 'filterFrom');
        },
      });
      $("#to").datepicker({
        defaultDate: "+1d",
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        showOn: "button",
        buttonImage: "img/calendar.gif",
        buttonImageOnly: true,
        onSelect: function () {
          filterDate('to', 'filterTo');
        }
      });
      $('#from').focus(function () {
        $('#from').datepicker('show');
      });
      $('#to').focus(function () {
        $('#to').datepicker('show');
      });
    });
  </script>
  </td>
  <?php
}
/**
  #    # #  ####   ####
  ##  ## # #      #    #
  # ## # #  ####  #
  #    # #      # #
  #    # # #    # #    #
  #    # #  ####   ####
 */

/**
 * submit_button()
 *
 * @param mixed $value
 * @param string $get_value
 * @param mixed $title
 * @return
 */
function submit_button($value, $get_value = '', $title = null, $name = 'submit') {
  return '<input type="submit" id="' . $name . '" name="' . $name . '" value="' . $value . '" class="button" ' . (($title != null) ? 'title="' . $title . '"' : '') . '/>' . "\n"
    . '<input type="hidden" name="get" value="' . $get_value . '" />' . "\n";
}

/**
 * hidden_fn()
 *
 * @param mixed $value
 * @return
 */
function hidden_fn($value, $name = "fn", $id = null) {
  $id = ($id === null ? $value : $id);
  return '<input type="hidden" name="' . $name . '" id="' . $id . '" value="' . $value . '" />' . "\n";
}

/**
 * operation()
 *
 * @param mixed $value
 * @return
 */
function operation($value) {
  return '<input type="hidden" name="tempAdd" value="' . $value . '" />' . "\n";
}

/**
 * delService()
 *
 * @param mixed $value
 * @return
 */
function delService($value) {
  return '<input type="hidden" name="delService" value="' . $value . '" />' . "\n";
}

/**
 * cancel_button()
 *
 * @param mixed $pager
 * @return
 */
function cancel_button(&$pager) {
  $cust_id = isset($_POST['customer_filter']) ? '&customer_filter=' . $_POST['customer_filter'] : (isset($_GET['customer_filter']) ? '&customer_filter=' . $_GET['customer_filter'] : '');
  return '<input type="button" name="cancel" value="Cancel" class="button" onclick="javascript: document.location=\'' . $_SERVER['PHP_SELF'] . '?operation=' . $_GET['operation'] . $cust_id . '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() . '&amp;' . $pager->getPageVar() . '=' . $pager->getPageNum() . '\';" />' . "\n";
}

/**
 * cancel_button_remove_GET()
 *
 * @param mixed $array of attributes to remove from GET string
 * @return
 */
function cancel_button_remove_GET($attrs2Remove) {
  $urlLink = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
  if (is_array($attrs2Remove)) {
    foreach ($attrs2Remove as $key => $attr) {
      $urlLink = preg_replace('/&?' . $attr . '=\w*/', '', $urlLink);
    }
  }

  return '<input type="button" name="cancel" value="Cancel" class="button" onclick="javascript: document.location=\'' . $urlLink . '\';" />' . "\n";
}

/**
 * cancel_button_close_window()
 *
 * @return
 */
function cancel_button_close_window() {
  return '<input type="button" name="cancel" value="Cancel" class="button" onclick="javascript: self.close()" />' . "\n";
}

/**
 * back_button()
 *
 * @param mixed $pager
 * @return
 */
function back_button(&$pager) {
  $cust_id = isset($_POST['customer_filter']) ? '&customer_filter=' . $_POST['customer_filter'] : (isset($_GET['customer_filter']) ? '&customer_filter=' . $_GET['customer_filter'] : '');
  return '<center><input type="button" name="Back" value="Back" class="button" onclick="javascript: document.location=\'' . $_SERVER['PHP_SELF'] . '?operation=' . $_GET['operation'] . $cust_id . '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() . '&amp;mode=view&amp;' . $pager->getPageVar() . '=' . $pager->getPageNum() . '\';" />' . "\n";
}

/**
 * build_conditional_submenu()
 *
 * @param mixed $parents
 * @param mixed $subMenuDivName
 * @param mixed $subMenuData
 * @param mixed $activeParentName
 * @param mixed $activeSubMenuName
 * @return
 */
function build_conditional_submenu($parents, $subMenuDivName, $subMenuData, $activeParentName, $activeSubMenuName) {
  echo '<div id="' . $subMenuDivName . '">' . "\n";

  if ($activeParentName == '') {
    $activeParentName = $parents[0];
  }

  if ($activeSubMenuName == '') {
    $activeSubMenuName = $subMenuData[$activeParentName][0];
  }

  for ($i = 0; $i < count($parents); $i++) {
    $parentName = $parents[$i];
    $specificSubMenu = $subMenuData[$parentName];
    echo '  <div id="' . $parentName . '" style="display=\'none\'">' . "\n";
    echo '    <select name="' . $parentName . '_submenu_item">' . "\n";
    for ($j = 0; $j < count($specificSubMenu); $j++) {
      echo '      <option value="' . $specificSubMenu[$j] . '"' . ((($parentName == $activeParentName) && ($specificSubMenu[$j] == $activeSubMenuName)) ? (' selected="selected"') : '') . '>' . $specificSubMenu[$j] . '</option>' . "\n";
    }
    echo '    </select>' . "\n";
    echo '  </div>' . "\n";
  }

  echo '</div>' . "\n";
}

/**
 * add_on_load_script()
 *
 * @param mixed $ownerId
 * @param mixed $subMenuId
 * @return
 */
function add_on_load_script($ownerId, $subMenuId) {

  ?>
  <script language="javascript" type="text/javascript">
    function loadMenu()
    {
  <?php echo "updateSubLists('" . $ownerId . "','" . $subMenuId . "');";

  ?>

    }
  </script>
  <?php
}

/**
 * add_cluster_load_script()
 *
 * @param mixed $requiresClusters
 * @param mixed $system_name
 * @param mixed $as_cluster_name
 * @return
 */
function add_cluster_load_script($requiresClusters, $system_name, $as_cluster_name) {

  ?>
  <script language="javascript" type="text/javascript">
    function loadMenu()
    {
  <?php echo "updateClusterListSection(" . ($requiresClusters ? 1 : 0) . ", 'customer_id', '" . $system_name . "', '" . $as_cluster_name . "');";

  ?>

    }
  </script>
  <?php
}

/**
 * add_on_load_script_for_licensing_page()
 *
 * @return
 */
function add_on_load_script_for_licensing_page() {

  ?>
  <script language="javascript" type="text/javascript">
    function loadMenu()
    {
  <?php echo "buildOptionList();";

  ?>

    }
  </script>
  <?php
}

/**
 * print_jscalendar_return_date()
 * Prints out the javascript necessary to set up the calendars for choosing start/end dates
 * @param mixed $returnDate
 * @return
 */
function print_jscalendar_return_date($returnDate = null, $maxYear = '2999', $maxDate = null) {
  if ($returnDate == null) {
    $returnDate = mktime();
  }
  //add something to handle maxYear

  ?>
  <script type="text/javascript">
    var returnDate = new Date(<?php
  echo is_int($returnDate) ? date('Y,m,d', $returnDate) : date('Y,m,d', strtotime($returnDate));

  ?>);
    // Start date calendar
    $("#hdn_return_date").datepicker({
      showOn: "both",
      buttonImage: "img/calendar.gif",
      yearRange: 'c-10:c+10',
      buttonImageOnly: true,<?php
  echo (!empty($maxDate) ? '  maxDate: "' . $maxDate . '",' : '');

  ?>
      dateFormat: 'mm/dd/yy',
      changeYear: true,
      defaultDate: returnDate});
    function clearReturnDate()
    {
      document.getElementById("hdn_return_date").value = "None";
    }
  </script>
  <?php
}

/**
 *
 * @param type $permissions
 * @param type $roles
 */
function build_manage_permissions($permissions, $roles) {
  if (!empty($_SESSION['canManageUsers'])) {
    echo '<h2>Manage Permissions (' . count($permissions) . ')</h2>';

    ?>
    <input type="button" class="button" name="add" value="Add new role" onClick="overlay('overlay')"/><br><br>
    <table id="permissions_table" class='sort' style='width:100%;table-layout:fixed'>
    <thead><tr>
    <th style='width:7%;'>Category</th>
    <th style='width:12%;'>Permission </th>
    <th style='width:4%;'>Desc</th>
    <?php
    foreach ($roles as $role) {
      if ($role['roles_uid'] != 1) {
        echo "<th style='width:6%;word-wrap:break-word;' title='" . $role['roles_description'] . "'> " . $role['roles_name'] . " <br><a style='color:#FFFFFF;text-decoration:underline;font-size:10px;' href='#' OnClick='javascript:EditRole(\"" . $role['roles_name'] . "\",\"" . $role['roles_uid'] . "\",\"" . $role['roles_description'] . "\")'>Edit </a> / <a style='color:#FFFFFF;text-decoration:underline;font-size:10px;' href='#' OnClick='javascript:ShowDeleteRole(\"" . $role['roles_uid'] . "\",\"" . $role['roles_name'] . "\")'>Delete </a></th>";
      } else {
        echo "<th style='width:6%;word-wrap:break-word;' title='" . $role['roles_description'] . "'> " . $role['roles_name'] . " <br><a style='color:#FFFFFF;text-decoration:underline;font-size:10px;' href='#' OnClick='javascript:EditRole(\"" . $role['roles_name'] . "\",\"" . $role['roles_uid'] . "\",\"" . $role['roles_description'] . "\")'>Edit </a></th>";
      }
    }
    echo "</tr></thead><tbody>";

    foreach ($permissions as $permission) {
      echo "<tr><th >" . $permission['category'] . "  </td>";
      echo "<th style='word-wrap:break-word;' title='" . $permission['permission_description'] . "'>" . $permission['permission_name'] . "</td>";
      echo "<th style='text-align:center;' title='" . $permission['permission_description'] . "'><a href='#' OnClick='javascript:EditPermission(\"" . $permission['permission_name'] . "\",\"" . $permission['permission_uid'] . "\",\"" . $permission['permission_description'] . "\",\"" . $permission['category'] . "\")'>Edit </a> </td>";
      foreach ($roles as $role) {
        if (!empty($permission['roles'][$role['roles_uid']])) {
          echo '<td style="text-align:center;" id="' . $role['roles_uid'] . "|" . $permission['permission_uid'] . '">';
          if ($role['roles_uid'] == 1) {
            echo '<input type="checkbox" disabled="disabled"  onclick=\'toggle_modified(this,"' . $role['roles_uid'] . "|" . $permission['permission_uid'] . '")\' name="uid" value="' . $permission['roles'][$role['roles_uid']]['roles_permissions_uid'] . '" checked="checked"/></td>';
          } else {
            echo '<input type="checkbox" onclick=\'toggle_modified(this,"' . $role['roles_uid'] . "|" . $permission['permission_uid'] . '")\' name="uid" value="' . $permission['roles'][$role['roles_uid']]['roles_permissions_uid'] . '" checked="checked"/></td>';
          }
        } else {
          echo '<td style="text-align:center;" id="' . $role['roles_uid'] . "|" . $permission['permission_uid'] . '">'
          . '<input type="checkbox" onclick=\'toggle_modified(this,"' . $role['roles_uid'] . "|" . $permission['permission_uid'] . '")\' value="" /></td>';
        }
      }
      echo "</tr>";
    }

    ?>
    </tbody></table>
    <div id="array_test" style="width:200px;" class="changed"> </div>
    <br><input type="button" class="button" name="action" value="save" onClick="send_permissions_changes()"/>
    <br>
    <br>


    <div id="edit_form" class="overlay">
    <div id="edit_form_child">
    <table class='sort'>
    <thead>
    <tr><th colspan="2"><span id="edit_name" ></span> (<span style='font-size:8px;' id="edit_uid"></span>)</th></tr>
    </thead>
    <tr><td>Category</td><td><input id="edit_category" type="" ></td></tr>
    <tr><td>Description</td><td><textarea ROWS="5" cols="35"  id="edit_description"></textarea></td></tr>
    <tr><td colspan="2" style="text-align:center;"><input type="button" class="button" name="action1" value="save_permission" onClick="send_permission_edit()"/>
    <input type="button" class="button" name="close_permission_role" value="Close window" onClick="overlay('edit_form')"/></td></tr>
    </table>
    </div>
    </div>

    <div id="edit_form_role" class="overlay">
    <div id="edit_form_role_child">
    <table class='sort'>
    <thead>
    <tr><th colspan="2"><span id="edit_name_role" ></span> (<span style='font-size:8px;' id="edit_uid_role"></span>)</th></tr>
    </thead>
    <tr><td>Description</td><td><textarea ROWS="5" cols="35"  id="edit_description_role"></textarea></td></tr>
    <tr><td colspan="2" style="text-align:center;"><input type="button" class="button" name="action2" value="save_role" onClick="send_roles_edits()"/>
    <input type="button" class="button" name="close_edit_role" value="Close window" onClick="overlay('edit_form_role')"/></td></tr>
    </table>
    </div>
    </div>

    <div id="overlay">
    <div>
    <table class='sort'>
    <thead>
    <tr><th colspan="2">         Enter a name for the role:</th></tr>
    </thead>
    <tr><td>Name</td><td><input type="text" id='role_name'></td></tr>
    <tr><td colspan="2" style="text-align:center;"> <input type="button" class="button" name="new_role" value="Add role" onClick="send_new_role()"/>
    <input type="button" class="button" name="close_new_role" value="Close window" onClick="overlay('overlay')"/></td></tr>
    </table>
    </div>
    </div>

    <div id="overlay2">
    <div id="delete_div">
    <table class='sort'>
    <thead>
    <tr><th colspan="2"><input type="hidden" id="role_uid_del" name="uid" value="" />
    Do you want to delete the following role? </th></tr>
    </thead>
    <tr><td>Role:</td><td><span id="role_to_delete"></span></td></tr>
    <tr><td>Role UID:</td><td><span id="role_to_delete_uid"></span></td></tr>
    <tr><td colspan="2" style="text-align:center;">
    <input type="button" class="button" name="new_role" value="Delete role" onClick="DeleteRole()"/>
    <input type="button" class="button" name="close_delete_role" value="Close window" onClick="overlay('overlay2')"/></td></tr>
    </table>
    </div>
    </div>
    <form name="permissionsmatrix" method="post" action="report_mgr.php" >
      <?php
      echo submit_button('Download Permissions Matrix') . hidden_fn('reportOnPermissions');

      ?>
    </form>
    <?php
  }
}

/**
 *
 * @param type $tickets
 * @param type $lists
 */
function build_manage_lists($tickets, $lists) {
  if (!empty($_SESSION['canManageMailingList'])) {

    ?>
    <ul class='tabrow'>
    <li class=''><a href='/php/licenseReporting/index.php?operation=ManageLists'> Mailing Lists </a></li>
    <li class='selected'><a href='/php/licenseReporting/index.php?operation=ManageLists&manageTickets=true'> Mailing List / Tickets Association </a></li>
    </ul>
    <table id="tickets_table" class='sort' style='width:100%;table-layout:fixed'>
    <thead><tr>
    <th style='width:12%;'>Ticket </th>
    <?php
    foreach ($lists as $list) {
      echo "<th style='width:6%;word-wrap:break-word;' title='" . $list['list_description'] . "'> " . $list['list_name'] . " </th>";
    }
    echo "</tr></thead><tbody>";

    foreach ($tickets as $ticket) {
      echo "<tr><th style='word-wrap:break-word;' >" . $ticket['ticket_name'] . "</td>";
      foreach ($lists as $list) {
        if (!empty($ticket['lists'][$list['list_uid']])) {
          echo '<td style="text-align:center;" id="' . $list['list_uid'] . "|" . $ticket['ticket_uid'] . '">';
          echo '<input type="checkbox" onclick=\'toggle_modified(this,"' . $list['list_uid'] . "|" . $ticket['ticket_uid'] . '")\' name="uid" value="' . $ticket['lists'][$list['list_uid']]['mailing_list_to_tickets_uid'] . '" checked="checked"/></td>';
        } else {
          echo '<td style="text-align:center;" id="' . $list['list_uid'] . "|" . $ticket['ticket_uid'] . '">'
          . '<input type="checkbox" onclick=\'toggle_modified(this,"' . $list['list_uid'] . "|" . $ticket['ticket_uid'] . '")\' value="" /></td>';
        }
      }
      echo "</tr>";
    }

    ?>
    </tbody></table>
    <div id="array_test" style="width:200px;" class="changed"> </div>
    <br><input type="button" class="button" name="action" value="save" onClick="send_tickets_changes()"/>
    <br>
    <br>

    <form name="ticketsmatrix" method="post" action="report_mgr.php" >
      <?php
      echo submit_button('Download Tickets Matrix') . hidden_fn('reportOnTickets');

      ?>
    </form>
    <?php
  }
}

function build_manage_mailing_lists($lists) {
  if (!empty($_SESSION['canManageMailingList'])) {

    ?>
    <ul class='tabrow'>
    <li class='selected'><a href='/php/licenseReporting/index.php?operation=ManageLists'> Mailing Lists </a></li>
    <li class=''><a href='/php/licenseReporting/index.php?operation=ManageLists&manageTickets=true'> Mailing List / Tickets Association </a></li>
    </ul>
    <form name="mailing_lists" action="mailing_mgr.php" method="post">
    <table id="lists_table" class='sort' style='width:100%;table-layout:fixed'>
    <thead><tr>
    <th style='width:15%;'>List Name </th>
    <th style='width:50%;'>Description </th>
    <th style='width:5%;'>User Count </th>
    <th style='width:10%;'>List Type </th>
    <th style='width:10%;'>Edit </th>
    <th style='width:10%;'>Delete </th>
    <?php
    echo "</tr></thead><tbody>";

    foreach ($lists as $uid => $list) {
      echo "<tr><td style='text-align:left;'>" . $list['list_name'] . "</td>";
      echo "<td style='text-align:left;'>" . $list['list_description'] . "</td>";
      echo "<td>" . count($list['members']) . "</td>";
      echo "<td>" . $list['list_frequency'] . "</td>";
      echo "<td><a href='/php/licenseReporting/index.php?operation=ManageLists&list_uid=$uid'>Edit</a></td>";
      echo "<td><input id='$uid' name='mailing[]' type='checkbox' value='" . $list['list_uid'] . "'></td>";
      echo "</tr>";
    }

    ?>
    </tbody></table><br>

    <input type="submit" style ="float:right;" class="button" name="submit" value="Delete Mailing List">
    </form>
    <?php
  }
}

/**
 *
 * @param type $list
 * @param type $logins
 */
function build_list_edit($list, $logins, $add = false) {
  if (!empty($_SESSION['canManageMailingList'])) {
    $util = new Utility();
    $used = array();
    $unused = array();
    if (isset($list['members'])) {
      foreach ($list['members'] as $memberid => $member) {
        $used[$memberid] = $member['fname'] . ' ' . $member['lname'] . ' (' . $member['email'] . ')';
      }
    }

    foreach ($logins as $memberid => $login) {
      $unused[$memberid] = $login['fname'] . ' ' . $login['lname'] . ' (' . $login['email'] . ')';
    }

    ?>
    <br>
    <form name="editList" method="post" action="mailing_mgr.php" onsubmit="return checkEditList();">
    <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
    <tr>
    <td class="tableBorder">
    <table width="100%" border="0" cellspacing="1" cellpadding="0">
    <tr>
    <td width="200" class="formNames">List Name</td>
    <td class="cellColor">
      <?php
      if ($add) {
        echo'<input type="text" name="list_name" id="list_name" class="textbox" maxLength="20" value="" />';
      } else {
        echo $list['list_name'];
        echo '<input type="hidden" name="list_name" id="list_name" class="textbox" maxLength="20" value="' . $list['list_name'] . '" />';
      }

      ?></td>
    </tr>
    <tr>
    <td width="200" class="formNames">Description</td>
    <td class="cellColor"><textarea rows='4' cols='100' name="list_description" id="list_description" class="textbox" maxLength="80" value="" /><?php echo (isset($list['list_description'])) ? $list['list_description'] : ''; ?></textarea></td>
    </tr>
    <tr>
    <td width="200" class="formNames">Users</td>
    <td class="cellColor">
      <?php
      $member_switch = $util->build_switch_list($used, $unused, 'non_recipients', 'list_recipients');
      echo$member_switch;

      ?>
    </td>
    </tr>
    <tr>
    <td width="200" class="formNames">List Type</td>
    <td class="cellColor">
    <select name="list_type" id="list_type" class="textbox">
      <?php if (!$add) {

        ?>
      <option value="" selected="selected"></option>
      <option value="daily" <?php echo ($list['list_frequency'] == 'daily') ? 'selected' : ''; ?>>daily</option>
      <option value="weekly" <?php echo ($list['list_frequency'] == 'weekly') ? 'selected' : ''; ?>>weekly</option>
      <option value="monthly" <?php echo ($list['list_frequency'] == 'monthly') ? 'selected' : ''; ?>>monthly</option>
      <option value="quarterly" <?php echo ($list['list_frequency'] == 'quarterly') ? 'selected' : ''; ?>>quarterly</option>
      <option value="ad hoc" <?php echo ($list['list_frequency'] == 'ad hoc') ? 'selected' : ''; ?>>ad hoc</option>
      <?php
    } else {

      ?>
      <option value="" selected="selected"></option>
      <option value="daily" >daily</option>
      <option value="weekly">weekly</option>
      <option value="monthly">monthly</option>
      <option value="quarterly" >quarterly</option>
      <option value="ad hoc" >ad hoc</option>
    <?php }

    ?>

    </select>
    </td>
    </tr>
    </table>
    </table>
    <br />
    <?php
    if ($add) {
      echo '<input type="submit" class="button" name="submit" value="Add Mailing List">';
    } else {
      echo '<input type="hidden" name="list_uid" value="' . $list['list_uid'] . '" />';
      echo'<input type="submit" name="submit" value="Edit Mailing List" class="button" />';
    }

    ?>
    <input type="reset" name="reset" value="Clear" class="button" />
    </form>
    <?php
  }
}

function build_undelivered_inv($inv, $SKUs, $pager) {
  echo "<h2 style='padding: 0px;margin: 0px;'>Undelivered Inventory</h2>";
  $delivered = (!empty($_POST['delivered'])) ? $_POST['delivered'] : "";
  $cust_type = (!empty($_POST['cust_type'])) ? $_POST['cust_type'] : "";
  $sys_prod_type = (!empty($_POST['sys_prod_type'])) ? $_POST['sys_prod_type'] : "";

  ?>

  <div id="undelivered_inv_filter" style="padding: 1px;margin: 1px;width: 748px; height: 35px; overflow: no-display;">
  <form action="<?php echo $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']; ?>" method="POST"><h4 style="padding: 0px;margin: 0px;">Filters:</h4>
  Customers
  <input type="hidden" name="operation" value ="undelivered_inv">
  <select class="button" name="cust_type">
  <option value="All" >All</option>
  <option value="Locked" <?php echo $cust_type == "Locked" ? "selected='selected'" : ""; ?>>Locked</option>
  <option value="Unlocked" <?php echo $cust_type == "Unlocked" ? "selected='selected'" : ""; ?>>Unlocked</option>
  </select>
  Inventory
  <select class="button" name="delivered">
  <option value="All" >All</option>
  <option value="Under" <?php echo $delivered == "Under" ? "selected='selected'" : ""; ?>>Undelivered</option>
  <option value="Over" <?php echo $delivered == "Over" ? "selected='selected'" : ""; ?>>Overdelivered</option>
  </select>
  System/Product Types
  <select class="button" name="sys_prod_type">
  <option value="All" >All</option>
  <option value="production" <?php echo $sys_prod_type == "production" ? "selected='selected'" : ""; ?>>production</option>
  <option value="lab" <?php echo $sys_prod_type == "lab" ? "selected='selected'" : ""; ?>>lab</option>
  <option value="trial" <?php echo $sys_prod_type == "trial" ? "selected='selected'" : ""; ?>>trial</option>
  </select>

  <input id="btn_validate_filter" class="button" type="submit" value="Apply">
  <input id="btn_reset_filter" class="button" type="button" value="Reset">
  </form>
  </div>
  <div id="undelivered_inv_table_div">
  Showing bought minus delivered:
  <div id="tablewrapper1" style="width: 750px; height: 580px; margin: 1px;overflow: auto; border: solid 1px #36648B;">

  <table class="sort" style="width:85%;overflow: auto;" id="undelivered_inv_table">
  <thead>
  <tr><th style='vertical-align:bottom;' data-ptcolumn="customer">Customer</th>
    <?php
    foreach ($SKUs as $key => $sku) {
      $count = 0;
      foreach ($inv as $cust) {
        if (!empty($cust[$key]['bought']) || !empty($cust[$key]['bought'])) {
          $count = 1;
        }
      }
      if ($count === 0) {
        unset($SKUs[$key]);
      }
    }

    foreach ($SKUs as $sku) {
      echo "<th style='font-weight:normal;vertical-align:top;max-width:45px;width:45px;height:123px;'  data-ptcolumn='" . $sku . "'> <p class='verticalText'>" . wordwrap($sku,
        15, '<BR>') . "</p> </th>";
    }

    ?>
  </tr>
  </thead><tbody>
    <?php
    $table = '';
    foreach ($inv as $cust) {
      $row = "";
      $row .= "<tr><td><a href='#' onclick=\"open__alloc_report('" . $cust['id'] . "')\">" . $cust['name'] . "</a></td>";

      foreach ($SKUs as $key => $sku) {
        if (!empty($cust[$key]['bought'])) {
          $row .= "<td> " . ($cust[$key]['bought'] - $cust[$key]['delivered']) . " </td>";
        } else {
          $row .= "<td></td>";
        }
      }

      $row .= "</tr>";
      $table .= $row;
      //echo $row;
    }
    echo $table;

    ?>
  </tbody>
  </table>
  <?php
  $pager->printPages();

  ?>
  </div>
  </div>

  <script src="/php/licenseReporting/javascripts/jquery-1.11.2.min.js"></script>
  <script src="/php/licenseReporting/javascripts/FixedColumns.min.js"></script>
  <script src="/php/licenseReporting/javascripts/jquery.dataTables.min.js"></script>
  <script>

    function open__alloc_report(customer) {
      var form = $('<form></form>').attr('action', 'report_mgr.php').attr('method', 'post');
      form.append($("<input></input>").attr('type', 'hidden').attr('name', 'relAllocCustomerId').attr('value', customer));
      form.append($("<input></input>").attr('type', 'hidden').attr('name', 'fn').attr('value', 'reportOnLicenseAllocation'));
      form.appendTo('body').submit().remove();
    }
    $(document).ready(function () {
      var oTable = $('#undelivered_inv_table').dataTable({
        "sScrollY": 400,
        "sScrollX": "100%",
        "bFilter": false,
        "bSort": false,
        "bLengthChange": false,
        "bInfo": false,
        "bAutoWidth": false,
        "bPaginate": false,
        "bScrollCollapse": true
          //"sScrollXInner": "100%"
      }
      );
      new FixedColumns(oTable);
    });
  </script>
  <?php
}

function build_as_unknown_host_id_edit($rs) {

  ?>
  <div id="add_unknown_hosts" class="overlay">
  <div id="add_unknown_hosts_child">

  <form name="addElementDynamicSystemList" method="post" action="license_mgr_update.php" onsubmit="return checkAddASHostId();" >
  <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
  <td class="tableBorder">
  <table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
  <td width="200" class="formNames">Reported Name</td>
  <td class="cellColor"><span id="reported_name" style="text-align:left;"></span>
  </td>
  </tr>
  <?php if (!empty($_SESSION['canManageEntries'])) { ?>
    <tr>
    <td width="200" class="formNames">Customer Id</td>
    <td class="cellColor" style="text-align:left;">
    <select name="customer_id" onchange="updateClusterListSection(true, 'customer_id', null, null)" >
      <?php
      $customerNameList = $rs['customerNameList'];
      $customerIdList = $rs['customerIdList'];
      for ($i = 0; $i < count($customerNameList); $i++) {
        echo '<option value="' . $customerIdList[$i] . '"' . ((isset($rs['customer_id']) && ($rs['customer_id'] == $customerIdList[$i])) ? (' selected="selected"') : '') . '>' . $customerNameList[$i] . '</option>' . "\n";
      }

      ?>
    </select>
    </td>
    </tr>
    <tr>
    <td width="200" class="formNames">System Name</td>
    <td class="cellColor" style="text-align:left;">
    <select name="system_name"  onchange="updateClusterListSection(true, 'system_name', null, null)" >
    <option value="default">default</option>
    </select>
    </td>
    </tr>
    <tr>
    <td width="200" class="formNames">Cluster Name</td>
    <td class="cellColor" style="text-align:left;">
    <select name="as_cluster_name" >
    <option value="default">default</option>
    </select>
    </td>
    </tr>
  <?php } ?>
  <tr>
  <td width="200" class="formNames">Host ID1</td>
  <td class="cellColor" style="text-align:left;">
  <input type="text" name="host_id[1]" id="host_id1" readonly class="textbox" size=40 maxLength="120" value="" />
  </td>
  </tr>
  <tr>
  <td width="200" class="formNames">Host ID2</td>
  <td class="cellColor" style="text-align:left;">
  <input type="text" name="host_id[2]" id="host_id2" readonly class="textbox" size=40 maxLength="120" value="" />
  </td>
  </tr>
  <tr>
  <td width="200" class="formNames">Host ID3</td>
  <td class="cellColor" style="text-align:left;">
  <input type="text" name="host_id[3]" id="host_id3" readonly class="textbox" size=40 maxLength="120" value="" />
  </td>
  </tr>
  <tr>
  <td width="200" class="formNames">Host ID4</td>
  <td class="cellColor" style="text-align:left;">
  <input type="text" name="host_id[4]" id="host_id4" readonly class="textbox" size=40 maxLength="120" value="" />
  </td>
  </tr>
  <?php if (!empty($_SESSION['canManageEntries'])) { ?>
    <tr>
    <td width="200" class="formNames">Decommissioned?</td>
    <td class="cellColor" style="text-align:left;"><input type="checkbox" name="decomissioned"  /></td>
    </tr>
  <?php } ?>
  <tr>
  <td width="200" class="formNames">Description</td>
  <td class="cellColor" style="text-align:left;"><input type="text" name="description" class="textbox" size=25 maxLength="40" value="" /></td>
  </tr>
  </table>
  <?php
  add_cluster_load_script(true, null, null);

  ?>
  </td>
  </tr>
  </table>
  <br />
  <?php
  echo!empty($_SESSION['canManageEntries']) ? submit_button('Add Host ID', 'host_id') . hidden_fn('addASHostId') : "";
  echo!empty($_SESSION['canManageEntries']) ? ' <input type="reset" name="reset" value="' . 'Clear' . '" class="button" />' . "\n" : "";

  ?>
  <input type="button" class="button" name="close_add_unknown_hosts" value="Close window" onClick="overlay('add_unknown_hosts')"/>
  <?php
  echo "</form> </div></div>\n";
  unset($rs);
}

/**
 *
 * @param type $logFile
 */
function build_git_log($logFile) {

  echo "<table class='sort' style='width:54%;'>"
    . "<thead>"
    . "<tr>"
    . "<th colspan='2'><h5>Latest updates</h5></th>"
    . "</tr>"
    . "<tr>"
    . "<th style='text-align:left;'>Revision/Date</th>"
    . "<th style='text-align:left;'>Content</th>"
    . "</tr>"
    . "</thead>"
    . "<tbody>";

    $lines = file($logFile, FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      $parts = explode(",", trim($line));
    $hash = array_shift($parts);
      $date = array_shift(explode(" ", array_shift($parts)));
    $msg = implode(",", $parts);
    echo "<tr>"
      . "<td><b>{$hash}</b><br>{$date}</td>"
      . "<td style='text-align:left;'> " . nl2br(utf8_decode($msg)) . "</td>"
      . "</tr>";
    }
    echo "</tbody></table><br>";
  }

  /**
   *
   * @param type $customers
   * @param type $customerIds
   */
function build_import_profile_upload_form($customers, $customerIds, $productsList) {
  $formTools = new FormSelectors();

  $formTools->echoFormSelectorJS();

  $formTools->setSelectName('importToolSelectorId');
  $formTools->setSelectId('importToolSelectorId');
  $formTools->setSelectOnchange('show_hide_import_tools();');
  $formTools->addOption('Import Licensable In Profiles', 'licensable_import');
  $formTools->addOption('Import Product', 'product_import');
  $formTools->addOption('Import Product Profile', 'profile_import');
  $formTools->addOption('Clone Production Profile', 'profile_clone');
  $mainSelect = $formTools->renderSelect();
  echo $mainSelect;

  $formTools->setSelectName('product_type');
  $formTools->addOption('production');
  $formTools->addOption('lab');
  $formTools->addOption('demo');
  $formTools->addOption('trial');
  $formTools->addOption('overprovisioning');
  $formTools->setSelectNoEmpty();
  $productType = $formTools->renderSelect();

  $customerSelect = '<select name="customer_id"><option value=""></option>';
  for ($i = 0; $i < count($customers); $i++) {
    $customerSelect .= '<option value="' . $customerIds[$i] . '">' . $customers[$i] . '</option>' . "\n";
  }
  $customerSelect .= '</select>';
  echo '<br><br>';

  $formTools->setSelectName('prodProduct');
  $formTools->setSelectOnchange('cascadeProfiles("prodProduct","prodProfile");');
  $formTools->setSelectSort();
  foreach ($productsList as $productUid => $product) {
    if ($product['sku_type'] === 'production') {
      $formTools->addOption($product['name'], $productUid);
    }
  }
  $prodProductSelect = $formTools->renderSelect();

  $formTools->setSelectName('labProduct');
  $formTools->setSelectOnchange('validateBaseUnits("prodProduct","labProduct");');
  $formTools->setSelectSort();
  foreach ($productsList as $productUid => $product) {
    if ($product['sku_type'] !== 'production') {
      $formTools->addOption($product['name'], $productUid);
    }
  }
  $labProductSelect = $formTools->renderSelect();

  $formTools->setSelectName('prodProfile');
  $formTools->allowEmptySelect();
  $prodProfileSelect = $formTools->renderSelect();

  $labProfileInput = '<input type="text"  class="textbox" name="labProfile" value=""/>';

  $profileImport = buildPanelLine('* Please select a product profile file:', '<input type="file" name="profile_file" size="40" class="button">');
  $profileImport .= buildPanelLine('* Enter default profile name :', '<input type="text"  class="textbox" name="profile_name" value=""/>');
  $profileImport .= buildPanelLine('Enter product name (override exported name):', '<input type="text"  class="textbox" name="product" value=""/>');
  $profileImport .= buildPanelLine('Add missing LEs to all profiles (0 Qty):',
    '<input type="radio" name="add_LEs" value="true" checked="checked">Yes<input type="radio" name="add_LEs" value="false">No');
  $profileImport .= buildPanelLine('Keep LEs from cloned profile as applicable:',
    '<input type="radio" name="keep_LEs" value="true">Yes<input type="radio" name="keep_LEs" value="false" checked="checked">No');
  $profileImport .= buildPanelLine('Select Customer :', $customerSelect);
  $profileImport .= buildPanelLine('', '<input type="submit" name="submit" value="Upload Profile File" class="button">');
  buildImportPanel("profile_import", "uploadProfile", "import_type", "profile", "Select Profile", "generateLicense.php?operation=sku_profile_import", $profileImport);

  $productImport = buildPanelLine('* Please select a product file:', '<input type="file" name="profile_file" size="40" class="button">');
  $productImport .= buildPanelLine('Enter profile name :', '<input type="text"  class="textbox" name="profile_name" value="default"/>');
  $productImport .= buildPanelLine('Enter Product name :', '<input type="text"  class="textbox" name="product" value=""/>');
  $productImport .= buildPanelLine('* Enter Product Code :', '<input type="text"  class="textbox" name="product_code" value=""/>');
  $productImport .= buildPanelLine('* Select Product Type :', $productType);
  $productImport .= buildPanelLine('', '<input type="submit" name="submit" value="Upload Product File" class="button">');
  buildImportPanel("product_import", "uploadProfile", "import_type", "product", "Select Product", "generateLicense.php?operation=sku_profile_import", $productImport);

  $leImport = buildPanelLine('* Please select a product file:', '<input type="file" id="licensable_file" name="licensable_file" size="40" class="button">');
  $leImport .= buildPanelLine('', '<input type="submit" name="submit" value="Upload Licensable Into Product" class="button">');
  buildImportPanel("licensable_import", "importLicensableInProduct", "import_type", "product", "Select Product", "license_mgr_update.php", $leImport,
    'return checkImportLicensableInProductForm();', hidden_fn('importLicensableInProduct'));

  $cloneProfile = buildPanelLine('* Production Product', $prodProductSelect);
  $cloneProfile .= buildPanelLine('* Profile', $prodProfileSelect);
  $cloneProfile .= buildPanelLine('* Lab Product', $labProductSelect);
  $cloneProfile .= buildPanelLine('* Lab Profile', $labProfileInput);
  $cloneProfile .= buildPanelLine('', '<input type="submit" name="submit" value="Clone Profile" class="button">');
  buildImportPanel("profile_clone", "cloneProductionProfile", "import_type", "clone", "Select Product to Clone", "generateLicense.php?operation=sku_profile_import",
    $cloneProfile);
}

/**
 * return an html row with text in the first cell and field in the second cell
 *
 * @param type $text  label/instructions
 * @param type $field html field(select,input,etc..)
 *
 * @return type       complete tr string
 */
function buildPanelLine($text, $field) {
  return '<tr align="center"><th>' . $text . '</th><td style="text-align: left;">' . $field . '</td></tr>';
}

/**
 *  Simple function building a toggleable panel div
 *
 * @param type $divId       div id used to toggle the panel on and off
 * @param type $formName    form name
 * @param type $hiddenName  name for a hidden field returned by the form
 * @param type $hiddenValue value for the hidden field returned by the form
 * @param type $title       Div title
 * @param type $action      form action
 * @param type $content     rows generated by buildPanelLine
 * @param type $onSubmit    (optional) javascript function called onSubmit event of the form
 * @param type $extraField  extra hidden fields if required
 */
function buildImportPanel($divId, $formName, $hiddenName, $hiddenValue, $title, $action, $content, $onSubmit = '', $extraField = '') {
  echo '<div id="' . $divId . '" style="display:none;">
      <form name="' . $formName . '" enctype="multipart/form-data" method="post" action="' . $action . '" onsubmit="' . $onSubmit . '">
        <input type="hidden" name="' . $hiddenName . '" value="' . $hiddenValue . '">
          ' . $extraField . '
      <table width="50%" class="sort" align="left">
        <thead>
          <tr><th colspan="2">' . $title . '</th></tr>
      </thead>' . $content . '
        </table>
      </form>
       </div>';
}

function build_teaser_profiles($teasers, $pager) {

  ?><form name="teaser_profile" action="teaser_mgr.php?operation=teaser_profiles" method="POST">
  <table class="sort" width="90%">
  <thead>
  <tr><th colspan="6" style='text-align:left;'><h5>&#8250; Manage Teaser Profiles<h5></th></tr>
  <tr><th>Teaser Name</th><th width="30%">Customer</th><th width="35%">Description</th><th width="5%">Used</th><th>Edit</th><th width="5%">Delete</th></tr>
  </thead>
  <tbody>
    <?php
    foreach ($teasers as $teaser) {
      echo "
          <tr>
            <td><a href='#' OnClick='javascript:EditTeaserProfile(\"" . $teaser['teaser_profile_uid'] . "\",\"" . $teaser['teaser_profile_name'] . "\")'>" . $teaser['teaser_profile_name'] . "</a></td>
            <td>" . $teaser['customer_id'] . "</td><td>" . $teaser['description'] . "</td>";
      if ($teaser['use_count'] > 0) {
        echo "<td><a target='_blank' href='index.php?operation=teaser_view&filter=" . $teaser['teaser_profile_name'] . "'>" . $teaser['use_count'] . "</a></td>";
        echo "<td><a href='#' OnClick='javascript:edit_teaser_profile(\"" . $teaser['teaser_profile_uid'] . "\",\"" . $teaser['teaser_profile_name'] . "\",\"" . $teaser['customer_id'] . "\",\"" . $teaser['description'] . "\")'>Edit</a></td>";
        echo "<td><input name='remove_teaser_profile[]' type='checkbox' disabled='disabled' value='" . $teaser['teaser_profile_uid'] . "'></td></tr>";
      } else {
        echo "<td>" . $teaser['use_count'] . "</td>";
        echo "<td><a href='#' OnClick='javascript:edit_teaser_profile(\"" . $teaser['teaser_profile_uid'] . "\",\"" . $teaser['teaser_profile_name'] . "\",\"" . $teaser['customer_id'] . "\",\"" . $teaser['description'] . "\")'>Edit</a></td>";
        echo "<td><input name='remove_teaser_profile[]' type='checkbox' value='" . $teaser['teaser_profile_uid'] . "'></td></tr>";
      }
    }

    ?>
  <tr><td colspan="6">
  <input  type="button" class="button" OnClick='javascript:Add_teaser_profile()' value="Add New Teaser Profile" >
  <input  type="submit" class="button"  value="Delete selected"  style="float:right;">
  </td></tr>
  </tbody>
  </table>
  </form><?php
  $pager->printPages();
  echo "<div id='teaser_edit_frame' style='display:none;'></div>
        <div id='rel_details' class='overlay'>
          <div id='rel_details_child'></div>
        </div>";
}

function build_teaser_view($view, $show_details = false) {

  ?>
  <input type="button" name="" class="button"
         value="Show/Hide details"
         onclick="toggleViewDetails();">
  <table class='sort' width='80%'>
  <thead>
  <tr>
  <th>Teaser Profile</th>
  <th>Cluster</th>
  <?php
  if ($show_details === true) {
    echo "<th>Release</th>";
  }

  ?>
  <th>Customer #</th>
  <th>Customer</th>
  </tr>
  </thead>
  <tbody>
    <?php
    foreach ($view as $cluster) {
      echo "<tr><td>" . $cluster['teaser_profile_name'] . "</td>
      <td><a target='_blank' href='index.php?operation=licinventory&customer_filter=" . $cluster['customer_id'] . "&inv_action=cluster_licenses&limit=25&as_cluster_uid=" . $cluster['as_cluster_uid'] . "'>" . $cluster['cluster_info']['as_cluster_name'] . "</a></td>";
      if ($show_details === true) {
        echo "<td>" . $cluster['cluster_info']['software_release'] . "</a></td>";
      }
      echo "<td><a target='_blank' href='index.php?operation=licinventory&customer_filter=" . $cluster['customer_id'] . "&inv_action=cluster_licenses&limit=25'>" . $cluster['customer_id'] . "</a></td>
      <td>" . $cluster['customer_info']['customer_name'] . "</td>
     </tr>";
    }

    ?>
  </tbody>
  </table>

  <?php
}

function build_display_regression_test($reg_test, $reg_test_details) {
  $link = new Link();
  $util = new Utility();
  $content = "";
  $nbNewFailed = 0;
  $grids = array(
    "Clusters" => "clusters",
    "Groups" => "groups"
  );
  // Filters
  $customerId_filter = $util->build_text_filter('customerId_filter', isset($_GET['customerId']) ? $_GET['customerId'] : "", true,
    "onkeydown=\"filterClusters(this, 'customerId')\"");
  $status_filter = $util->build_select_options(array('Not Started', 'In Progress', 'Completed'), 'status_filter', isset($_GET['status']) ? $_GET['status'] : null, true,
    "onchange=\"filterClusters(this, 'status')\"", true);
  $isNew_filter = $util->build_select_options(array('true', 'false'), 'isNew_filter', isset($_GET['isNew']) ? $_GET['isNew'] : null, true,
    "onchange=\"filterClusters(this, 'isNew')\"", true);
  $error_filter = $util->build_text_filter('error_filter', isset($_GET['error']) ? $_GET['error'] : "", true,
    "onkeydown=\"filterClusters(this, 'error')\"");
  // Sorting
  $customerNameSorting = $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_name'), 'Customer Name');
  $customerIdSorting = $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_id'), 'Customer ID');
  $systemNameSorting = $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'system_name'), 'System Name');
  $systemIdSorting = $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'system_uid'), 'System ID');
  $nameSorting = $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'name'), 'Name');
  $statusSorting = $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'status'), 'Status');
  $releaseSorting = $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'release_name'), 'Release');
  $isNewSorting = $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'is_new'), 'Is New');
  foreach ($grids as $label => $details) {
    $content .= "<table class='sort' width='100%'>
      <thead>
        <tr><th colspan='11' style='text-align:left;'><h5>&#8250;$label</h5></th></tr>
      </thead>
      <tbody>
        <tr>
          <td width='10%'><b>$customerNameSorting</b></td>
          <td width='5%'><b>$customerIdSorting</b>{$customerId_filter}</td>
          <td width='10%'><b>$systemNameSorting</b></td>
          <td width='5%'><b>$systemIdSorting</b></td>
          <td width='10%'><b>$nameSorting</b></td>
          <td width='8%'><b>$statusSorting</b>{$status_filter}</td>
          <td width='4%'><b>$releaseSorting</b></td>
          <td width='4%'><b>$isNewSorting</b>{$isNew_filter}</td>
          <td><b>Comparison Result</b>{$error_filter}</td>
          <td width='5%'><b>Comparison Report</b></td>
          <td width='10%'><b>Action</b></td>
        </tr>";
    foreach ($reg_test_details[$details] as $test) {
      $objectLink = "/php/licenseReporting/index.php?operation=licinventory&customer_filter={$test["customer_id"]}&inv_action=cluster_licenses&as_cluster_uid={$test["object_uid"]}";
      $reportLink = "/php/licenseReporting/generateLicense.php?toolType=showRegressionTestReport&object_uid={$test["object_uid"]}&is_group=0";
      $replaceAction = "ReplaceTestSnapshot({$test["object_uid"]});";
      if ($test["is_group"] == 1) {
        $objectLink = "/php/licenseReporting/index.php?operation=licinventory&customer_filter={$test["customer_id"]}&inv_action=group_licenses&group_uid={$test["object_uid"]}";
        $reportLink = "/php/licenseReporting/generateLicense.php?toolType=showRegressionTestReport&object_uid={$test["object_uid"]}&is_group=1";
        $replaceAction = "ReplaceGroupTestSnapshot({$test["object_uid"]});";
      }
      $content .= "<tr>";
      $content .= "<td bgcolor='#FFFF00'>" . (isset($test['customer_name']) ? $test['customer_name'] : '') . "</td>";
      $content .= "<td bgcolor='#FFFF00'>" . (isset($test['customer_id']) ? $test['customer_id'] : '') . "</td>";
      $content .= "<td bgcolor='#FFFF00'>" . (isset($test['system_name']) ? $test['system_name'] : '') . "</td>";
      $content .= "<td bgcolor='#FFFF00'>" . (isset($test['system_uid']) ? $test['system_uid'] : '') . "</td>";
      $content .= "<td bgcolor='#FFFF00'><a href='{$objectLink}' target='_blank'>" . $test['name'] . "</a></td>";
      $content .= "<td bgcolor='#FFFF00'>" . $test['status'] . "</td>";
      $content .= "<td bgcolor='#FFFF00'>" . (isset($test['release_name']) ? $test['release_name'] : '') . "</td>";
      if ($test["is_new"] == 1) {
        $nbNewFailed++;
        $content .= "<td bgcolor='#FFFF00'>New</td>";
      } else {
        $content .= "<td bgcolor='#FFFF00'></td>";
      }
      $content .= "<td bgcolor='#FFFF00'>" . $test['errors'] . "</td>";
      $content .= "<td bgcolor='#FFFF00'><a href='{$reportLink}' target='_blank'>report</a></td>";
      $content .= "<td bgcolor='#FFFF00'><input type='button' id='Replace_{$test["object_uid"]}' value='Replace Snapshot' title='Replace Snapshot with Current License' class='button' onclick='return {$replaceAction}'></td>";
      $content .= "</tr>";
    }
    $content .= "</tbody></table>";
  }
  echo "<table class='sort' width='100%'>
    <thead>
      <tr><th colspan='2' style='text-align:left;'><h5>&#8250;Regression Test Summary</h5></th></tr>
    </thead>
    <tbody>
      <tr><td><b>Outcome</b></td><td><b>Parsed License Compare</b></td></tr>";
  echo "<tr><td>Passed</td><td>" . $reg_test['reg_passed'] . "</td></tr>";
  echo "<tr><td>Failed</td><td>" . $reg_test['reg_failed'] . "</td></tr>";
  echo "<tr><td>Failed (New)</td><td>" . $nbNewFailed . "</td></tr>";
  echo "<tr><td>Completed tests</td><td>" . ($reg_test['reg_failed'] + $reg_test['reg_passed']) . "</td></tr>";
  echo "<tr><td>Total Runtime</td><td>" . $reg_test['runtime'] . " seconds</td>";
  echo "</tbody></table><br>";
  echo "<table class='sort' width='100%'>
      <thead>
        <tr><th style='text-align:left;'><h5>&#8250;Regression Test Details</h5></th></tr>
      </thead>
    </table>";
  echo $content;
}

/**
 * Displays a view with all the clusters using an outdated license file
 *
 * @param type $clusters
 */
function build_view_outdated_licenses($clusters) {
  $link = new Link();
  $util = new Utility();

  ?>
  <table class="sort" width="100%">
  <thead>
  <tr><th colspan="8" style='text-align:left;'><h5>&#8250;Clusters With Outdated Licenses</h5></th></tr>
  <tr>
    <?php
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_name'), 'Customer Name') . "</td>";
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'system_name'), 'System Name') . "</td>";
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'as_cluster_name'), 'Cluster Name') . "</td>";
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'server_type'), 'Cluster Type') . "</td>";
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'software_release'), 'Release') . "</td>";
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'status'), 'Status') . "</td>";
    echo "<td style='font-weight:bold;'># HostId</td>";
    echo "<td style='font-weight:bold;'># Lic Nodes</td>";

    ?>
  </tr>
  </thead>
  <tbody>
    <?php
    foreach ($clusters as $cluster) {

      echo "<tr><td>" . $cluster['customer_name'] . "</td>";
      echo "<td>" . $cluster['system_name'] . "</td>";
      echo "<td><a href='/php/licenseReporting/index.php?operation=licinventory&customer_filter=" . $cluster['customer_id'] . "&inv_action=cluster_licenses&limit=25&as_cluster_uid=" . $cluster['as_cluster_uid'] . "' target='_blank'>" . $cluster['as_cluster_name'] . "</a></td>";
      echo "<td>" . $cluster['server_type'] . "</td>";
      echo "<td>" . $cluster['software_release'] . "</td>";
      echo "<td>" . $cluster['status'] . "</td>";
      echo "<td>" . $cluster['host_id_count'] . "</td>";
      echo "<td>" . $cluster['node_per_clusters'] . "</td>";
      echo "</tr>";
    }

    ?>
  </tbody>
  </table>
  <?php
}

/**
 * Displays a view with all the clusters using an outdated license file
 *
 * @param type $clusters
 */
function build_view_duplicated_hostids($data) {
  $link = new Link();
  $util = new Utility();

  ?>
  <table class="sort" width="100%">
  <thead>
  <tr><th colspan="7" style='text-align:left;'><h5>&#8250;Duplicated HostIDs</h5></th></tr>
  <tr>
    <?php
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_name'), 'Customer Name') . "</td>";
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'system_name'), 'System Name') . "</td>";
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'as_cluster_name'), 'Cluster Name') . "</td>";
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'server_type'), 'Cluster Type') . "</td>";
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'software_release'), 'Release') . "</td>";
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'status'), 'Status') . "</td>";
    echo "<td style='font-weight:bold;'>" . $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'a.host_id'), 'Host ID') . "</td>";

    ?>
  </tr>
  </thead>
  <tbody>
    <?php
    foreach ($data as $hostid) {

      echo "<tr><td>" . $hostid['customer_name'] . "</td>";
      echo "<td>" . $hostid['system_name'] . "</td>";
      echo "<td><a href='/php/licenseReporting/index.php?operation=licinventory&customer_filter=" . $hostid['customer_id'] . "&inv_action=cluster_licenses&limit=25&as_cluster_uid=" . $hostid['as_cluster_uid'] . "' target='_blank'>" . $hostid['as_cluster_name'] . "</a></td>";
      echo "<td>" . $hostid['server_type'] . "</td>";
      echo "<td>" . $hostid['software_release'] . "</td>";
      echo "<td>" . $hostid['status'] . "</td>";
      echo "<td><a href='/php/licenseReporting/index.php?operation=ashostids&customer_filter=" . $hostid['customer_id'] . "&limit=25&host_id_uid=" . $hostid['host_id_uid'] . "' target='_blank'>" . $hostid['host_id'] . "</a></td>";

      echo "</tr>";
    }

    ?>
  </tbody>
  </table>
  <?php
}

/**
 * build_resync_tools()
 *
 * displays the tools or the output of a sync if one was selected
 *
 */
function build_resync_tools() {

  ?>
  Sync Tools: <select id="syncToolSelectorId" onchange="show_hide_sync_tools();"  >
  <option value="" selected="true"></option><?php
  echo ((!empty($_SESSION['canResyncCustomers']) || !empty($_SESSION['superAdmin'])) ? '<option value="resync_sfdc_customers">Resync Salesforce Customers Information</option>' : '');
  echo (!empty($_SESSION['superAdmin']) ? '<option value="pre_provisionning">Pre-Provision Clusters</option>' : '');
  echo (!empty($_SESSION['superAdmin']) ? '<option value="decommission_clusters">Bulk Decommission Clusters</option>' : '');
  echo (!empty($_SESSION['superAdmin']) ? '<option value="auto_platform">Auto Adjust Platform Quantities</option>' : '');

  ?>
  </select>
  <br><br>
  <div id="resync_sfdc_customers" style="display:none;">
  This tool will resynchronize the customer information by reading from the salesforce database and updating the LRS database.
  <br><br>
  <form action="index.php" type="GET">
  <input type="hidden" name="operation" value="resync_systems">
  <input type="submit" name="resync_customers" class='button' value="Synchronize Systems">
  </form>

  </div>
  <div id="pre_provisionning" style="display:none;">
  This tool will pre-provision clusters with default associations for mandatory products like Operating System.
  <br><br>
  <form action="license_mgr_update.php" type="GET">
  <input type="hidden" name="operation" value="resync_systems">
  <input type="hidden" name="fn" value="pre_provision_systems">
  <input type="submit" name="pre_provision_systems" class='button' value="Update Clusters">
  </form>

  </div>
  <div id="decommission_clusters" style="display:none;">
  This tool will set to Decommissioned clusters with default all host_ids decommissioned or a stop reporting date in the past.
  <br><br>
  <form action="license_mgr_update.php" type="GET">
  <input type="hidden" name="operation" value="resync_systems">
  <input type="hidden" name="fn" value="decom_clusters">
  <input type="submit" name="decom_clusters" class='button' value="Decommission Clusters">
  </form>

  </div>
  <div id="auto_platform" style="display:none;">
  This tool will adjust the cluster platform quantities on as clusters, and then adjust total platform quantities on NS,XSP,WS servers.
  <br><br>
  <form action="license_mgr_update.php" type="GET">
  <input type="hidden" name="operation" value="resync_systems">
  <input type="hidden" name="fn" value="auto_platform">
  <input type="submit" name="auto_platform" class='button' value="Update platform Quantities">
  </form>

  </div>
  <?php
  /*   * *********************************
   * RUN THE SALESFORCE CUSTOMER SYNC
   * ********************************* */
  if (isset($_GET['resync_customers'])) {
    include_once __DIR__ . "/../resync_with_sfdc.php";
  }
  /*   * *********************************
   * RUN THE PRE PROVISIONNING SCRIPT
   * ********************************* */
  if (isset($_GET['pre_provision_systems'])) {
    /*
     * UNLOCKED CUSTOMERS ONLY
     *
     * R1.	The Operating System product shall be assigned by default against all production AS, NS, PS, WS, XSP, DBS, and MS clusters.
     * R2.	The Element Management System product shall be assigned by default to all production EMS in the LRS.
     * R3.	The EWS – CommPilot Interface (11310) shall be assigned by default for production WS and XSP clusters
     * R4.	The Operating System-Lab product shall be assigned by default to all lab AS, NS, PS, WS, XSP, DBS, and MS clusters
     * R5.	The Element Management System – Lab product shall be assigned by default to lab EMS servers.
     *
     * Quantity of 1 assigned with default profile
     * association created if it doesnt exist
     *
     */
    echo "Clusters updated.";
  }
}

/**
 * product profile view is used to display all the clusters associated to a product profile
 * @param type $view
 */
function build_product_profile_view($view, $show_details = false) {
  if ($show_details === true) {
    set_time_limit(3 * 60 /* in sec. */);
  }

  ?>
  <input type="button" name="" class="button"
         value="Show/Hide details"
         onclick="toggleViewDetails();">
  <table class='sort' width='80%'>
  <thead>
  <tr>
  <th colspan=15">Clusters</th>
  </tr>
  <tr>
  <th>Product Code</th>
  <th>Product Name</th>
  <?php
  if ($show_details === true) {
    echo '<th>Quantity</th>';
  }

  ?>
  <th>Profile</th>
  <?php
  if ($show_details === true) {
    echo '<th>Teaser</th>';
  }

  ?>
  <th>Customer ID</th>
  <th>Customer name</th>
  <th>System</th>
  <?php
  if ($show_details === true) {
    echo '<th>Type</th>';
  }

  ?>
  <th>Cluster</th>
  <?php
  if ($show_details === true) {
    echo '<th>Type</th><th>Release</th><th>Status</th><th>User Type</th><th>Cluster UID</th>';
  }

  ?>
  </tr>
  </thead>
  <tbody>
    <?php
    //build view rows first loop goes through each product
    foreach ($view['clusters'] as $product_uid => $product) {
      //second loop goes through each profiles within a product
      foreach ($product['profiles'] as $product_profile_uid => $product_profile) {
        //third loop goes through each cluster associated to a profile
        foreach ($product_profile['clusters'] as $as_cluster_uid => $as_cluster) {
          $cutomer_link = $_SERVER['PHP_SELF'] . '?operation=licinventory&inv_action=cluster_licenses&customer_filter=' . urlencode($as_cluster['customer_id']);
          $system_link = $cutomer_link . '&system_name=' . urlencode($as_cluster['system_name']);
          echo "<tr><td>" . $product['product_code'] . "</td>";
          echo "<td>" . $product['sku_name'] . "</td>";
          if ($show_details === true) {
            echo "<td><a target='_blank' href='$system_link'>" . $product['quantity'] . "</a></td>";
          }
          echo "<td>" . $product_profile['sku_profile_name'] . "</td>";
          if ($show_details === true) {
            $teaser_name = empty($as_cluster['teaser_profile_name']) ? '' : $as_cluster['teaser_profile_name'];
            echo "<td><a target='_blank' href='$system_link'>" . $teaser_name . "</a></td>";
          }
          //build link to the licensing section, first to the customer, then system, then cluster
          echo "<td><a target='_blank' href='$cutomer_link'>" . $as_cluster['customer_id'] . "</a></td>";
          echo "<td><a target='_blank' href='$cutomer_link'>" . $as_cluster['customer_name'] . "</a></td>";
          echo "<td><a target='_blank' href='$system_link'>" . $as_cluster['system_name'] . "</a></td>";
          if ($show_details === true) {
            echo "<td><a target='_blank' href='$system_link'>" . $as_cluster['system_type'] . "</a></td>";
          }
          $cluster_link = $system_link . '&as_cluster_uid=' . urlencode($as_cluster_uid);
          echo "<td><a target='_blank' href='$cluster_link'>" . $as_cluster['cluster_name'] . "</a></td>";
          if ($show_details === true) {
            echo "<td><a target='_blank' href='$cluster_link'>" . $as_cluster['server_type'] . "</a></td>";
            echo "<td><a target='_blank' href='$cluster_link'>" . $as_cluster['software_release'] . "</a></td>";
            echo "<td><a target='_blank' href='$cluster_link'>" . $as_cluster['status'] . "</a></td>";
            echo "<td><a target='_blank' href='$cluster_link'>" . $as_cluster['ac_user_type'] . "</a></td>";
            echo "<td><a target='_blank' href='$cluster_link'>" . $as_cluster_uid . "</a></td>";
          }
          echo "</tr>";
        }
      }
    }

    ?>
  </tbody>
  </table>
  <br>
  <table class='sort' width='80%'>
  <thead>
  <tr>
  <th colspan="11">Groups</th>
  </tr>
  <tr>
  <th>Product Code</th>
  <th>Product Name</th>
  <th>Profile</th>
  <th>Customer ID</th>
  <th>Customer name</th>
  <th>System</th>
  <?php
  if ($show_details === true) {
    echo '<th>Type</th>';
  }

  ?>
  <th>Group</th>
  <?php
  if ($show_details === true) {
    echo '<th>Release</th><th>Status</th><th>Group UID</th>';
  }

  ?>
  </tr>
  </thead>
  <tbody>
    <?php
    //build view rows first loop goes through each product
    foreach ($view['groups'] as $product_uid => $product) {
      //second loop goes through each profiles within a product
      foreach ($product['profiles'] as $product_profile_uid => $product_profile) {
        //third loop goes through each cluster associated to a profile
        foreach ($product_profile['groups'] as $group_uid => $group) {
          echo "<tr><td>" . $product['product_code'] . "</td>";
          echo "<td>" . $product['sku_name'] . "</td>";
          echo "<td>" . $product_profile['sku_profile_name'] . "</td>";
          //build link to the licensing section, first to the customer, then system, then cluster
          $cutomer_link = $_SERVER['PHP_SELF'] . '?operation=licinventory&inv_action=group_licenses&customer_filter=' . urlencode($group['customer_id']);
          echo "<td><a target='_blank' href='$cutomer_link'>" . $group['customer_id'] . "</a></td>";
          echo "<td><a target='_blank' href='$cutomer_link'>" . $group['customer_name'] . "</a></td>";
          $system_link = $cutomer_link . '&system_name=' . urlencode($group['system_name']);
          echo "<td><a target='_blank' href='$system_link'>" . $group['system_name'] . "</a></td>";
          if ($show_details === true) {
            echo "<td><a target='_blank' href='$system_link'>" . $group['system_type'] . "</a></td>";
          }
          $group_link = $system_link . '&group_uid=' . urlencode($group_uid);
          echo "<td><a target='_blank' href='$group_link'>" . $group['group_name'] . "</a></td>";
          if ($show_details === true) {
            echo "<td><a target='_blank' href='$system_link'>" . $group['group_software_release'] . "</a></td>";
            echo "<td><a target='_blank' href='$system_link'>" . $group['status'] . "</a></td>";
            echo "<td><a target='_blank' href='$system_link'>" . $group_uid . "</a></td>";
          }
          echo "</tr>";
        }
      }
    }

    ?>
  </tbody>
  </table>
  <br>
  <table class='sort' width='80%'>
  <thead>
  <tr>
  <th colspan="11">Over Allocations</th>
  </tr>
  <tr>
  <th>Product Code</th>
  <th>Product Name</th>
  <th>Profile</th>
  <th>System Name</th>
  <th>Cluster</th>
  <?php
  if ($show_details === true) {
    echo '<th>Operation</th>';
    echo '<th>Value</th>';
  }
  ?>
  </tr>
  </thead>
  <tbody>
    <?php
    //build view rows first loop goes through each product
    foreach ($view['overAllocations'] as $product_uid => $product) {
      //second loop goes through each profiles within a product
      foreach ($product['profiles'] as $product_profile_uid => $product_profile) {
        //third loop goes through each cluster associated to a profile
        foreach ($product_profile['overprovisionings'] as $overAllocation_uid => $overAllocation) {
          echo "<tr><td>" . $product['product_code'] . "</td>";
          echo "<td>" . $product['sku_name'] . "</td>";
          echo "<td>" . $product_profile['sku_profile_name'] . "</td>";
          echo "<td>" . $overAllocation['system_name'] . "</td>";
          $cluster_link = $_SERVER['PHP_SELF'] . '?operation=licinventory&inv_action=cluster_licenses&customer_filter=' . urlencode($overAllocation['customer_id']) . '&as_cluster_uid=' . urlencode($overAllocation['as_cluster_uid']);
          echo "<td><a target='_blank' href='$cluster_link'>" . $overAllocation['as_cluster_name'] . "</a></td>";
         if ($show_details === true) {
            echo "<td>" . $overAllocation['operation'] . "</td>";
            echo "<td>" . $overAllocation['value'] . "</td>";
          }
          echo "</tr>";
        }
      }
    }

    ?>
  </tbody>
  </table>
  <br>
  <table class='sort' width='80%'>
  <thead>
  <tr>
  <th colspan="11">Teasers</th>
  </tr>
  <tr>
  <th>Product Code</th>
  <th>Product Name</th>
  <th>Profile</th>
  <th>Customer ID</th>
  <th>Customer Name</th>
  <th>Teasure Profile Name</th>
  <?php
  if ($show_details === true) {
    echo '<th>Teasure Profile ID</th>';
  }
  ?>
  </tr>
  </thead>
  <tbody>
    <?php
    //build view rows first loop goes through each product
    foreach ($view['teasers'] as $product_uid => $product) {
      //second loop goes through each profiles within a product
      foreach ($product['profiles'] as $product_profile_uid => $product_profile) {
        //third loop goes through each cluster associated to a profile
        foreach ($product_profile['teasers'] as $teaser_uid => $teaser) {
          echo "<tr><td>" . $product['product_code'] . "</td>";
          echo "<td>" . $product['sku_name'] . "</td>";
          echo "<td>" . $product_profile['sku_profile_name'] . "</td>";
          //build link to the licensing section, first to the customer, then system, then cluster
          $cutomer_link = $_SERVER['PHP_SELF'] . '?operation=licinventory&inv_action=cluster_licenses&customer_filter=' . urlencode($teaser['customer_id']);
          echo "<td><a target='_blank' href='$cutomer_link'>" . $teaser['customer_id'] . "</a></td>";
          echo "<td><a target='_blank' href='$cutomer_link'>" . $teaser['customer_name'] . "</a></td>";
          echo "<td>" . $teaser['teaser_profile_name'] . "</td>";
          if ($show_details === true) {
            echo "<td>" . $teaser['teaser_profile_uid'] . "</td>";
          }
          echo "</tr>";
        }
      }
    }
    ?>
  </tbody>
  </table>
  <?php
}

function build_reset_customers_view($rs, $show_locked_customers_only = false) {
  $count_mandatory = sizeof($rs['mandatory_platform']);
  $count_optional = sizeof($rs['optional_platform']);
  //output table headers

  ?>
  <input type="button" name="" class="button" value="<?php echo ($show_locked_customers_only ? 'Show all' : 'Show only locked'); ?> customers" onclick="hideResetCustomers();">
  <table class="sort">
  <thead>
  <tr>
  <th colspan="7"></th>
  <th colspan="<?php echo $count_mandatory; ?>">Mandatory Platform Products</th>
  <th colspan="<?php echo $count_optional; ?>">Optional Platform Products</th>
  </tr>
  <tr>
  <th>Customer Name</th>
  <th>Customer ID</th>
  <th>Reseted</th>
  <th>SFDC Locked Date</th>
  <th># Production Systems</th>
  <th># Production Clusters</th>
  <th>Platform Qty</th>
  <?php
  foreach ($rs['mandatory_platform'] as $product_code => $product) {
    echo "<th>($product_code) " . $product['product_name'] . "</th>";
  }
  foreach ($rs['optional_platform'] as $product_code => $product) {
    echo "<th>($product_code) " . $product['product_name'] . "</th>";
  }

  ?>
  </tr><tbody>
    <?php
    foreach ($rs['resetables'] as $customer) {

      echo '<tr><td style="text-align:left;"><a href="index.php?operation=licinventory&inv_action=skus_summary&customer_filter=' . $customer['customer_id'] . '" target="_blank">' . $customer['customer_name'] . '</a></td>';
      echo '<td><a href="index.php?operation=licinventory&inv_action=skus_summary&customer_filter=' . $customer['customer_id'] . '" target="_blank">' . $customer['customer_id'] . '</a></td>';
      echo '<td ' . (empty($customer['reseted']) ? '>No' : ' style="color:red;">Yes') . '</td>';
      echo '<td>' . (empty($customer['customer_sfdc_only_inv']) ? "None" : Time::formatDate($customer['customer_sfdc_only_inv'])) . '</td>';
      echo '<td>' . (isset($customer['counts']['prod_systems']) ? $customer['counts']['prod_systems'] : 0) . '</td>';
      echo '<td>' . (isset($customer['counts']['prod_clusters']) ? $customer['counts']['prod_clusters'] : 0) . '</td>';
    $qty = 0;
      foreach ($rs['platform'] as $product_code => $product) {
        if (isset($customer['consumed'][$product['sku_uid']])) {
          $qty += $customer['consumed'][$product['sku_uid']];
        }
      }
      echo '<td style="text-align:right;">' . $qty . '</td>';
      foreach ($rs['mandatory_platform'] as $product_code => $product) {
        if (isset($customer['consumed'][$product['sku_uid']])) {
          $m_qty = $customer['consumed'][$product['sku_uid']];
          if ($qty > $m_qty) {
            echo '<td style="color:red;text-align:right;">' . $m_qty . '</td>';
          } else {
            echo '<td style="text-align:right;">' . $m_qty . '</td>';
          }
        } else {
          echo '<td style="text-align:right;color:red;">Missing</td>';
        }
      }
      foreach ($rs['optional_platform'] as $product_code => $product) {
        if (isset($customer['consumed'][$product['sku_uid']])) {
          $m_qty = $customer['consumed'][$product['sku_uid']];
          if ($qty > $m_qty) {
            echo '<td style="color:red;text-align:right;">' . $m_qty . '</td>';
          } else {
            echo '<td style="text-align:right;">' . $m_qty . '</td>';
          }
        } else {
          echo'<td></td>';
        }
      }
      echo '</tr>';
    }

    ?>
  </tbody></table>
  <?php
}

/**
 * displays the auditing report filters
 * sets value if any filter already active
 */
function build_audit_report_header($currentDay = false) {
  $fromText = "From ";
  if ($currentDay) {
    $fromText = "Select Day ";
  }
  $from = filter_input(INPUT_GET, 'from', FILTER_SANITIZE_STRING);
  $to = filter_input(INPUT_GET, 'to', FILTER_SANITIZE_STRING);
  echo "From <input type='text' class='textbox' style='width: 75px;' id='from' name='from' " . (!empty($from) ? "value='{$from}'" : '') . "/>";
  if (!$currentDay) {
    echo "      To <input type='text'  class='textbox'  style='width: 75px;' id='to' name='to' " . (!empty($to) ? "value='{$to}'" : '') . "/></td>";
  }
  echo '</form><script type="text/javascript" src="javascripts/auditing_report.js"></script></td>';
  if ($currentDay && empty($from)) {
    echo '<script type="text/javascript"> REPORT_ARGS.init(["currentday"]); </script>';
  }
}

function build_usage_report_header($currentDay, $phoneHomeToggle, $othersToggle, $dateType) {
  $checkedPhoneHome = "";
  if ($phoneHomeToggle) {
    $checkedPhoneHome = " checked='checked' ";
  }
  $checkedOthers = "";
  if ($othersToggle) {
    $checkedOthers = " checked='checked' ";
  }
  $reportedDateSelected = ($dateType === "reported_date") ? " selected" : "";
  $reportDateSelected = ($dateType === "report_date") ? " selected" : "";
  echo "Included:  Phone Home<input type='checkbox' onchange='toggleURLParam(\"phonehome\", \"phoneHomeChk\");' id='phoneHomeChk' {$checkedPhoneHome}/>";
  echo " Others<input type='checkbox' onchange='toggleURLParam(\"others\", \"othersChk\");' id='othersChk' {$checkedOthers}/><br/>Date: ";
  echo "<select name='dateType' id='dateType' onchange='toggleURLParam(\"dateType\", \"dateType\");'><option value='reported_date'{$reportedDateSelected}>Accept Date</option><option value='report_date'{$reportDateSelected}>Email Date</option></select>";
  build_audit_report_header($currentDay);
}

function build_audit_report_body($rs) {

  ?>
  <table class='sort'>
  <thead>
  <tr><th colspan='13'>Auditing Report</th></tr>
  <tr class="rowHeaders"><td colspan="5"></td>
  <td colspan="3">Opportunities</td>
  <td colspan="3">Licenses</td>
  <td colspan="2">Difference</td></tr>
  <tr class="rowHeaders"><td>Customer ID</td><td>Customer Name</td>
  <td>Product Code</td><td>Product Name</td><td>Product Type</td>
  <td>BOP Qty</td><td>EOP Qty</td><td>Delta(%)</td>
  <td>BOP Shipped(%)</td><td>EOP Shipped(%)</td><td>Delta(%)</td>
  <td>BOP Qty - BOP Shipped</td><td>EOP Qty - EOP Shipped</td></tr>
  </thead>
  <tbody>
    <?php
    foreach ($rs['customers'] as $customer) {
      $customer_name = $rs['customers_table'][$customer]['customer_name'];
      if (isset($rs['customers_products'][$customer])) {
        $buffer = '';
        $bop_qty_total = 0;
        $eop_qty_total = 0;
        $bop_ship_total = 0;
        $eop_ship_total = 0;
        $customer_id = '<a href="index.php?operation=licinventory&inv_action=skus_summary&customer_filter=' . $customer . '" target="_blank">' . $customer . '</a>';
        $customer_name = '<a href="index.php?operation=licinventory&inv_action=skus_summary&customer_filter=' . $customer . '" target="_blank">' . $customer_name . '</a>';
        //customer link
        //journal link filtered to opportunities and customer and report dates
        //journal link filtered to license and dates from today to a month before the report

        foreach ($rs['customers_products'][$customer] as $sku_uid => $sku) {

          $product_code = $rs['skus'][$sku_uid]['product_code'];
          $product_name = $rs['skus'][$sku_uid]['sku_name'];
          $product_type = $rs['skus'][$sku_uid]['sku_type'];
          $bop_qty = (!empty($rs['opportunities']['from'][$customer]['products'][$sku_uid]) ? $rs['opportunities']['from'][$customer]['products'][$sku_uid] : 0);
          $bop_qty_total += $bop_qty;
          $eop_qty = (!empty($rs['opportunities']['to'][$customer]['products'][$sku_uid]) ? $rs['opportunities']['to'][$customer]['products'][$sku_uid] : 0);
          $eop_qty_total += $eop_qty;
          $bop_ship = (!empty($rs['clusters'][$customer]['total_start'][$sku_uid]) ? $rs['clusters'][$customer]['total_start'][$sku_uid] : 0);
          $bop_ship_total += $bop_ship;
          $eop_ship = (!empty($rs['clusters'][$customer]['total_end'][$sku_uid]) ? $rs['clusters'][$customer]['total_end'][$sku_uid] : 0);
          $eop_ship_total += $eop_ship;
          $eop_class = $bop_class = " class='ship_complete' ";
          if ($bop_qty > ($bop_ship * 1.01)) {
            $bop_class = " class='under_ship' ";
          }
          if ($bop_qty < ($bop_ship * .99)) {
            $bop_class = " class='overship' ";
          }
          if ($eop_qty > ($eop_ship * 1.01)) {
            $eop_class = " class='under_ship' ";
          }
          if ($eop_qty < ($eop_ship * .99)) {
            $eop_class = " class='overship' ";
          }
          $buffer .= "<tr class='theader_hidden' style='text-align:right;' data-for='$customer' ><td style='text-align:left;' >$customer_id</td><td style='text-align:left;'>$customer_name</td>"
            . "<td style='text-align:left;'>$product_code</td><td style='text-align:left;'>$product_name</td><td style='text-align:left;'>$product_type</td>"
            . "<td>" . number_format($bop_qty, 0, '.', ',') . "</td><td>" . number_format($eop_qty, 0, '.', ',') . "</td><td>" . number_format(($eop_qty - $bop_qty), 0, '.',
              ',') . "(" . number_format(($bop_qty !== 0 ? ($eop_qty - $bop_qty) / $bop_qty : 0) * 100, 1, '.', ',') . "%)</td>"
            . "<td>" . number_format($bop_ship, 0, '.', ',') . "(<span $bop_class>" . number_format(($bop_qty !== 0 ? $bop_ship / $bop_qty : 0) * 100, 1, '.', ',') . "%</span>)</td><td>" . number_format($eop_ship,
              0, '.', ',') . "(<span $eop_class>" . number_format(($eop_qty !== 0 ? $eop_ship / $eop_qty : 0) * 100, 1, '.', ',') . "%</span>)</td><td>" . number_format(($eop_ship - $bop_ship),
              0, '.', ',') . "(" . number_format(($bop_ship !== 0 ? ($eop_ship - $bop_ship) / $bop_ship : 0) * 100, 1, '.', ',') . "%)</td>"
            . "<td>" . number_format(($bop_qty - $bop_ship), 0, '.', ',') . "</td><td>" . number_format(($eop_qty - $eop_ship), 0, '.', ',') . "</td></tr>";
        }

        $eop_total_class = $bop_total_class = " class='ship_complete' ";
        if ($bop_qty_total > ($bop_ship_total * 1.01)) {
          $bop_total_class = " class='under_ship' ";
        }
        if ($bop_qty_total < ($bop_ship_total * .99)) {
          $bop_total_class = " class='overship' ";
        }
        if ($eop_qty_total > ($eop_ship_total * 1.01)) {
          $eop_total_class = " class='under_ship' ";
        }
        if ($eop_qty_total < ($eop_ship_total * .99)) {
          $eop_total_class = " class='overship' ";
        }

        echo "<tr class='theader' style='font-weight:bold;text-align:right;' id='$customer'><td style='text-align:left;'><img id='img_" . $customer . "' src='img/Add.png' />$customer_id</td><td style='text-align:left;'>$customer_name</td>"
        . "<td colspan='3' style='text-align:center;'>Total</td>"
        . "<td>" . number_format($bop_qty_total, 0, '.', ',') . "</td><td>" . number_format($eop_qty_total, 0, '.', ',') . "</td><td>" . number_format(($eop_qty_total - $bop_qty_total),
          0, '.', ',') . "(" . number_format(($bop_qty_total !== 0 ? ($eop_qty_total - $bop_qty_total) / $bop_qty_total : 0) * 100, 1, '.', ',') . "%)</td>"
        . "<td>" . number_format($bop_ship_total, 0, '.', ',') . "(<span $bop_total_class>" . number_format(($bop_qty_total !== 0 ? $bop_ship_total / $bop_qty_total : 0) * 100,
          1, '.', ',') . "%</span>)</td><td>" . number_format($eop_ship_total, 0, '.', ',') . "(<span $eop_total_class>" . number_format(($eop_qty_total !== 0 ? $eop_ship_total / $eop_qty_total : 0) * 100,
          1, '.', ',') . "%</span>)</td><td>" . number_format(($eop_ship_total - $bop_ship_total), 0, '.', ',') . "(" . number_format(($bop_ship_total !== 0 ? ($eop_ship_total - $bop_ship_total) / $bop_ship_total : 0) * 100,
          1, '.', ',') . "%)</td>"
        . "<td>" . number_format(($bop_qty_total - $bop_ship_total), 0, '.', ',') . "</td><td>" . number_format(($eop_qty_total - $eop_ship_total), 0, '.', ',') . "</td></tr>";
        echo $buffer;
        unset($buffer);
      }
    }

    ?>
  </tbody>
  </table>
  <script>
    $(".theader").click(function () {
      $("[data-for=" + this.id + "]").slideToggle("slow");
      var src = $("#img_" + this.id).attr("src");
      if (src === 'img/minus.png') {
        src = 'img/Add.png';
      } else {
        src = 'img/minus.png';
      }
      $("#img_" + this.id).attr("src", src);
    });
  </script>

  <?php
}

function build_vm_report_body($rs) {
  $vmTable = new TableView('vmReportView');
  $report_title = array(array('content' => '<h5>&raquo; VM Reporting (clones)</h5>', 'type' => 'th', 'align' => 'left', 'colspan' => 9));
  $titles = array(
    array('content' => 'Customer ID', 'type' => 'th', 'align' => 'left'),
    array('content' => 'Customer Name', 'type' => 'th', 'align' => 'left'),
    array('content' => 'System', 'type' => 'th', 'align' => 'left'),
    array('content' => 'Cluster', 'type' => 'th', 'align' => 'left'),
    array('content' => 'Cluster Type', 'type' => 'th', 'align' => 'left'),
    array('content' => 'Host ID', 'type' => 'th', 'align' => 'left'),
    array('content' => 'Date Reported', 'type' => 'th', 'align' => 'left'),
    array('content' => 'Reported Count', 'type' => 'th', 'align' => 'right'),
    array('content' => 'Report', 'type' => 'th', 'align' => 'right')
  );
  $vmTable->addRow($report_title, 'thead')
    ->addRow($titles, 'thead');

  if (!empty($rs)) {
    foreach ($rs as $report) {
      $url = "show_license_report.php?reportArchiveUID=" . $report['report_uid'];
      $link = '<a href="' . $url . '" target="_blank" onClick="javascript:openBlankWindow(\'' . $url . '\', \'TEST\');return false;">'
        . $report['report_uid'] . "</a>";
      $report_link = '<span  class="ReportInfo">' . $link . '</span>';
      $render = array(
        array('content' => $report['customer_id'], 'align' => 'left'),
        array('content' => $report['customer_name'], 'align' => 'left'),
        array('content' => $report['system_name'], 'align' => 'left'),
        array('content' => $report['as_cluster_name'], 'align' => 'left'),
        array('content' => $report['server_type'], 'align' => 'left'),
        array('content' => $report['host_id'], 'align' => 'left'),
        array('content' => $report['date_reported'], 'align' => 'left'),
        array('content' => $report['reporting_count'], 'align' => 'right'),
        array('content' => $report_link, 'align' => 'right')
      );
      $vmTable->addRow($render, 'tbody');
    }
  }
  $vmTable->renderTable();
  echo '<script type="text/javascript" src="javascripts/vm_report_view.js"></script>';
}

function build_email_users_form($data) {

  ?>
  <form name="emailUsers" method="post" action="license_mgr_update.php" onsubmit="return checkEmailForm();">
  <tr>
  <td colspan="2"><h2>Email LRS Users</h2></td>
  </tr>
  <tr>
  <td colspan="1"><input type="checkbox" onclick="return adjustEmailGroups('ALL');" name="ALL" id="ALL" />ALL</td>
  <td colspan="2"><input type="checkbox" onclick="return adjustEmailGroups('recent');" name="recent" id="recent" />Recent Users Only (past two months)</td>
  </tr>
  <tr>
  <td ><br><h4>Role-Specific:</h4></td><td ><br><h4>Mailing List:</h4></td>
  </tr>
  <?php
  $max = max(sizeof($data['roles']), sizeof($data['mailing_lists']));
  for ($i = 0; $i <= $max; $i++) {
    echo '<tr>';
    if (isset($data['roles'][$i])) {
      echo '<td>';
      echo '<input type="checkbox" onclick="return adjustEmailGroups();" id="check_' . $data['roles'][$i] . '" name="check_' . $data['roles'][$i] . '" /> ' . preg_replace('/session_role/',
        '', $data['roles'][$i]);
      echo '</td>';
    } else {
      echo '<td></td>';
    }
    if (isset($data['mailing_lists'][$i])) {
      echo '<td>';
      echo '<input type="checkbox" onclick="return adjustEmailGroups();" id="mailing_' . $data['mailing_lists'][$i] . '" name="mailing_' . $data['mailing_lists'][$i] . '" /> ' . preg_replace('/session_role/',
        '', $data['mailing_lists'][$i]);
      echo '</td>';
    } else {
      echo '<td></td>';
    }
    echo '</tr>';
  }

  ?>
  <tr><td width="100%" colspan="2"> (note: you can combine Role-Specific and Mailing Lists)</td></tr>
  <tr>
  <td width="100%" colspan="2"><br>Subject:<input type="text" name="subject" id="subject" size=80 maxLength="120"></td>
  </tr>
  <tr>
  <td width="100%" colspan="2"><br>Content:<br><textarea rows="10" cols="100" name="content" id="content"></textarea></td>
  </tr>
  <tr>
  <td width="100%" colspan="2"><input type="checkbox" name="HTML" id="HTML" />Send as HTML (note: &lt;html&gt;&lt;body&gt; and &lt;&#47;body&gt;&lt;&#47;html&gt; will be added around your content)</td>
  </tr>
  <tr>
  <td><br><?php
    echo submit_button('Send Email') . hidden_fn('sendEmail');

    ?><td>
  </tr>
  </form>
  <?php
  }

//end build_email_users_form

  function build_sox_controls_form($data) {

    ?>
    <form name="soxControls" method="post" action="license_mgr_update.php" onsubmit="return true;">
    <tr>
      <td><h2>Generate SOX Controls</h2></td>
      </tr>
      <tr>
      <td>
        <span>Last X months</span>
        <select name="lastMonths">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3" selected>3</option>
        <option value="6">6</option>
        <option value="12">12</option>
        </select>
        </td>
        </tr>
        <tr>
          <td>
          <br>
        <?php
        echo submit_button('Download') . hidden_fn('downloadSoxControls');

        ?>
        <td>
        </tr>
    </form>
    <?php
  }

  function build_auditing_servers_report_view($customers, $product_server_matrix) {
  $util = new Utility();
  $AuditingServersTable = new TableView('auditingServersView');
  $server_types = array_keys($product_server_matrix['server_types']);
  sort($server_types);
  $tempTitles = array('Customer Name', 'Customer ID', 'Release', 'Product Code', 'Product Name', 'Bought Quantity');
  $titles = array();
  foreach (array_merge($tempTitles, $server_types) as $tempTitle) {
    $titles[] = array('content' => $tempTitle, 'type' => 'th', 'align' => 'left');
  }
  $columns = sizeof($titles);
  $report_title = array(array('content' => '<h5>&raquo; Servers Delivered by Product</h5>', 'type' => 'th', 'align' => 'left', 'colspan' => $columns));
  $AuditingServersTable->addRow($report_title, 'thead')
    ->addRow($titles, 'thead');
  foreach ($customers as $customer) {
    $customer_release = $customer['max_release'];
    $prov_link = $util->return_customer_inventory_name_link($customer['customer_id'], $customer['customer_name']);
    if (empty($customer_release)) {
      //single row with could not calculate customer release
      $colspan = $columns - 3;
      $render = array(array('content' => $prov_link, 'align' => 'left'),
        array('content' => $customer['customer_id'], 'align' => 'left'),
        array('content' => 'Not set', 'align' => 'left'),
        array('content' => '', 'colspan' => $colspan));
      $AuditingServersTable->addRow($render, 'tbody');
      continue;
    }
    $product_release_select = $product_server_matrix['R' . $customer_release];
    foreach ($customer['inventory'] as $product_code => $product_content) {
      if (!isset($product_release_select[$product_content['sku_type']][$product_code])) {
        continue;
      }
      $cust_server_types = (isset($customer['completed_servers'][$product_content['sku_type']]) ? $customer['completed_servers'][$product_content['sku_type']] : null);
      $completed_product_servers = return_mandatory_server($product_release_select[$product_content['sku_type']][$product_code], $cust_server_types);
      $empty = false;
      foreach ($completed_product_servers as $server_count) {
        if (empty($server_count)) {
          $empty = true;
        }
      }
      if ($empty) {
        $render = array(array('content' => $prov_link, 'align' => 'left'),
          array('content' => $customer['customer_id'], 'align' => 'left'),
          array('content' => 'R' . $customer_release, 'align' => 'left'),
          array('content' => $product_code, 'align' => 'left'),
          array('content' => $product_content['product_name'], 'align' => 'left'),
          array('content' => $product_content['bought_quantity'], 'align' => 'right'));
        foreach ($server_types as $server_type) {
          $count = (isset($completed_product_servers[$server_type]) ? $completed_product_servers[$server_type] : '');
          if ($count === 0) {
            $render[] = array('content' => $count, 'color' => 'red');
          } else {
            $render[] = array('content' => $count);
          }
        }
        $AuditingServersTable->addRow($render, 'tbody');
      }
    }
  }
  $AuditingServersTable->renderTable();
}

//end  build_temp_reporting

function build_view_groups($groups) {
  $groupTable = new TableView('tableView');
  $utils = new Utility();
  $report_title = array(array('content' => '<h5>&raquo; View Configured Groups</h5>', 'type' => 'th', 'align' => 'left', 'colspan' => 7));
  $titles = array(
    array('content' => 'Customer ID', 'type' => 'th'),
    array('content' => 'Customer Name', 'type' => 'th'),
    array('content' => 'System Type', 'type' => 'th'),
    array('content' => 'System Name', 'type' => 'th'),
    array('content' => 'Group', 'type' => 'th'),
    array('content' => 'Release', 'type' => 'th'),
    array('content' => 'Count of Clusters', 'type' => 'th')
  );
  $groupTable->addRow($report_title, 'thead')
    ->addRow($titles, 'thead');
  foreach ($groups as $group_uid => $row) {
    $link_params = array('operation' => 'licinventory',
      'customer_filter' => $row['customer_id'],
      'inv_action' => 'group_licenses',
      'group_uid' => $group_uid);
    $link = $utils->build_link($link_params, $row['customer_name']);
    $render = array(
      array('content' => $row['customer_id']),
      array('content' => $link),
      array('content' => $row['system_type']),
      array('content' => $row['system_name']),
      array('content' => $row['group_name']),
      array('content' => $row['group_software_release']),
      array('content' => $row['cluster_count'])
    );
    $groupTable->addRow($render, 'tbody');
  }
  $groupTable->renderTable();
}

//end build_view_groups

function return_mandatory_server($product, $completed_servers) {
  foreach ($product as $server_type => $product_server) {
    if (!empty($completed_servers[$server_type])) {
      $product[$server_type] = $completed_servers[$server_type];
    } else {
      $product[$server_type] = 0;
    }
  }
  return $product;
}

function build_reset_clones($clones) {
  $cloneTable = new TableView('resetClones');
  $utils = new Utility();
  $repTools = new ReportingTools();
  $cols = 11;
  $report_title = array(array('content' => '<h5>&raquo; Reset Clone status for lab clusters</h5>', 'type' => 'th', 'align' => 'left', 'colspan' => $cols));
  $titles = array(
    array('content' => 'Customer ID', 'type' => 'th'),
    array('content' => 'Customer Name', 'type' => 'th'),
    array('content' => 'System Type', 'type' => 'th'),
    array('content' => 'System Name', 'type' => 'th'),
    array('content' => 'Cluster Name', 'type' => 'th'),
    array('content' => 'Last Generated Date', 'type' => 'th', 'align' => 'right'),
    array('content' => 'Expiring Date', 'type' => 'th', 'align' => 'right'),
    array('content' => 'Max Expire Date', 'type' => 'th', 'align' => 'right'),
    array('content' => 'Cloned Release', 'type' => 'th', 'align' => 'right'),
    array('content' => 'Regeneration Count', 'type' => 'th', 'align' => 'right'),
    array('content' => 'Select', 'type' => 'th', 'align' => 'center'));
  $cloneTable->addRow($report_title, 'thead')->addRow($titles, 'thead');
  foreach ($clones as $row) {
    $cluster_uid = $row['as_cluster_uid'];
    $link_params = array('operation' => 'licinventory',
      'customer_filter' => $row['customer_id'],
      'inv_action' => 'cluster_licenses',
      'as_cluster_uid' => $cluster_uid);
    $link = $utils->build_link($link_params, $row['customer_name']);
    $checkbox = "<input class='clusters_to_reset' name='clusters_to_reset' id='clusters_to_reset[]' type='checkbox' value='$cluster_uid|{$row['acd_ac_software_release']}'>";
    $render = array(
      array('content' => $row['customer_id']),
      array('content' => $link),
      array('content' => $row['system_type']),
      array('content' => $row['system_name']),
      array('content' => $row['as_cluster_name']),
      array('content' => date('m/d/Y', strtotime($row['acd_prod_as_last_date'])), 'align' => 'right'),
      array('content' => date('m/d/Y', strtotime($row['acd_prod_as_expire_current_date'])), 'align' => 'right'),
      array('content' => date('m/d/Y', strtotime($row['acd_prod_as_expire_max_date'])), 'align' => 'right'),
      array('content' => $row['acd_ac_software_release'], 'align' => 'right'),
      array('content' => $row['acd_prod_as_count'], 'align' => 'right'),
      array('content' => $checkbox, 'align' => 'center'));
    $cloneTable->addRow($render, 'tbody');
  }
  $submitRow = array(array('content' => '<input type="button" name="action" value="Reset Selected Clusters" onclick="send_reset_clones()" class="button">', 'colspan' => $cols, 'align' => 'center'));
  $cloneTable->addRow($submitRow, 'tbody');
  $repTools->form_header('resetCloneStatus', 'CR validate', 'post', 'license_mgr_update.php');
  $cloneTable->renderTable();
  echo '</form>';
}

/**
 * build the main table of NFM Releases
 * @param array $nfm_releases
 */
function build_nfm_releases($nfm_releases) {
  $nfmReleaseTable = new TableView('nfmReleases');
  $utils = new Utility();
  $repTools = new ReportingTools();
  $cols = 3;
  $report_title = array(array('content' => '<h5>&raquo; NFM Releases & Tokens Management</h5>', 'type' => 'th', 'align' => 'left', 'colspan' => $cols));
  $titles = array(
    array('content' => 'NFM Release', 'type' => 'th'),
    array('content' => 'NFM Token', 'type' => 'th'),
    array('content' => 'Action', 'type' => 'th')
  );
  $nfmReleaseTable->addRow($report_title, 'thead')->addRow($titles, 'thead');
  foreach ($nfm_releases as $row) {
    $link_params = array('operation' => 'nfmRelease',
      'edit' => $row['nr_nfm_release_uid']);
    $link = $utils->build_link($link_params, "edit", "_self");
    $render = array(
      array('content' => $row['nr_nfm_release_name']),
      array('content' => $row['nr_nfm_release_token'], 'class' => 'forcewrap'),
      array('content' => $link),
    );
    $nfmReleaseTable->addRow($render, 'tbody');
  }
  $repTools->form_header('resetCloneStatus', 'CR validate', 'post', 'license_mgr_update.php');
  $nfmReleaseTable->renderTable();
  echo '</form>';
}

/**
 * build the edit / add section of the NFM Releases
 * @param string $mode
 * @param array $nfm_releases
 * @param int $nfm_release_uid
 */
function build_nfm_releases_edit($mode, $nfm_releases, $nfm_release_uid = null) {
  $repTool = new ReportingTools();
  echo $repTool->form_header("nfm_release_edit", "", 'post', 'license_mgr_update.php');
  $release = array();
  // invalid key submitted, lets revert to Add
  if (!array_key_exists($nfm_release_uid, $nfm_releases)) {
    $mode = "Add";
  }
  $submit = $mode;
  $header = "Add NFM Release";
  echo"<div class='editContent'>\n";
  $token = "";
  $name = "";
  switch ($mode) {
    case "Edit":
      $release = $nfm_releases[$nfm_release_uid];
      $submit = 'edit';
      $token = $release['nr_nfm_release_token'];
      $name = $release['nr_nfm_release_name'];
      $header = "Edit Release {$name}";
      break;
    case "Add":
    default:
  }
  $repTool->outputHeader($header, 'smallHeader');
  $repTool->outputLabelAndValue('Name', $repTool->getTextInput('var_name', "text", 'var_name', null, null, $name));
  $repTool->outputLabelAndValue('NFM Release Token', $repTool->getTextArea('var_content', 'var_content', 80, 10, 'text', $token, 4096));
  $repTool->outputLabelAndValue('', $repTool->getTextInput('submit', 'submit', 'submit', '50px', 'button', $submit));
  echo hidden_fn('nfm_release_edit', 'fn', 'fn');
  if ($mode == "Edit") {
    echo hidden_fn($release['nr_nfm_release_uid'], 'var_uid', 'var_uid');
  }
  echo"</form>\n";
  echo"</div>\n";
}

function build_license_generation_reasons($reasons, $from, $to) {

  ?><input type="button" name="" class="button" value="Show/Hide Decommissioned" onclick="hideDecommisionned();"><?php
  $table = new TableView('lic_gen_reasons', 'sort', '75%');
  $utils = new Utility();
  $report_title = array(array('content' => '<h5>&raquo; View License Generation Reasons</h5>', 'type' => 'th', 'align' => 'left', 'colspan' => 3));
  $titles = array(
    array('content' => 'Reason', 'type' => 'th', 'align' => 'left'),
    array('content' => '# used', 'type' => 'th'),
    array('content' => 'details', 'type' => 'th')
  );
  $table->addRow($report_title, 'thead')
    ->addRow($titles, 'thead');
  foreach ($reasons as $reason => $licenses) {
    $link_params = array('operation' => 'licgen_reasons',
      'from' => $from,
      'to' => $to,
      'reason' => urlencode($reason));
    $link = $utils->build_link($link_params, 'detailed_report', '_self');
    $render = array(
      array('content' => $reason, 'align' => 'left'),
      array('content' => sizeof($licenses), 'align' => 'right'),
      array('content' => $link)
    );
    $table->addRow($render, 'tbody');
  }
  $table->renderTable();
}

function build_license_generation_reasons_details($report, $title) {
  echo '<br>';
  $table = new TableView('lic_gen_reasons_details', 'sort', '75%');
  $utils = new Utility();
  $report_title = array(array('content' => '<h5>&raquo; Reason: ' . $title . '</h5>', 'type' => 'th', 'align' => 'left', 'colspan' => 6));
  $titles = array(
    array('content' => 'Customer', 'type' => 'th', 'align' => 'left'),
    array('content' => 'System', 'type' => 'th', 'align' => 'left'),
    array('content' => 'Cluster', 'type' => 'th', 'align' => 'left'),
    array('content' => 'Version', 'type' => 'th', 'align' => 'left'),
    array('content' => 'User Email', 'type' => 'th', 'align' => 'left'),
    array('content' => 'Date', 'type' => 'th', 'align' => 'right'),
  );
  $table->addRow($report_title, 'thead')
    ->addRow($titles, 'thead');
  foreach ($report as $license) {
    $link_params = array('operation' => 'licinventory',
      'customer_filter' => $license['customer_id'],
      'inv_action' => 'archive_licenses');
    $link = $utils->build_link($link_params, "{$license['customer_name']} ({$license['customer_id']})");
    $render = array(
      array('content' => $link, 'align' => 'left'),
      array('content' => $license['system_name'], 'align' => 'left'),
      array('content' => "{$license['cluster_name']} ({$license['as_cluster_uid']})", 'align' => 'left'),
      array('content' => $license['bw_version'], 'align' => 'left'),
      array('content' => $license['email'], 'align' => 'left'),
      array('content' => $license['generated_on_date'], 'align' => 'left')
    );
    $table->addRow($render, 'tbody');
  }
  $table->renderTable();
}

function cleanDate($date) {
  if (empty($date)) {
    return "";
  }
  return array_shift(explode(" ", $date));
}

function build_usage_report_view($reports) {
  $table = new TableView('usage_report_table', 'sort', '100%');
  $rt = new ReportingTools();
  $link = new Link();
  $util = new Utility();
  $report_title = array(array('content' => '<h5>&raquo; Usage Report</h5>', 'type' => 'th', 'align' => 'left', 'colspan' => 9));
  $titles = array(
    array('content' => $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'customer_name'), "Customer"), 'type' => 'th', 'align' => 'left'),
    array('content' => $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'system_name'), "System"), 'type' => 'th', 'align' => 'left'),
    array('content' => $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'as_cluster_name'), "Cluster"), 'type' => 'th', 'align' => 'left'),
    array('content' => $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'reported_date'), "Accept Date *"), 'type' => 'th', 'align' => 'left'),
    array('content' => $link->getLink($_SERVER['PHP_SELF'] . $util->getSortingUrl($_SERVER['QUERY_STRING'], 'report_date'), "Email Date *"), 'type' => 'th', 'align' => 'left'),
    array('content' => 'Sellthrough Year-Month', 'type' => 'th', 'align' => 'left'),
    array('content' => 'Phone Home / Other', 'type' => 'th', 'align' => 'center'),
    array('content' => 'First Lines ', 'type' => 'th', 'align' => 'center'),
    array('content' => 'Tech Support File', 'type' => 'th', 'align' => 'right'),
  );
  $table->addRow($report_title, 'thead')
    ->addRow($titles, 'thead');
  foreach ($reports as $report) {
    //http://localhost/php/techSupportReport/show_license_report.php?reportArchiveUID=841428
    $url = "show_license_report.php?reportArchiveUID=" . $report['report_info']['report_uid'];
    $link = '<a href="' . $url . '" target="_blank" onClick="javascript:openBlankWindow(\'' . $url . '\', \'TEST\');return false;">'
      . $report['report_info']['report_uid'] . "</a>";
    $report_link = '<span  class="ReportInfo">' . $link . '</span>';
    $render = array(
      array('content' => "{$report['report_info']['customer_name']} ({$report['report_info']['customer_id']})", 'align' => 'left'),
      array('content' => $report['report_info']['system_name'], 'align' => 'left'),
      array('content' => $report['report_info']['as_cluster_name'], 'align' => 'left'),
      array('content' => cleanDate($report['report_info']['reported_date']), 'align' => 'right'),
      array('content' => cleanDate($report['report_info']['report_date']), 'align' => 'right'),
      array('content' => "{$report['report_info']['report_year']} / {$report['report_info']['report_month']}", 'align' => 'right'),
      array('content' => $report['report_info']['ra_sender_email'] === "bwlopsManager@broadsoft.com" ? "Phone Home" : "Other"),
      array('content' => nl2br(implode($report['report_lines'])), 'align' => 'left'),
      array('content' => $report_link, 'align' => 'right')
    );
    $table->addRow($render, 'tbody');
  }
  $table->renderTable();
  $rt->outputLabelAndValue("* Accept Date: ", "is the date of the last report process for the current month reporting (sell thru, etc...)");
  $rt->outputLabelAndValue("* Email Date: ", "the day the email was received");
  $rt->addDownloadXLS()
    ->addDownloadCSV()
    ->closeForm();
}

function build_is_banners($data) {
  $link = new Link();
  $repTool = new ReportingTools();
  echo $repTool->form_header("is_banner_delete", "", 'post', 'license_mgr_update.php');
  $table = new TableView('is_banner_table', 'sort', '75%');
  $report_title = array(array('content' => '<h5>&raquo; IS Banners / Config</h5>', 'type' => 'th', 'align' => 'left', 'colspan' => 4));
  $titles = array(
    array('content' => 'Entry', 'type' => 'th', 'align' => 'left'),
    array('content' => 'Value', 'type' => 'th', 'align' => 'left', 'width' => '75%'),
    array('content' => 'Edit', 'type' => 'th', 'align' => 'right'),
    array('content' => 'Clear', 'type' => 'th', 'align' => 'right'),
  );
  $table->addRow($report_title, 'thead')
    ->addRow($titles, 'thead');
  foreach ($data as $name => $value) {
    $url = filter_input(INPUT_SERVER, 'PHP_SELF');
    $url .= "?operation=is_banner&edit={$name}";
    $delete = "<input type='checkbox' name='names[]' value='{$name}'>";
    if ($name === "server status") {
      $delete = "";
    }
    $render = array(
      array('content' => "{$name}", 'align' => 'left'),
      array('content' => "{$value}", 'align' => 'left', 'width' => '75%'),
      array('content' => $link->getLink($url, 'edit'), 'align' => 'right'),
      array('content' => $delete, 'align' => 'right')
    );

    $table->addRow($render, 'tbody');
  }
  $table->renderTable();
  echo hidden_fn('is_banner_delete', 'fn', 'fn');
  $repTool->outputLabelAndValue('', $repTool->getTextInput('submit', 'submit', 'submit', '50px', 'button', 'Clear'));
  echo"</form>\n";
  echo"<br><br>\n";
}

function build_is_banners_edit($data, $entry) {
  $repTool = new ReportingTools();
  echo $repTool->form_header("is_banner_edit", "", 'post', 'license_mgr_update.php');
  $content = "";
  $submit = 'add';
  $header = "Add Config Entry";
  echo"<div class='editContent'>\n";
  $edit = array_key_exists($entry, $data);
  if ($edit) {
    $content = $data[$entry];
    $submit = 'edit';
    $header = "Edit {$entry}";
  }
  $repTool->outputHeader($header, 'smallHeader');
  if (!$edit) {
    $repTool->outputLabelAndValue('Name', $repTool->getTextInput('var_name', "text", 'var_name'));
  }
  $repTool->outputLabelAndValue('Content', $repTool->getTextArea('var_content', 'var_content', 80, 6, 'text', $content, 512));
  $repTool->outputLabelAndValue('', $repTool->getTextInput('submit', 'submit', 'submit', '50px', 'button', $submit));
  echo hidden_fn('is_banner_edit', 'fn', 'fn');
  if ($edit) {
    echo hidden_fn($entry, 'var_name', 'var_name');
  }
  echo"</form>\n";
  echo"</div>\n";
}

function build_blue_banners($customer_infos) {
  $link = new Link();
  $repTool = new ReportingTools();
  echo $repTool->form_header("blue_banner_delete", "", 'post', 'license_mgr_update.php');
  $table = new TableView('blue_banner_table', 'sort', '75%');
  $report_title = array(array('content' => '<h5>&raquo; Customer Blue Banner</h5>', 'type' => 'th', 'align' => 'left', 'colspan' => 4));
  $titles = array(
    array('content' => 'Customer Name', 'type' => 'th', 'align' => 'left'),
    array('content' => 'Customer Note', 'type' => 'th', 'align' => 'left', 'width' => '75%'),
    array('content' => 'Edit', 'type' => 'th', 'align' => 'right'),
    array('content' => 'Delete', 'type' => 'th', 'align' => 'right'),
  );
  $table->addRow($report_title, 'thead')
    ->addRow($titles, 'thead');
  foreach ($customer_infos as $customer_info) {
    $url = filter_input(INPUT_SERVER, 'PHP_SELF');
    $url .= "?operation=blue_banner&edit=1&customer_filter={$customer_info['customer_id']}";
    $delete = "<input type='checkbox' name='ids[]' value='{$customer_info['customer_id']}'>";
    $render = array(
      array('content' => "{$customer_info['customer_name']} ({$customer_info['customer_id']})", 'align' => 'left'),
      array('content' => "{$customer_info['c_blue_banner']}", 'align' => 'left', 'width' => '75%'),
      array('content' => $link->getLink($url, 'edit'), 'align' => 'right'),
      array('content' => $delete, 'align' => 'right')
    );

    $table->addRow($render, 'tbody');
  }
  $table->renderTable();
  echo hidden_fn('blue_banner_delete', 'fn', 'fn');
  $repTool->outputLabelAndValue('', $repTool->getTextInput('submit', 'submit', 'submit', '50px', 'button', 'delete'));
  echo"</form>\n";
  echo"<br><br>\n";
}

function build_blue_banners_edit($customer_info, $edit = null) {
  $repTool = new ReportingTools();
  echo $repTool->form_header("blue_banner_edit", "", 'post', 'license_mgr_update.php');
  $submit = 'add';
  $header = "Add Customer Blue Banner";
  echo"<div class='editContent'>\n";
  if (!empty($customer_info['c_blue_banner'])) {
    $content = $customer_info['c_blue_banner'];
    $submit = 'edit';
    $header = "Edit Customer Blue Banner";
  }
  $repTool->outputHeader($header, 'smallHeader');

  $repTool->outputLabelAndValue('Content', $repTool->getTextArea('banner_content', 'banner_content', 80, 6, 'text', $content, 512));
  $repTool->outputLabelAndValue('', $repTool->getTextInput('submit', 'submit', 'submit', '50px', 'button', $submit));
  echo hidden_fn('blue_banner_edit', 'fn', 'fn');
  if (isset($customer_info['customer_id'])) {
    echo hidden_fn($customer_info['customer_id'], 'customer_id', 'customer_id');
  }
  echo"</form>\n";
  echo"</div>\n";
}
