<?php

/**
 * Body
 * Provides functions for outputting HTML body
 *
 * @package
 * @author tpilote
 * @copyright Copyright (c) 2010
 * @version $Id$
 * @access public
 */
class Body extends AbstractClass {

  var $operation;
  var $action;

  /**
   *
   * @var Pager
   */
  var $pager;

  /**
   *
   * @var GenericLicensingDB
   */
  var $db;
  var $licgen;
  var $cause;
  private $customVert;
  private $customOrderBy;
  var $tables;

  /**
   * Body::Body()
   *
   * @param mixed $operation
   * @param mixed $action
   */
  function __construct($context, $operation, $action) {
    parent::__construct($context);
    $this->tables = Tables::getTables();
    $this->operation = $operation;
    $this->action = $action;
    $this->pager = new Pager();
    $this->pager->setTextStyle('font-size: 10px;');
    $this->pager->setTbClass('textbox');
    $this->db = $this->getContext()->getGenericLicensingDB();
    $this->licgen = $this->getContext()->getLicenseGenerationDB();
  }

  /**
   * Body::build_body()
   *
   * @return
   */
  function build_body() {
    echo '<td style="padding-left: 5px; vertical-align: top;">
      <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
    <tr>
          <td>';
    if (isset($this->operation)) {
      $this->log_activity();
    }
    $this->perform_action();
    echo '</td>
    </tr>
    </table>
    </td>';
  }

  /**
   * Body::perform_action()
   *
   * @return
   */
  function perform_action() {
    switch ($this->operation) {
      case 'customers':
        $this->handle_customers();
        break;
      case 'getUniqueNodeUUID':
        $this->getUniqueUUID();
        break;
      case 'sku_profile_import':
        $this->handle_sku_profile_import();
        break;
      case 'systems':
        $this->handle_systems();
        break;
      case 'asclusters':
        $this->handle_asclusters();
        break;
      case 'ashostids':
        $this->handle_ashostids();
        break;
      case 'uploadreport':
        $this->handle_uploadreport();
        break;
      case 'outdated_licenses':
        $this->handle_view_outdated_licenses();
        break;
      case 'duplicatedHostIDs':
        $this->handle_view_duplicated_hostids();
        break;
      case 'manageUsers':
        $this->handle_manageUsers();
        break;
      case 'unknownhostids':
        $this->handle_unknownhostids();
        break;
      case 'undelivered_inv':
        $this->handle_undelivered_inv();
        break;
      case 'teaser_profiles':
        $this->handle_teaser_profiles();
        break;
      case 'teaser_view':
        $this->handle_teaser_view();
        break;
      case 'product_profiles_view':
        $this->handle_product_profile_view();
        break;
      case 'reporting':
        $this->handle_reporting();
        break;
      case 'welcome':
        $this->handle_welcome();
        break;
      case 'git_log':
        $this->handle_git_log();
        break;
      case 'resync_systems':
        $this->handle_resync_tools();
        break;
      case 'licensingProgress':
        $this->handle_licensingProgress();
        break;
      case 'licensingProgressHistory':
        $this->handle_licensingProgressHistory();
        break;
      case 'journaling':
        $this->handle_journaling();
        break;
      case 'licentities':
        $this->handle_licensableEntities();
        break;
      case 'licinventory':
        $this->handle_licensableInventory();
        break;
      case 'licsign':
        $this->handle_licenseUploadAndSign();
        break;
      case 'licSfdcOpportunity':
        $this->handle_licenseSfdcOpportiuty();
        break;
      case 'licskus':
        if (!isset($_GET['ajax']))
          $this->handle_licensableSKUs(); // if a normal request for the page
        else
          $this->handle_filterSKUs();
        break;
      case 'licOvpReasons':
        $this->handle_licenseOverProvReasons();
        break;
      case 'licGenTemplates':
        if (!isset($_GET['ajax'])) {
          $this->handle_licenseLicGenTemplates();
        } else {
          $this->handle_licenseLicGenTemplates(true);
        }
        break;
      case 'licManualOpReasons':
        $this->handle_licenseManualOpReasons();
        break;
      case 'blue_banner':
        $this->handle_blueBanner();
        break;
      case 'is_banner':
        $this->handle_is_banners();
        break;
      case 'create_new_sku':
        build_sku_creation_form($this->db->getEnumValues($this->tables['SKUsTable']['tableName'], 'sku_type'));
        break;
      case 'mergeProfiles':
        $this->handle_merge_profiles();
        break;
      case 'create_new_sku_profile':
      case 'configureSKUProfile':
        $this->handle_sku_configuration();
        break;
      case 'configureLEReleases':
        $this->handle_configureLEReleases();
        break;
      case 'adminTools':
        $this->handle_admin_tools();
        break;
      case 'resetClone':
        $this->handle_reset_clones();
        break;
      case 'nfmRelease':
        $this->handle_nfm_releases();
        break;
      case 'ManagePermissions':
        $this->handle_manage_permissions();
        break;
      case 'ManageLists':
        $this->handle_manage_lists();
        break;
      case 'lpprovisioning':
      case 'lplicensing':
      case 'lpreporting':
      case 'lpadministration':
        $this->handle_generic_landing_page($this->operation);
        break;
      case 'isRegTestsLocked':
        $this->handle_is_reg_test_locked();
        break;
      case 'set_homepage':
        $this->handle_set_homepage();
        break;
      case 'reset_customers_view':
        $this->handle_reset_customers_view();
        break;
      case 'auditing_report_view':
        $this->handle_auditing_report_view();
        break;
      case 'usage_report_view':
        $this->handle_usage_report_view();
        break;
      case 'licgen_reasons':
        $this->handle_licgen_reasons_report_view();
        break;
      case 'vm_report_view':
        $this->handle_vm_report_view();
        break;
      case 'auditing_servers_report_view':
        $this->handle_auditing_servers_report_view();
        break;
      case 'view_groups':
        $this->handle_view_groups();
        break;
    }
  }

  /**
   * Body::handle_manageUsers()
   *
   * @return
   */
  function handle_manageUsers() {
    if (empty($_SESSION['canManageUsers'])) {
      print 'You do not have access to this page';
      return;
    }

    if (!empty($_GET['lname'])) {
      $num = $this->db->get_num_recs($this->tables['loginTable'], " lname=? ", array($_GET['lname']));
    } else if (!empty($_GET['fname'])) {
      $num = $this->db->get_num_recs($this->tables['loginTable'], " fname=? ", array($_GET['fname']));
    } else if (!empty($_GET['email'])) {
      $num = $this->db->get_num_recs($this->tables['loginTable'], " email=? ", array($_GET['email']));
    } else {
      $num = $this->db->get_num_recs($this->tables['loginTable']); // Get number of records
    }

    $templogins = $this->db->getUsers();
    $alllogins = array();
    foreach ($templogins as $templogin) {
      $alllogins['fname'][] = utf8_decode($templogin['fname']);
      $alllogins['lname'][] = utf8_decode($templogin['lname']);
      $alllogins['email'][] = $templogin['email'];
    }

    $this->pager->setTotRecords($num); // Pager method calls
    $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['loginTable']['tableKey']) : $this->tables['loginTable']['orderBy']);
    if (!empty($_GET['lname'])) {
      $logins = $this->db->get_all_paged_data($this->tables['loginTable'], $this->pager, $orders, " lname=?", array($_GET['lname']));
    } else if (!empty($_GET['fname'])) {
      $logins = $this->db->get_all_paged_data($this->tables['loginTable'], $this->pager, $orders, " fname=?", array($_GET['fname']));
    } else if (!empty($_GET['email'])) {
      $logins = $this->db->get_all_paged_data($this->tables['loginTable'], $this->pager, $orders, " email=?", array($_GET['email']));
    } else {
      $logins = $this->db->get_all_paged_data($this->tables['loginTable'], $this->pager, $orders);
    }
    for ($i = 0; $i < sizeof($logins); ++$i) {
      $logins[$i]['roles'] = $this->db->get_roles_by_memberid($logins[$i]['memberid']);
    }
    $roles = $this->db->get_roles();
    build_logins_body($this->pager, $logins, $this->db->getLastError(), $roles, $alllogins);
    $this->pager->printPages();

    $edit = (isset($_GET['memberid'])); // Determine if the form should contain values or be blank
    $rs = array();

    if ($edit) { // Validate customer_id
      $memberid = trim($_GET['memberid']);
      $rs = $this->db->get_all_properties($this->tables['loginTable'], $memberid);
    }
    if (isset($_SESSION['post'])) {
      $rs = $_SESSION['post'];
    }
    $activity = $this->db->get_user_activity();
    build_logins_edit($rs, $edit, $this->pager, $roles, $logins, $activity);
    unset($_SESSION['post'], $rs);
  }

  /**
   * Body::handle_licensableEntities()
   *
   * @return
   */
  function handle_licensableEntities() {
    $level_is_overriden = false;
    $num = $this->db->get_num_recs($this->tables['licensableEntitiesTable']); // Get number of records
    $this->pager->setTotRecords($num); // Pager method calls
    $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['licensableEntitiesTable']['tableKey']) : $this->tables['licensableEntitiesTable']['orderBy']);

    if (isset($_GET['licensable_entity_uid']))
      $rs['LEs'][0] = $this->db->get_all_properties($this->tables['licensableEntitiesTable'], $_GET['licensable_entity_uid']);
    else
      $rs['LEs'] = $this->db->get_all_paged_data($this->tables['licensableEntitiesTable'], $this->pager, $orders);

    $rs['leServerCompatibility'] = $this->db->get_all_paged_data($this->tables['licensable_entity_to_server_compatibility'], null, array('licensable_entity_uid'));

    $LEsForDropDown = $this->db->get_all_paged_data($this->tables['licensableEntitiesTable'], NULL, $orders);

    build_licensableEntities_body($this->pager, $rs, $this->db->getLastError(), $LEsForDropDown, isset($_GET['licensable_entity_uid']) ? $_GET['licensable_entity_uid'] : NULL);

    if (!isset($_GET['licensable_entity_uid']))
      $this->pager->printPages();

    $skus = array();
    $canDelete = false;
    if (isset($_GET['licensable_entity_uid'])) {
      $le_uid = $_GET['licensable_entity_uid'];

      $profilesRAW = $this->db->getRows('select sku_list.sku_name, sku_list.product_code, sku_content.sku_uid, sku_profiles.sku_profile_name, sku_profiles.sku_profile_uid, sku_profile_content_release_info.include_in_license, sku_profile_content_release_info.relative_quantity, sku_profile_content_release_info.pack_name, sku_profile_content_release_info.licensable_entity_override_level from sku_profile_content_release_info, sku_profiles, sku_list, sku_content, sku_profile_content where sku_profile_content.sku_content_uid=sku_content.sku_content_uid AND sku_content.licensable_entity_uid=' . $le_uid . ' and sku_list.sku_uid=sku_content.sku_uid and sku_profiles.sku_profile_uid=sku_profile_content.sku_profile_uid and sku_profile_content_release_info.sku_profile_content_uid=sku_profile_content.sku_profile_content_uid;');
      // crunch the raw dta:
      $profiles = array();
      foreach ($profilesRAW as $record) {
        if (isset($record['include_in_license']) && $record['include_in_license'] == '1')
          $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['included_sometimes'] = true;
        if (isset($record['include_in_license']) && $record['include_in_license'] == '0')
          $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['excluded_sometimes'] = true;

        $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['sku_name'] = $record['sku_name'];
        $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['product_code'] = $record['product_code'];
        $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['sku_uid'] = $record['sku_uid'];
        $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['sku_profile_uid'] = $record['sku_profile_uid'];
        $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['sku_profile_name'] = $record['sku_profile_name'];
        $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['licensable_entity_uid'] = $le_uid;

        if (!isset($profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['pack_name']))
          $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['pack_name'] = $record['pack_name'];
        elseif ($profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['pack_name'] != $record['pack_name'])
          $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['pack_name'] = 'release-specific';

        if (!isset($profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['relative_quantity']))
          $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['relative_quantity'] = $record['relative_quantity'];
        elseif ($profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['relative_quantity'] != $record['relative_quantity'])
          $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['relative_quantity'] = 'release-specific';

        if ($record['licensable_entity_override_level'] == NULL)
          $level = 'LE Default';
        else {
          $level = $record['licensable_entity_override_level'];
          $level_is_overriden = true;
        }
        if (!isset($profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['licensable_entity_override_level']))
          $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['licensable_entity_override_level'] = $level;
        elseif ($profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['licensable_entity_override_level'] != $level)
          $profiles[$record['product_code'] . '~' . $record['sku_profile_uid']]['licensable_entity_override_level'] = 'release-specific';
      }
      foreach ($profiles as $key => $record) {
        if (isset($record['included_sometimes']) && $record['included_sometimes'] && isset($record['excluded_sometimes']) && $record['excluded_sometimes']) {
          $profiles[$key]['pack_name'] = 'release-specific';
          $profiles[$key]['relative_quantity'] = 'release-specific';
          $profiles[$key]['licensable_entity_override_level'] = 'release-specific';
        }
      }

      build_licensableEntities_SKUs($profiles);
      $where_clause = ' sku_overprovisiong_rules.as_cluster_uid=as_clusters.as_cluster_uid AND as_clusters.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id AND sku_overprovisiong_rules.licensable_entity_uid=?';
      $where_values = array($le_uid);
      $orders = $this->tables['skuOverAllocationRulesPerClusters']['orderBy'];
      $overallocations = $this->db->get_all_paged_data($this->tables['skuOverAllocationRulesPerClusters'], NULL, $orders, $where_clause, $where_values);
      build_licensableEntities_Overallocations($overallocations);
      $canDelete = !((isset($profiles) && is_array($profiles) && count($profiles) > 0) || (isset($overallocations) && is_array($overallocations) && count($overallocations) > 0));
    }

    if (!empty($_SESSION['canManageSKUs']))
      build_licensableEntities_addEdit($rs, $this->db->getEnumValues('licensable_entities', 'level'),
        $this->db->getEnumValues('licensable_entity_to_server_compatibility', 'server_type'), $this->db->getEnumValues('licensable_entities', 'licensable_entity_type'),
        $this->db->getEnumValues('licensable_entities', 'special_computation_rules'), $level_is_overriden, $canDelete
      );

    $edit = (isset($_GET['memberid'])); // Determine if the form should contain values or be blank
    $rs = array();

    if ($edit) {
      $memberid = trim($_GET['memberid']);
      $rs = $this->db->get_all_properties($this->tables['licensableEntitiesTable'], $memberid);
    }
    if (isset($_SESSION['post'])) {
      $rs = $_SESSION['post'];
    }

    unset($_SESSION['post'], $rs);
    unset($_SESSION['post'], $LE);
  }

  /**
   * Body::handle_configureLEReleases()
   *
   * @return
   */
  function handle_configureLEReleases() {
    $selected_profile_uid = 0;
    $le_uid = 0;
    $sku_uid = 0;
    $LEName = "";
    $LE_level_override = FALSE;
    $SKUName = "";

    if (isset($_GET['licensable_entity_uid'])) {
      $rs = $this->db->get_data_from_key($this->tables['licensableEntitiesTable']['tableName'], 'enable_level_override, licensable_entity_name', 'licensable_entity_uid=?',
        $_GET['licensable_entity_uid']);
      $LEName = $rs[0]['licensable_entity_name'];
      $LE_level_override = $rs[0]['enable_level_override'];

      $le_uid = trim($_GET['licensable_entity_uid']);
    }
    if (isset($_GET['sku_uid'])) {
      $sku_uid = trim($_GET['sku_uid']);
      $rs = $this->db->get_data_from_key($this->tables['SKUsTable']['tableName'], 'sku_name', 'sku_uid=?', $_GET['sku_uid']);
      $SKUName = $rs[0];
    }
    if (isset($_GET['profile_uid']))
      $selected_profile_uid = trim($_GET['profile_uid']);
    $settings_for_this_le_profile = $this->db->getRows("select sku_profile_content_release_info.licensable_entity_override_level, sku_profile_content_release_info.sku_profile_content_release_info_uid, sku_profile_content_release_info.applicable_release, sku_profile_content_release_info.relative_quantity, sku_profile_content_release_info.pack_name, sku_profile_content_release_info.include_in_license from sku_profile_content_release_info, sku_profile_content, sku_content where sku_profile_content_release_info.sku_profile_content_uid=sku_profile_content.sku_profile_content_uid AND sku_profile_content.sku_profile_uid=" . $selected_profile_uid . " AND sku_content.sku_content_uid=sku_profile_content.sku_content_uid AND sku_content.licensable_entity_uid=" . $le_uid . " ORDER BY sku_profile_content_release_info.applicable_release ASC");
    $existing_profiles = $this->db->getRows("select * from sku_profiles where sku_profiles.sku_uid=" . $sku_uid);

    $LE_level_enum = $this->db->getEnumValues($this->tables['licensableEntitiesTable']['tableName'], 'level');

    build_SKULicensableEntity_Edit($selected_profile_uid, $settings_for_this_le_profile, $le_uid, $sku_uid, $LEName, $SKUName, $existing_profiles, $LE_level_override,
      $LE_level_enum);
  }

  /**
   * Body::handle_filterSKUs()
   *
   * @return
   */
  function handle_filterSKUs() {
    $where_clause = "sku_list.sku_name like '" . $_GET['name'] . "%'";
    $where_values = array();
    $SKUs = $this->db->get_all_paged_data($this->tables['SKUsTable'], NULL, $this->tables['SKUsTable']['orderBy'], $where_clause, $where_values);

    $json = '{ "skus" : ';
    $json .= '['; // start the json array element
    $skus_items = array();
    foreach ($SKUs as $key => $sku)
      $skus_items[] = ' { "name" : "' . $sku['sku_name'] . '", "uid" : "' . $sku['sku_uid'] . '", "pcode" : "' . $sku['product_code'] . '" } ';

    $json .= implode(',', $skus_items); // join the objects by commas;
    $json .= '] '; // end the json array element
    $json .= ' }'; // end the json element
    echo $json;
  }

  /**
   * Body::handle_licensableSKUs()
   *
   * @return
   */
  function handle_licensableSKUs() {
    $num = $this->db->get_num_recs($this->tables['SKUsTable']);
    $this->pager->setTotRecords($num);
    $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['SKUsTable']['tableKey']) : $this->tables['SKUsTable']['orderBy']);

    $SKUs = $this->db->get_all_paged_data($this->tables['SKUsTable'], $this->pager, $orders);
    $SKUsForDropDown = $this->db->get_all_paged_data($this->tables['SKUsTable'], NULL, $this->tables['SKUsTable']['orderBy']);
    // check if each sku is assigned... only allow removal of unassigned skus
    if (isset($SKUs) and is_array($SKUs))
      foreach ($SKUs as $key => $sku)
        $SKUs[$key]['isAssigned'] = $this->db->is_sku_assigned($sku['sku_uid']);

    build_SKUs_body($this->pager, $SKUs, $SKUsForDropDown, $this->db->getLastError());
    if (!isset($_GET['sku_pcode']))
      $this->pager->printPages();

    $basic_attributes = array();
    $skuName = "";
    $LEList = NULL;

    $editSKU = (isset($_GET['sku_uid']));
    if ($editSKU) {
      $sku_uid = trim($_GET['sku_uid']);

      $basic_attributes = $this->db->get_all_properties($this->tables['SKUsTable'], $sku_uid);
      $skuName = $basic_attributes['sku_name'];
    }

    if (isset($_SESSION['post']))
      $basic_attributes = $_SESSION['post'];

    if ($editSKU) {
      $where_clause = 'sku_profiles.sku_uid=? and sku_list.sku_uid=sku_profiles.sku_uid';
      $where_values = array($_GET['sku_uid']);
      $SKUProfileList = $this->db->get_all_paged_data($this->tables['SKUsProfileTable'], NULL, $this->tables['SKUsProfileTable']['tableKey'], $where_clause, $where_values);
      // enhance customer info with customer name (should do  a single query eventually)
      foreach ($SKUProfileList as $id => $SKUProfile) {
        $SKUProfileList[$id]['used'] = $this->db->getProductProfileUsers($SKUProfile['sku_profile_uid']);
        if (strlen($SKUProfile['customer_id']) > 0) {
          $SKUProfileList[$id]['customer_name'] = $this->db->get_customer_name($SKUProfile['customer_id']);
        }
      }

      $RelativeQuantitiesForThisSKU_RAW = $this->db->getRows("SELECT distinct sku_profile_content_release_info.relative_quantity FROM licensable_entities, sku_content, sku_profile_content_release_info, sku_profile_content, sku_profiles WHERE sku_profile_content.sku_profile_content_uid=sku_profile_content_release_info.sku_profile_content_uid AND sku_profile_content.sku_profile_uid=sku_profiles.sku_profile_uid AND sku_profiles.sku_uid=" . $_GET['sku_uid'] . " AND sku_profile_content.sku_content_uid=sku_content.sku_content_uid AND sku_content.licensable_entity_uid=licensable_entities.licensable_entity_uid AND (licensable_entities.special_computation_rules<>'Fixed Maximal Value' OR licensable_entities.special_computation_rules IS NULL)");
      // tweak this into what we want...
      $RelativeQuantitiesForThisSKU = array();

      foreach ($RelativeQuantitiesForThisSKU_RAW as $entry) {
        if ($entry['relative_quantity'] != 0 && $entry['relative_quantity'] != MAX_SQL_INT)
          $RelativeQuantitiesForThisSKU[] = $entry['relative_quantity'];
      }

      build_SKUs_editSearch($basic_attributes, $editSKU, $this->pager, $this->db->getEnumValues($this->tables['SKUsTable']['tableName'], 'sku_type'), $SKUProfileList,
        $RelativeQuantitiesForThisSKU);
    }
    // ----- table with all LEs for one SKU ----- vvv
    if ($editSKU) {
      // for Add-LE dropdown: build list of existing LEs
      $LEList = $this->db->get_all_paged_data($this->tables['licensableEntitiesTable'], NULL, $this->tables['licensableEntitiesTable']['orderBy'], NULL, NULL, 'ASC');

      $where_clause = 'sku_content.sku_uid=sku_list.sku_uid AND sku_content.sku_uid=?';
      $where_values = array($_GET['sku_uid']);
      $orders = $this->tables['SKULicensableEntitiesMergeTable']['orderBy'];
      // only show for selected profile - or default one if none selected
      $selected_profile_uid = 0;
      if (preg_match('/profile_uid=/', $_SERVER['QUERY_STRING'])) {
        $selected_profile_uid = trim($_GET['profile_uid']);
      } /* select default profile
        else {
        if (is_array($SKUProfileList))
        foreach($SKUProfileList as $id => $SKUProfile) {
        if ($SKUProfile['isdefault'] == 1)
        $selected_profile_uid = $SKUProfile['sku_profile_uid'];
        }
        }
       */
      // SQL: gives all release data for all LEs in profile $selected_profile_uid
      // sorting is NOT done at query time, because data displayed is computed afterwards (see below)
      $query = "
        SELECT  licensable_entities.licensable_entity_name,
                sku_content.licensable_entity_uid,
                sku_content.sku_content_uid,
                sku_profile_content_release_info.applicable_release,
                sku_profile_content_release_info.relative_quantity,
                sku_profile_content_release_info.pack_name,
                sku_profile_content_release_info.licensable_entity_override_level,
                sku_profile_content_release_info.include_in_license
        FROM    licensable_entities,
                sku_profile_content_release_info,
                sku_profile_content,
                sku_content
        WHERE   sku_profile_content_release_info.sku_profile_content_uid=sku_profile_content.sku_profile_content_uid
        AND     sku_profile_content.sku_profile_uid=" . $selected_profile_uid . "
        AND     sku_content.sku_content_uid=sku_profile_content.sku_content_uid
        AND     licensable_entities.licensable_entity_uid=sku_content.licensable_entity_uid";
      $LEs_in_this_profile_RAW = $this->db->getRows($query);
      $query_recent = "
        SELECT  licensable_entities.licensable_entity_name,
                sku_content.licensable_entity_uid,
                sku_content.sku_content_uid,
                sku_profile_content_release_info.applicable_release,
                sku_profile_content_release_info.relative_quantity,
                sku_profile_content_release_info.pack_name,
                sku_profile_content_release_info.licensable_entity_override_level,
                sku_profile_content_release_info.include_in_license
        FROM    licensable_entities,
                sku_profile_content_release_info,
                sku_profile_content,
                sku_content,
		(SELECT CONCAT('R',releases.release) applicable_release  FROM releases ORDER BY releases.order DESC LIMIT 3) a
        WHERE   sku_profile_content_release_info.sku_profile_content_uid=sku_profile_content.sku_profile_content_uid
        AND     sku_profile_content.sku_profile_uid=" . $selected_profile_uid . "
        AND     sku_content.sku_content_uid=sku_profile_content.sku_content_uid
        AND     a.applicable_release=sku_profile_content_release_info.applicable_release
        AND     licensable_entities.licensable_entity_uid=sku_content.licensable_entity_uid";
      $LEs_in_this_profile_recent = $this->db->getRows($query_recent);

      // tweak this into what we want...
      $LEs_in_this_profile = array();
      foreach ($LEs_in_this_profile_RAW as $profile_content_data) {
        $le_uid = $profile_content_data['licensable_entity_uid'];
        $idx = $profile_content_data['licensable_entity_name'];

        $LEs_in_this_profile[$idx] ['licensable_entity_uid'] = $le_uid;

        $LEs_in_this_profile[$idx] ['licensable_entity_name'] = $profile_content_data['licensable_entity_name'];
        $LEs_in_this_profile[$idx] ['sku_content_uid'] = $profile_content_data['sku_content_uid'];

        $LEs_in_this_profile[$idx]['sku_profile_content_uid'] = $this->db->get_sku_profile_content_uid($_GET['sku_uid'], $le_uid, $selected_profile_uid);
        // relative qty: detect changes in value between releases
        if (!isset($LEs_in_this_profile[$idx]['relative_quantity']))
          $LEs_in_this_profile[$idx]['relative_quantity'] = $profile_content_data['relative_quantity'];
        elseif ($LEs_in_this_profile[$idx]['relative_quantity'] != $profile_content_data['relative_quantity'])
          $LEs_in_this_profile[$idx]['relative_quantity'] = 'release-specific';
        // pack name: detect changes in value between releases
        if (!isset($LEs_in_this_profile[$idx]['pack_name']))
          $LEs_in_this_profile[$idx]['pack_name'] = $profile_content_data['pack_name'];
        elseif ($LEs_in_this_profile[$idx]['pack_name'] != $profile_content_data['pack_name'])
          $LEs_in_this_profile[$idx]['pack_name'] = 'release-specific';
        // LE level: detect changes in value between releases
        if (!isset($LEs_in_this_profile[$idx]['licensable_entity_override_level']))
          $LEs_in_this_profile[$idx]['licensable_entity_override_level'] = $profile_content_data['licensable_entity_override_level'];
        elseif ($LEs_in_this_profile[$idx]['licensable_entity_override_level'] != $profile_content_data['licensable_entity_override_level'])
          $LEs_in_this_profile[$idx]['licensable_entity_override_level'] = 'release-specific';
        // inclusion: detect changes in value between releases
        if (!isset($LEs_in_this_profile[$idx]['include_in_license']))
          $LEs_in_this_profile[$idx]['include_in_license'] = $profile_content_data['include_in_license'];
        elseif ($LEs_in_this_profile[$idx]['include_in_license'] != $profile_content_data['include_in_license'])
          $LEs_in_this_profile[$idx]['include_in_license'] = 'release-specific';
      }

      function licensable_entity_nameASC($a1, $a2) {
        return ($a1['licensable_entity_name'] == $a2['licensable_entity_name']) ? 0 : (($a1['licensable_entity_name'] < $a2['licensable_entity_name']) ? -1 : 1);
      }

      function licensable_entity_nameDESC($a1, $a2) {
        return ($a1['licensable_entity_name'] == $a2['licensable_entity_name']) ? 0 : (($a1['licensable_entity_name'] > $a2['licensable_entity_name']) ? -1 : 1);
      }

      function pack_nameASC($a1, $a2) {
        return ($a1['pack_name'] == $a2['pack_name']) ? 0 : (($a1['pack_name'] < $a2['pack_name']) ? -1 : 1);
      }

      function pack_nameDESC($a1, $a2) {
        return ($a1['pack_name'] == $a2['pack_name']) ? 0 : (($a1['pack_name'] > $a2['pack_name']) ? -1 : 1);
      }

      function relative_quantityASC($a1, $a2) {
        return ($a1['relative_quantity'] == $a2['relative_quantity']) ? 0 : (($a1['relative_quantity'] < $a2['relative_quantity']) ? -1 : 1);
      }

      function relative_quantityDESC($a1, $a2) {
        return ($a1['relative_quantity'] == $a2['relative_quantity']) ? 0 : (($a1['relative_quantity'] > $a2['relative_quantity']) ? -1 : 1);
      }

      function include_in_licenseASC($a1, $a2) {
        return ($a1['include_in_license'] == $a2['include_in_license']) ? 0 : (($a1['include_in_license'] < $a2['include_in_license']) ? -1 : 1);
      }

      function include_in_licenseDESC($a1, $a2) {
        return ($a1['include_in_license'] == $a2['include_in_license']) ? 0 : (($a1['include_in_license'] > $a2['include_in_license']) ? -1 : 1);
      }
      $order2 = (isset($_GET['order2']) ? $_GET['order2'] : 'licensable_entity_name'); // dflt
      $vert2 = (isset($_GET['vert2']) ? $_GET['vert2'] : 'ASC'); // dflt
      uasort($LEs_in_this_profile, $order2 . $vert2);

      build_SKULicensableEntities($skuName, $sku_uid, $selected_profile_uid, $LEList, $LEs_in_this_profile, $this->db->getLastError(), $SKUProfileList,
        $LEs_in_this_profile_recent);
    }

    unset($_SESSION['post'], $basic_attributes);
  }
  /*
   * LRS Admin tools related lookup
   */

  function handle_admin_tools() {
    $extraData = NULL;
    if (isset($_GET['toolType']) && $_GET['toolType'] == 'processRegressionTests') {
      $extraData['clusters'] = $this->db->getClustersTest();
      $extraData['groups'] = $this->db->getGroupsTest();
    }
    if (isset($_GET['toolType']) && $_GET['toolType'] == 'showRegressionTests') {
      $test_uid = $_GET['test_uid'];
      $reg_test = $this->db->get_reg_test($test_uid);
      $reg_test_details = $this->db->get_reg_test_details($test_uid, CmnFns::cleanVals($_GET, true));
      build_display_regression_test($reg_test, $reg_test_details);
      return;
    }
    if (isset($_GET['toolType']) && $_GET['toolType'] == 'manageReleases') {
      $extraData = $this->db->get_conf_releases_array(true);
    }
    if (isset($_GET['toolType']) && $_GET['toolType'] == 'emailUsers') {
      $extraData['roles'] = Auth::getRoleNames($this->getContext()->getAuthDB());
      $extraData['mailing_lists'] = $this->db->getMailingLists();
    }
    if (isset($_GET['success'])) {
      $extraData = array('success' => ($_GET['success'] == "true" ? true : false));
    }
    build_admin_tools_body((isset($_GET['toolType']) ? $_GET['toolType'] : ''), $extraData, $this->db->get_reg_tests());
    unset($_SESSION['post']);
  }

  /**
   * Body::handle_customers()
   *
   * @return
   */
  function handle_customers() {
    if (!isset($_GET['ajax'])) { // if a normal request for the page
      $where_clause = '';
      $AND = "";
      //read customer missing servers cache
      if (isset($_GET['customerIdSearch'])) {
        $where_clause = "customer_id like '%" . $_GET['customerIdSearch'] . "%' ";
        $AND = " AND ";
      }
      if (isset($_GET['customerNameSearch'])) {
        $where_clause .= $AND . "customer_name like '%" . $_GET['customerNameSearch'] . "%' ";
        $AND = " AND ";
      }

      if (isset($_GET['customer_filter']) && ($AND == '')) {
        $where_clause = "customers.customer_id='" . $_GET['customer_filter'] . "'";
      }

      $num = $this->db->get_num_recs($this->tables['customerTable'], $where_clause); // Get number of records
      $this->pager->setTotRecords($num); // Pager method calls
      $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['customerTable']['tableKey']) : $this->tables['customerTable']['orderBy']);

      $customers = $this->db->get_all_paged_data($this->tables['customerTable'], $this->pager, $orders, $where_clause);

      if ($num > 0) {
        $systemCountByCustomer = $this->db->get_customer_dependees_counts();
        for ($i = 0; $i < count($customers); $i++) {
          $customer_id = $customers[$i]['customer_id'];
          $system_count = 0;
          $cluster_count = 0;
          $host_id_count = 0;
          if (isset($systemCountByCustomer[$customer_id])) {
            $system_count = $systemCountByCustomer[$customer_id]['system_count'];
            $cluster_count = $systemCountByCustomer[$customer_id]['cluster_count'];
            $host_id_count = $systemCountByCustomer[$customer_id]['host_id_count'];
          }
          $customers[$i]['system_count'] = $system_count;
          $customers[$i]['cluster_count'] = $cluster_count;
          $customers[$i]['host_id_count'] = $host_id_count;
          $customers[$i]['has_license'] = false;
        }
      }

      build_customers_body($this->pager, $customers, $this->db->getLastError());
      $this->pager->printPages();

      $managingMode = new ProvisioningManagingMode("customer_id");

      $rs = array();
      if ($managingMode->shouldLoadData()) { // Validate customer_id
        $rs = $this->db->get_all_properties($this->tables['customerTable'], trim($managingMode->getId()));
        if ($managingMode->isInInitFromMode()) {
          // Reset some fields
          $rs["customer_id"] = "";
          $rs["customer_name"] = "";
          $rs["sfdc_status"] = "";
          $rs["sfdc_account_type"] = "";
        }
      }
      if (isset($_SESSION['post'])) {
        $rs = $_SESSION['post'];
      }

      $customerTypeList = array('Direct', 'Indirect', 'Reseller');
      $rs['customerTypeList'] = $customerTypeList;

      $where_clause = 'customers.customer_type=?';
      $where_values = array('Reseller');
      $rs['customerIdList'] = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_id', $where_clause, $where_values, 'customer_name');
      build_customers_editSearch($rs, $managingMode->isInEditMode(), $managingMode->shouldLoadData(), $this->pager);

      unset($_SESSION['post'], $rs);
    } else {
      if (isset($_GET['type']) && ($_GET['type'] == 'id')) {
        $orders = array('customer_id');
      } else {
        $orders = array('customer_name');
      }
      $customers = $this->db->get_all_paged_data($this->tables['customerTable'], null, $orders, null);
      $json = array();
      foreach ($customers as $customer) {
        $json['customers'][] = array("name" => $customer['customer_name'], "id" => $customer['customer_id']);
      }
      echo json_encode($json);
    }
  }

  /**
   * Body::handle_systems()
   *
   * @return
   */
  function handle_systems() {
    $where_clause = '';
    $where_values = array();
    $AND = "";
    if (isset($_GET['systemIdSearch'])) {
      $where_clause .= $AND . "systems.system_uid like '%" . $_GET['systemIdSearch'] . "%' ";
      $AND = " AND ";
    }
    if (isset($_GET['systemNameSearch'])) {
      $where_clause .= $AND . "systems.system_name like '%" . $_GET['systemNameSearch'] . "%' ";
      $AND = " AND ";
    }
    if (isset($_GET['customer_filter'])) {
      $where_clause .= $AND . "systems.customer_id=?";
      array_push($where_values, $_GET['customer_filter']);
      $AND = " AND ";
    }
    if (!empty($_GET['hideDecommissioned'])) {
      $where_clause .= $AND . " system_decommissioned='0' ";
      $AND = " AND ";
    }
    $num = $this->db->get_num_recs($this->tables['systemCustomerMergeTable'],
      ' customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values); // Get number of records
    $this->pager->setTotRecords($num); // Pager method calls
    $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['systemCustomerMergeTable']['tableKey']) : $this->tables['systemCustomerMergeTable']['orderBy']);

    $systems = $this->db->get_all_paged_data($this->tables['systemCustomerMergeTable'], $this->pager, $orders,
      ' customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values);

    $decommissioned_date = null;
    if ($num > 0) {
      // TODO: This should build arrays only for the current page (system ids)
      $countsBySystem = $this->db->get_system_dependees_counts();
      for ($i = 0; $i < count($systems); $i++) {
        $system_uid = $systems[$i]['system_uid'];
        $cluster_count = 0;
        $host_id_count = 0;
        if (isset($countsBySystem[$system_uid])) {
          $cluster_count = $countsBySystem[$system_uid]['cluster_count'];
          $host_id_count = $countsBySystem[$system_uid]['host_id_count'];
          if (isset($selectedSystemId) && $system_uid == trim($selectedSystemId)) {
            $decommissioned_date = $countsBySystem[$system_uid]['decommissioned_date'];
          }
        }
        $systems[$i]['cluster_count'] = $cluster_count;
        $systems[$i]['host_id_count'] = $host_id_count;
        $systems[$i]['has_license'] = false;
      }
    }

    build_systems_body($this->pager, $systems, $this->db->getLastError());
    $this->pager->printPages();

    $managingMode = new ProvisioningManagingMode("system_uid");

    $rs = array();
    if ($managingMode->shouldLoadData()) { // Validate customer_uid
      $rs = $this->db->get_all_properties($this->tables['systemCustomerMergeTable'], trim($managingMode->getId()), ' customers.customer_id=systems.customer_id ');
      if ($managingMode->isInInitFromMode()) {
        // Reset some fields
        $rs["system_uid"] = "";
        $rs["system_name"] = "";
      }
    }
    $rs['decommissioned_date'] = $decommissioned_date;
    if (isset($_SESSION['post'])) {
      $rs = $_SESSION['post'];
    }

    $systemTypeList = array('lab', 'production', 'demo', 'trial');

    $where_clause = '';
    $where_values = array();
    if (isset($_GET['customer_filter'])) {
      $where_clause = 'customer_id=?';
      array_push($where_values, $_GET['customer_filter']);
    }
    $rs['customerNameList'] = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_name', $where_clause, $where_values, 'customer_name');
    $rs['customerIdList'] = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_id', $where_clause, $where_values, 'customer_name');

    $rs['systemTypes'] = $systemTypeList;

    if (!empty($_SESSION['canManageEntries'])) {
      build_systems_edit($rs, $managingMode->isInEditMode(), $managingMode->shouldLoadData(), $this->pager);
    }
    unset($_SESSION['post'], $rs);
  }

  /**
   * Body::handle_asclusters()
   *
   * @return
   */
  function handle_asclusters() {
    $where_clause = "";
    $where_values = array();
    $AND = "";
    if (isset($_GET['clusterIdSearch'])) {
      $where_clause .= $AND . "as_cluster_uid like '%" . $_GET['clusterIdSearch'] . "%'";
      $AND = " AND ";
    }
    if (isset($_GET['clusterNameSearch'])) {
      $where_clause .= $AND . "as_cluster_name like '%" . $_GET['clusterNameSearch'] . "%'";
      $AND = " AND ";
    }
    if (isset($_GET['customer_filter'])) {
      $where_clause .= $AND . "systems.customer_id=?";
      array_push($where_values, $_GET['customer_filter']);
      $AND = " AND ";
    }
    if (isset($_GET['system_filter'])) {
      $where_clause .= $AND . "system_name=?";
      array_push($where_values, $_GET['system_filter']);
      $AND = " AND ";
    }
    if (!empty($_GET['hideDecommissioned'])) {
      $where_clause .= $AND . " status<>'Decommissioned' ";
    }

    $num = $this->db->get_num_recs($this->tables['asClusterSystemMergeTable'],
      ' as_clusters.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values); // Get number of records
    $this->pager->setTotRecords($num); // Pager method calls
    $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['asClusterSystemMergeTable']['tableKey']) : $this->tables['asClusterSystemMergeTable']['orderBy']);

    $clusters = $this->db->get_all_paged_data($this->tables['asClusterSystemMergeTable'], $this->pager, $orders,
      ' as_clusters.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values);

    if ($num > 0) {
      $countsByClusters = $this->db->get_as_cluster_dependees_counts();

      for ($i = 0; $i < count($clusters); $i++) {
        $as_cluster_uid = $clusters[$i]['as_cluster_uid'];
        $host_id_count = 0;
        $number_of_nodes_count = 0;
        if (isset($countsByClusters[$as_cluster_uid])) {
          $host_id_count = $countsByClusters[$as_cluster_uid]['host_id_count'];
          $number_of_nodes_count = (isset($countsByClusters[$as_cluster_uid]['number_of_nodes_count']) && $countsByClusters[$as_cluster_uid]['number_of_nodes_count'] != "" ? $countsByClusters[$as_cluster_uid]['number_of_nodes_count'] : 0);
        }
        $clusters[$i]['host_id_count'] = $host_id_count;
        $clusters[$i]['node_per_clusters'] = (($clusters[$i]['node_per_clusters'] == NULL || $clusters[$i]['node_per_clusters'] == "") ? $number_of_nodes_count : $clusters[$i]['node_per_clusters']);
        $clusters[$i]['has_license'] = false;
      }
    }

    build_as_clusters_body($this->pager, $clusters, $this->db->getLastError(), true);
    $this->pager->printPages();

    $managingMode = new ProvisioningManagingMode("as_cluster_uid");

    $rs = array();

    $rs['max_lic_node_count'] = "";
    if ($managingMode->shouldLoadData()) { // Validate customer_uid
      $rs = $this->db->get_all_properties($this->tables['asClusterSystemMergeTable'], $managingMode->getId(),
        ' as_clusters.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id ');
      $rs['max_lic_node_count'] = 0;
      if (isset($countsByClusters[$managingMode->getId()]) && isset($countsByClusters[$managingMode->getId()]['number_of_nodes_count'])) {
        $clusterCounts = $countsByClusters[$managingMode->getId()];
        $rs['max_lic_node_count'] = ($clusterCounts['number_of_nodes_count'] == NULL || $clusterCounts['number_of_nodes_count'] == "" ? 0 : $clusterCounts['number_of_nodes_count']);
      }
      if ($managingMode->isInInitFromMode()) {
        // Reset some fields
        $rs["as_cluster_uid"] = "";
        $rs["as_cluster_name"] = "";
      }
    }
    if (isset($_SESSION['post'])) {
      $rs = $_SESSION['post'];
    }
    //reset where as we use it only for customer values
    $where_clause = "";
    $where_values = array();
    // Push the list of customers along with the list of systems for that customer...
    if (isset($_GET['customer_filter'])) {
      $where_clause = 'customer_id=?';
      array_push($where_values, $_GET['customer_filter']);
    }
    $rs['customerNameList'] = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_name', $where_clause, $where_values, 'customer_name');
    $rs['customerIdList'] = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_id', $where_clause, $where_values, 'customer_name');

    $cluster_type_list = $this->db->getEnumValues($this->tables['asClusterTable']['tableName'], 'server_type');
    $rs['cluster_type_list'] = $cluster_type_list;
    $rs['relArr'] = $this->db->get_conf_releases_array();
    if (!empty($_SESSION['canManageEntries'])) {
      build_as_clusters_edit($rs, $managingMode->isInEditMode(), $this->db->getEnumValues('as_clusters', 'ac_region'), $managingMode->shouldLoadData(), $this->pager);
    }
    unset($_SESSION['post'], $rs);
  }

  /**
   * Body::handle_ashostids()
   *
   * @return
   */
  function handle_ashostids() {
    if (!isset($_GET['ajax'])) { // if a normal request for the page
      $where_clause = '';
      $where_values = array();
      $isAsearch = false;
      if (isset($_GET['customer_filter'])) {
        $where_clause = 'systems.customer_id=?';
        $where_values = array($_GET['customer_filter']);
      }
      if (isset($_GET['system_filter'])) {
        $where_clause .= ' AND system_name=?';
        $where_values = array($_GET['customer_filter'], $_GET['system_filter']);
      }
      if (isset($_GET['cluster_filter'])) {
        $where_clause .= ' AND as_cluster_name=?';
        $where_values = array($_GET['customer_filter'], $_GET['system_filter'], $_GET['cluster_filter']);
      }
      $AND = "";
      if (isset($_GET['hostIdValueSearch'])) {
        $where_clause = "host_ids.host_id like '%" . $_GET['hostIdValueSearch'] . "%' ";
        $AND = " AND ";
      }
      if (isset($_GET['hostIdDescSearch'])) {
        $where_clause .= $AND . "host_ids.description like '%" . $_GET['hostIdDescSearch'] . "%' ";
      }
      if (!empty($_GET['hideDecommissioned'])) {
        if (strpos(substr($where_clause, -5), "AND") == FALSE && !empty($where_clause)) {
          $AND = " AND";
        } else {
          $AND = "";
        }
        $where_clause .= $AND . " host_ids.decomissioned='N' ";
      }

      $num = $this->db->get_num_recs($this->tables['asHostIdMergeTable'],
        ' host_ids.as_cluster_uid=as_clusters.as_cluster_uid AND as_clusters.system_uid=systems.system_uid  AND customers.customer_id=systems.customer_id' . ($where_clause != '' ? ' AND ' . $where_clause : ''),
        $where_values); // Get number of records
      $this->pager->setTotRecords($num); // Pager method calls
      $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['asHostIdMergeTable']['tableKey']) : $this->tables['asHostIdMergeTable']['orderBy']);

      $ashostids = $this->db->get_all_paged_data($this->tables['asHostIdMergeTable'], $this->pager, $orders,
        ' host_ids.as_cluster_uid=as_clusters.as_cluster_uid AND as_clusters.system_uid=systems.system_uid  AND customers.customer_id=systems.customer_id' . ($where_clause != '' ? ' AND ' . $where_clause : ''),
        $where_values);
      build_as_host_id_body($this->pager, $ashostids, $this->db->getLastError());
      $this->pager->printPages();

      $managingMode = new ProvisioningManagingMode("host_id_uid");

      $rs = array();

      if ($managingMode->shouldLoadData()) { // Validate customer_uid
        $rs = $this->db->get_all_properties($this->tables['asHostIdMergeTable'], $managingMode->getId(),
          ' host_ids.as_cluster_uid=as_clusters.as_cluster_uid AND as_clusters.system_uid=systems.system_uid  AND customers.customer_id=systems.customer_id');
        if ($managingMode->isInInitFromMode()) {
          // Reset some fields
          $rs["host_id_uid"] = "";
          $rs["host_id"] = "";
        }
      }
      if (isset($_SESSION['post'])) {
        $rs = $_SESSION['post'];
      }
      // Push the list of customers along with the list of systems for that customer...
      if (isset($_GET['customer_filter'])) {
        $where_clause = 'customer_id=?';
        $where_values = array($_GET['customer_filter']);
      } else {
        $where_clause = '';
        $where_values = array();
      }
      $rs['customerNameList'] = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_name', $where_clause, $where_values, 'customer_name');
      $rs['customerIdList'] = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_id', $where_clause, $where_values, 'customer_name');

      build_as_host_id_edit($rs, $managingMode->isInEditMode(), $managingMode->shouldLoadData(), $this->pager);
      unset($_SESSION['post'], $rs);
    } else { //handling ajax starting here
      $customer_id = $_GET['customer_id'];
      if (isset($_GET['system_filter'])) {
        $systemList = array($_GET['system_filter']);
      } else {
        $systemList = $this->db->get_data_from_key($this->tables['systemTable']['tableName'], 'system_name', ' customer_id=? ', array($customer_id));
      }
      if (isset($_GET['system_name'])) {
        $system_name = $_GET['system_name'];
      } else if (isset($_GET['defaultsystem_name'])) {
        $system_name = $_GET['defaultsystem_name'];
      } else {
        $system_name = $systemList[0];
      }
      if (isset($_GET['cluster_filter'])) {
        $clusterList = array($_GET['cluster_filter']);
      } else {
        $clusterList = $this->db->get_data_from_key($this->tables['asClusterSystemMergeTable']['tableName'], 'as_cluster_name',
          ' as_clusters.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id AND systems.customer_id=? AND systems.system_name=? ',
          array($customer_id, $system_name));
      }
      echo json_encode(array("systemList" => $systemList, "clusterList" => $clusterList));
    }
  }

  /**
   */
  function handle_licenseUploadAndSign() {
    build_upload_and_sign_license($this->db->getEnumValues('licenses_generated', 'reason'));
  }

  /**
   * Body::handle_licensableInventory()
   *
   * @return
   */
  function handle_licensableInventory() {
    if (!isset($_GET['customer_filter'])) {
      build_license_inventory_body(null);
      return;
    }
    $rs = array();
    $rs['customer_name'] = $this->db->get_customer_name($_GET['customer_filter']);

    $where_values = array($_GET['customer_filter']);
    $where_clause = ' systems.customer_id=? ';
    $action = (isset($_GET['inv_action']) ? $_GET['inv_action'] : 'opportunities');
    switch ($action) {
      case 'opportunities':
        $where_clause = ' opportunities.customer_id=?';
        $num = $this->db->get_num_recs($this->tables['opportunities'], $where_clause, $where_values); // Get number of records
        $this->pager->setTotRecords($num); // Pager method calls
        $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['opportunities']['tableKey']) : array('opportunity_import_timestamp'));
        $opportunities = $this->db->get_all_paged_data($this->tables['opportunities'], $this->pager, $orders, $where_clause, $where_values, 'DESC');
        $resellers = $this->db->get_all_paged_data($this->tables['customerTable'], NULL, array('customers.customer_id'), "customer_type='Reseller'", NULL, "ASC", true);
        $manualopreasons = $this->db->get_all_paged_data($this->tables['manualopReasons'], NULL, array('manual_opportunity_rule_reason_name'), null, array(), "ASC", true);
        $rs['opportunities'] = $opportunities;
        $rs['customer_id'] = $_GET['customer_filter'];
        build_opportunities_body_for_licensing($this->pager, $rs, $this->db->getLastError(), $action, $resellers, $manualopreasons);
        break;

      case 'cluster_licenses':
        /**
         *  load the auditing info to see if servers are missing based on opportunities and LEs to servers compatibility
         */
        $audit_servers = $this->handle_auditing_servers_report_view(false);
        $psm = $audit_servers['PSM'];
        $cust_info = reset($audit_servers['customers']);
        $inventory = $cust_info['inventory'];
        $rel = 'R' . $cust_info['max_release'];
        $missing = false;
        unset($audit_servers['customers'], $cust_info['inventory']);
        if (!empty($inventory)) {
          foreach ($inventory as $product_code => $product_details) {
            if (isset($psm[$rel][$product_details['sku_type']][$product_code])) {
              $servers_to_get = $psm[$rel][$product_details['sku_type']][$product_code];
              foreach ($servers_to_get as $server_type => $value) {
                if (empty($cust_info['completed_servers'][$product_details['sku_type']][$server_type])) {
                  $missing = true;
                }
              }
            }
          }
        }

        //filtering check for  system_name/system type/cluster type
        $this->buildFilter('server_type', 'server_type', '=', $where_clause, $where_values);
        $this->buildFilter('system_name', 'system_name', '=', $where_clause, $where_values);
        $this->buildFilter('system_type', 'system_type', '=', $where_clause, $where_values);
        $this->buildFilter('cluster_release', 'software_release', '=', $where_clause, $where_values);
        $this->buildFilter('cluster_name', 'as_cluster_name', '=', $where_clause, $where_values);
        $this->buildFilter('group_name', 'group_name', '=', $where_clause, $where_values);
        $this->buildFilter('nfm_managed', 'ac_nfm_managed_as_cluster_uid', '=', $where_clause, $where_values);
        $this->buildFilter('status', 'as_clusters.status', '=', $where_clause, $where_values);
        $this->buildFilter('ac_region', 'as_clusters.ac_region', '=', $where_clause, $where_values);

        $num = $this->db->get_num_recs($this->tables['asClusterGroupsSystemMergeTable'],
          ' as_clusters.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values); // Get number of records

        $this->pager->setTotRecords($num); // Pager method calls
        $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['asClusterGroupsSystemMergeTable']['tableKey']) : $this->tables['asClusterGroupsSystemMergeTable']['orderBy']);
        $clusters = $this->db->get_all_paged_data($this->tables['asClusterGroupsSystemMergeTable'], $this->pager, $orders,
          ' as_clusters.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values);
        $filters = $this->db->get_all_paged_data($this->tables['asClusterGroupsSystemMergeTable'], null, $orders,
          ' as_clusters.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values);
        $as_cluster_ids = array();
        if (is_array($clusters)) {
          foreach ($clusters as $cluster) {
            array_push($as_cluster_ids, $cluster["as_cluster_uid"]);
          }
        }
        //get info about generated licenses from license table
        $expiringInfo = $this->db->getExpiringLicensesInfo(false, $as_cluster_ids);
        $rs['filters'] = $filters;
        $rs['clusters'] = $clusters;
        $rs['expiringInfo'] = $expiringInfo;
        $as_cluster_uid = null;
        $rs['nfm_in_system'] = array();
        $rs['available_nfm_nodes'] = array();
        if (isset($_GET['as_cluster_uid'])) {
          $as_cluster_uid = $_GET['as_cluster_uid'];
          $rs['nfm_in_system'] = $this->db->get_nfm_in_system($as_cluster_uid);
          $rs['available_nfm_nodes'] = $this->db->get_available_nfm_nodes($as_cluster_uid);
        }
        if ($as_cluster_uid != null) {
          $rs['cluster_lic_data'] = $this->db->get_all_paged_data($this->tables['sku_license_assignmentMergeTable'], NULL, array('sku_license_assignment.resolution_order'),
            'sku_license_assignment.sku_association_uid=sku_associations.sku_association_uid AND '
            . 'sku_associations.sku_uid=sku_list.sku_uid AND '
            . 'sku_license_assignment.sku_profile_uid=sku_profiles.sku_profile_uid AND '
            . 'sku_license_assignment.as_cluster_uid=? ', array($as_cluster_uid));
          $rs['cluster_info'] = $this->db->get_all_paged_data($this->tables['asClusterGroupsSystemMergeTable'], NULL, array('as_clusters.as_cluster_uid'),
            'as_clusters.system_uid=systems.system_uid AND '
            . 'systems.customer_id=customers.customer_id AND '
            . 'as_clusters.as_cluster_uid=? ', array($as_cluster_uid));
          if ($rs['cluster_info'][0]['server_type'] === 'nfm') {
            $rs['current_nfm_nodes'] = $this->db->get_nfm_nodes($as_cluster_uid);
            $rs['nfm_releases'] = $this->db->getNfmReleases();
          }
          $rs['assigned_teaser_profile'] = $this->db->get_assigned_teaser_profile(array($as_cluster_uid));
          $rs['sku_teaser_assignment2'] = $this->db->get_assigned_teaser_profiles_content(array($as_cluster_uid));
          $sku_profiles = $this->db->get_all_paged_data($this->tables['SKUsProfileTable'], NULL, $this->tables['SKUsProfileTable']['tableKey'],
            "sku_list.sku_uid=sku_profiles.sku_uid", NULL);
          $rs['sku_profiles'] = array();
          foreach ($sku_profiles as $sku_profile) {
            if (($sku_profile['customer_id'] == NULL) || ($sku_profile['customer_id'] == $rs['cluster_info'][0]['customer_id'])) {
              $sku_uid = $sku_profile['sku_uid'];
              if (!isset($rs['sku_profiles'][$sku_uid])) {
                $rs['sku_profiles'][$sku_uid]['profiles'] = array();
                $rs['sku_profiles'][$sku_uid]['sku_name'] = $sku_profile['sku_name'];
                $rs['sku_profiles'][$sku_uid]['sku_type'] = $sku_profile['sku_type'];
              }
              $rs['sku_profiles'][$sku_uid]['profiles'][] = array(
                'sku_profile_name' => $sku_profile['sku_profile_name'],
                'sku_profile_uid' => $sku_profile['sku_profile_uid'],
                'isDefault' => $sku_profile['isDefault']);
            }
          }
          $rs['over_allocation_rules'] = $this->licgen->get_overprovisioning_rules($as_cluster_uid);
          $tmpCounts = $this->db->get_as_cluster_dependees_counts($as_cluster_uid);
          $rs['max_lic_node_count'] = $tmpCounts['number_of_nodes_count'];
          $rs['host_ids'] = $this->db->get_host_ids_for_cluster($as_cluster_uid);
          $rs['oldLicenseFilename'] = $this->licgen->getOldLicenseFileLocation($rs['cluster_info'][0]['customer_id'], $as_cluster_uid);
        }
        $rs['consumed_count'] = $this->db->get_consumed_available_sku_count_for_customer($_GET['customer_filter']);
        $rs['per_server_type_consumed_count'] = $this->db->get_consumed_available_sku_count_for_customer_per_server_type($_GET['customer_filter']);
        if (!empty($as_cluster_uid)) {
          $generateLicense = new GenerateLicenseCore($this->getContext());
          $rs['available'] = $generateLicense->getAvailableProducts($as_cluster_uid);
        }
        //AS - PS-HSS
        build_as_clusters_body_for_licensing($this->getContext(), $this->pager, $rs, $this->db->getLastError(), $action, $as_cluster_uid,
          $this->db->getEnumValues('as_clusters', 'status'),
          $this->db->getEnumValues('as_clusters', 'ac_region'), $this->db->get_conf_releases_array(), $missing);
        break;
      case 'group_licenses':
        if (empty($_SESSION['canManageGroups'])) {
          print 'You do not have access to this page';
          return;
        }
        //filtering check for  system_name/system type/cluster type
        $this->buildFilter('server_type', 'server_type', '=', $where_clause, $where_values);
        $this->buildFilter('system_name', 'system_name', '=', $where_clause, $where_values);
        $this->buildFilter('system_type', 'system_type', '=', $where_clause, $where_values);
        $this->buildFilter('group_name', 'group_name', '=', $where_clause, $where_values);
        $this->buildFilter('nfm_managed', 'nfm_managed_as_cluster_uid', '=', $where_clause, $where_values);
        $this->buildFilter('group_release', 'group_software_release', '=', $where_clause, $where_values);
        $this->buildFilter('status', 'status', '=', $where_clause, $where_values);

        $num = $this->db->get_num_recs($this->tables['GroupsSystemMergeTable'],
          ' groups.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values); // Get number of records
        $this->pager->setTotRecords($num); // Pager method calls
        $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['GroupsSystemMergeTable']['tableKey']) : $this->tables['GroupsSystemMergeTable']['orderBy']);
        $groups = $this->db->get_all_paged_data($this->tables['GroupsSystemMergeTable'], $this->pager, $orders,
          ' groups.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values);
        $filters = $this->db->get_all_paged_data($this->tables['GroupsSystemMergeTable'], null, $orders,
          ' groups.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values);
        $group_ids = array();
        if (is_array($groups)) {
          foreach ($groups as $group) {
            array_push($group_ids, $group["group_uid"]);
          }
        }
        //get info about generated licenses from license table
        $expiringInfo = $this->db->getExpiringLicensesInfo(true, $group_ids);
        $rs['filters'] = $filters;
        $rs['groups'] = $groups;
        $rs['expiringInfo'] = $expiringInfo;
        $group_uid = isset($_GET['group_uid']) ? $_GET['group_uid'] : NULL;
        $rs['nfm_in_system'] = array();
        if ($group_uid != null) {
          $rs['nfm_in_system'] = $this->db->get_nfm_in_group_system($group_uid);
          $rs['members'] = $this->db->get_group_members($group_uid);
          $rs['available'] = $this->db->get_group_available($group_uid);
          $rs['group_lic_data'] = $this->db->get_all_paged_data($this->tables['sku_license_assignmentGroupsMergeTable'], NULL,
            array('sku_license_assignment_group.resolution_order'),
            'sku_license_assignment_group.sku_association_uid=sku_associations.sku_association_uid AND '
            . 'sku_associations.sku_uid=sku_list.sku_uid AND '
            . 'sku_license_assignment_group.sku_profile_uid=sku_profiles.sku_profile_uid AND '
            . 'sku_license_assignment_group.group_uid=? ', array($group_uid));
          $rs['group_info'] = $this->db->get_all_paged_data($this->tables['GroupsSystemMergeTable'], NULL, array('groups.group_uid'),
            'groups.system_uid=systems.system_uid AND '
            . 'systems.customer_id=customers.customer_id AND '
            . 'groups.group_uid=? ', array($group_uid));
          $rs['assigned_teaser_profile'] = $this->db->get_assigned_teaser_profile_group(array($group_uid));
          $rs['sku_teaser_assignment2'] = $this->db->get_assigned_teaser_profiles_group_content(array($group_uid));
          $sku_profiles = $this->db->get_all_paged_data($this->tables['SKUsProfileTable'], NULL, $this->tables['SKUsProfileTable']['tableKey'],
            "sku_list.sku_uid=sku_profiles.sku_uid", NULL);
          $rs['sku_profiles'] = array();
          foreach ($sku_profiles as $sku_profile) {
            if (($sku_profile['customer_id'] == NULL) || ($sku_profile['customer_id'] == $rs['group_info'][0]['customer_id'])) {
              $sku_uid = $sku_profile['sku_uid'];
              if (!isset($rs['sku_profiles'][$sku_uid])) {
                $rs['sku_profiles'][$sku_uid]['profiles'] = array();
                $rs['sku_profiles'][$sku_uid]['sku_name'] = $sku_profile['sku_name'];
                $rs['sku_profiles'][$sku_uid]['sku_type'] = $sku_profile['sku_type'];
              }
              $rs['sku_profiles'][$sku_uid]['profiles'][] = array(
                'sku_profile_name' => $sku_profile['sku_profile_name'],
                'sku_profile_uid' => $sku_profile['sku_profile_uid'],
                'isDefault' => $sku_profile['isDefault']);
            }
          }
          $rs['over_allocation_rules'] = $this->licgen->get_overprovisioning_group_rules($group_uid);
          $tmpCounts = $this->db->get_group_dependees_counts($group_uid);
          $rs['max_lic_node_count'] = $tmpCounts['number_of_nodes_count'];
          $rs['host_ids'] = $this->db->get_host_ids_for_group($group_uid);
          $rs['oldLicenseFilename'] = $this->licgen->getOldLicenseFileLocation($rs['group_info'][0]['customer_id'], $group_uid);
        }
        $rs['consumed_count'] = $this->db->get_consumed_available_sku_count_for_customer($_GET['customer_filter']);
        //useless?
        $rs['per_server_type_consumed_count'] = $this->db->get_consumed_available_sku_count_for_customer_per_server_type($_GET['customer_filter']);
        //AS - PS-HSS

        $rs['systems'] = $this->db->get_all_paged_data($this->tables['GroupsSystemMergeTable2'], $this->pager, $orders,
          ' customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values);

        build_groups_body_for_licensing($this->getContext(), $this->pager, $rs, $this->db->getLastError(), $action, $group_uid,
          $this->db->getEnumValues('groups', 'status'), $this->db->get_conf_releases_array());
        break;

      case 'skus_summary':
        $where_clause = ' sku_associations.bought_quantity<>0 AND sku_associations.customer_id=?';
        $num = $this->db->get_num_recs($this->tables['sku_associationsMergeTable'],
          " sku_list.sku_uid=sku_associations.sku_uid " . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values); // Get number of records
        $this->pager->setTotRecords($num); // Pager method calls
        $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['sku_associationsMergeTable']['tableKey']) : $this->tables['sku_associationsMergeTable']['orderBy']);
        $sku_associations = $this->db->get_all_paged_data($this->tables['sku_associationsMergeTable'], $this->pager, $orders,
          " sku_list.sku_uid=sku_associations.sku_uid " . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values);
        $rs['sku_associations'] = $sku_associations;
        $rs['consumed_count'] = $this->db->get_consumed_available_sku_count_for_customer($_GET['customer_filter']);
        //AS - PS-HSS
        $rs['per_server_type_consumed_count'] = $this->db->get_consumed_available_sku_count_for_customer_per_server_type($_GET['customer_filter']);
        $rs['opportunities_sku_associations'] = $this->db->get_all_paged_data($this->tables['opportunities_sku_associations'], NULL,
          $this->tables['opportunities_sku_associations']['orderBy'],
          " sku_opportunities_associations.opportunity_uid=opportunities.opportunity_uid AND opportunities.customer_id=? AND opportunities.lrs_status='loaded' ", $where_values);
        build_sku_associations_for_licensing($this->pager, $rs, $this->db->getLastError(), $action);
        break;

      case 'archive_licenses':
        $where_clause = ' generated_licenses.customer_id=?';
        $this->buildFilter('system_name', 'system_name', '=', $where_clause, $where_values);
        $this->buildFilter('cluster_name', 'cluster_name', '=', $where_clause, $where_values);
        $this->buildFilter('server_type', 'server_type', '=', $where_clause, $where_values);
        $this->buildFilter('as_cluster_uid', 'as_cluster_uid', '=', $where_clause, $where_values);
        $this->buildFilter('reason', 'reason', '=', $where_clause, $where_values);
        $this->buildFilter('version', 'bw_version', '=', $where_clause, $where_values);
        $this->buildFilter('is_group', 'is_group', '=', $where_clause, $where_values);
        $num = $this->db->get_num_recs($this->tables['generatedLicensesMerged'], $where_clause, $where_values); // Get number of records
        $this->pager->setTotRecords($num); // Pager method calls
        $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['generatedLicensesMerged']['tableKey']) : $this->tables['generatedLicensesMerged']['orderBy']);
        $filters = $this->db->get_all_paged_data($this->tables['generatedLicensesMerged'], null, $orders, $where_clause, $where_values, 'DESC');
        $generatedlicenses = $this->db->get_all_paged_data($this->tables['generatedLicensesMerged'], $this->pager, $orders, $where_clause, $where_values, 'DESC');
        $rs['generatedlicenses'] = $generatedlicenses;
        $license_generated_uid = (isset($_GET['license_generated_uid']) ? trim($_GET['license_generated_uid']) : NULL);
        if ($license_generated_uid != NULL) {
          $rs['generated_license'] = $this->licgen->getGeneratedLicense($license_generated_uid);
        }
        if ($license_generated_uid == NULL) {
          $license_generated_uid = (isset($_GET['license_generated_group_uid']) ? trim($_GET['license_generated_group_uid']) : NULL);
        }
        if ($license_generated_uid != NULL && isset($_GET['license_generated_group_uid'])) {
          $rs['generated_license'] = $this->licgen->getGeneratedGroupLicense($license_generated_uid);
        }
        build_archive_body_for_licensing($this->pager, $rs, $this->db->getLastError(), $action, $license_generated_uid, $filters);
        break;
    }

    unset($_SESSION['post'], $rs);
  }

  /**
   * Body::handle_licenseOverProvReasons()
   *
   * @return
   */
  function handle_licenseOverProvReasons() {
    $data = array();
    $data['overAllocationRuleReasons'] = $this->licgen->get_overprovisioning_rule_reasons();
    $data['overAllocationRuleReasonCounts'] = $this->licgen->get_overprovisioning_rule_reason_counts();
    $data['overAllocationRuleReasonGroupCounts'] = $this->licgen->get_overprovisioning_rule_reason_group_counts();
    if (isset($_GET['viewClusterList'])) {
      $data['viewClusterList'] = $this->db->get_all_paged_data($this->tables['skuOverAllocationRulesPerClusters'], NULL,
        $this->tables['skuOverAllocationRulesPerClusters']['orderBy'],
        ' sku_overprovisiong_rules.as_cluster_uid=as_clusters.as_cluster_uid AND as_clusters.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id AND sku_overprovisiong_rules.sku_overprovisiong_rule_reason_uid=' . $_GET['sku_overprovisiong_rule_reason_uid']);
      $data['viewGroupList'] = $this->db->get_all_paged_data($this->tables['skuOverAllocationRulesPerGroups'], NULL,
        $this->tables['skuOverAllocationRulesPerGroups']['orderBy'],
        ' sku_overprovisioning_rules_group.group_uid=groups.group_uid AND groups.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id AND sku_overprovisioning_rules_group.sku_overprovisiong_rule_reason_uid=' . $_GET['sku_overprovisiong_rule_reason_uid']);
    }
    build_license_overprov_reasons($data, isset($_GET['sku_overprovisiong_rule_reason_uid']) ? $_GET['sku_overprovisiong_rule_reason_uid'] : NULL);
    unset($_SESSION['post'], $rs);
  }

  function handle_licenseLicGenTemplates($ajax = false) {
    $templates = $this->licgen->get_license_templates_data();
    if (!$ajax) {
      build_license_generation_templates($templates);
      if (isset($_GET['edit'])) {
        build_license_generation_template_edit($_GET['edit'], $templates);
      } else {
        build_license_generation_template_edit();
      }
    } else {
      $return = $this->licgen->get_license_generation_template($_GET['lgct_uuid']);
      header('Cache-Control: no-cache, must-revalidate');
      header('Content-Type: application/json');
      echo $return;
    }
  }

  /**
   * Body::handle_licenseManualOpReasons()
   *
   * @return
   */
  function handle_licenseManualOpReasons() {
    $data = array();
    $data['ManualOpRuleReasons'] = $this->licgen->get_ManualOp_rule_reasons();
    $data['ManualOpRuleReasonCounts'] = $this->licgen->get_ManualOp_rule_reason_counts();
    if (isset($_GET['viewOpportunityList'])) {
      $data['viewOpportunityList'] = $this->db->get_all_paged_data($this->tables['opportunitiesManualOp'], NULL, $this->tables['opportunitiesManualOp']['orderBy'],
        ' opportunities.customer_id=customers.customer_id AND opportunities.manual_opportunity_rule_reason_uid=' . $_GET['manual_opportunity_rule_reason_uid']);
    }
    build_manual_opportunities_reasons($data, isset($_GET['manual_opportunity_rule_reason_uid']) ? $_GET['manual_opportunity_rule_reason_uid'] : NULL);
    unset($_SESSION['post'], $rs);
  }

  /**
   *
   */
  function handle_blueBanner() {
    if (empty($_SESSION['canManageLicenseInventory'])) {
      return;
    }
    $customer_id = filter_input(INPUT_GET, 'customer_filter');
    if (empty($customer_id)) {
      $where_clause = " customers.c_blue_banner IS NOT NULL ";
      $num = $this->db->get_num_recs($this->tables['customerTable'], $where_clause); // Get number of records
      $this->pager->setTotRecords($num); // Pager method calls
      $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['customerTable']['tableKey']) : $this->tables['customerTable']['orderBy']);
      $customer_info = $this->db->get_all_paged_data($this->tables['customerTable'], $this->pager, $orders, $where_clause);
      if (empty($customer_info)) {
        build_license_inventory_body(null);
        return;
      }
    } else {
      $customer_info[] = $this->db->getCustomer($customer_id);
    }
    build_blue_banners($customer_info);
    $edit = filter_input(INPUT_GET, 'edit', FILTER_SANITIZE_STRING);
    if (!is_null($edit)) {
      build_blue_banners_edit($customer_info[0], $edit);
    }
  }

  function handle_is_banners() {
    if (empty($_SESSION['superAdmin'])) {
      return;
    }
    $data = $this->db->getConfigTable();
    build_is_banners($data);
    $edit = filter_input(INPUT_GET, 'edit', FILTER_SANITIZE_STRING);
    if (!is_null($edit)) {
      build_is_banners_edit($data, $edit);
    } else {
      build_is_banners_edit($data, null);
    }
  }

  /**
   * Body::handle_manage_permissions()
   *
   * @return
   */
  function handle_manage_permissions() {
    $num = $this->db->get_num_recs($this->tables['permissionsTable']); // Get number of records
    $this->pager->setTotRecords($num); // Pager method calls
    $this->db->push_superadmin_permissions();
    $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['permissionsTable']['tableKey']) : $this->tables['permissionsTable']['orderBy']);

    $permissions = $this->db->get_all_paged_data($this->tables['permissionsTable'], $this->pager, $orders);
    for ($i = 0; $i < sizeof($permissions); ++$i) {
      $permissions[$i]['roles'] = $this->db->get_roles_by_permission_uid($permissions[$i]['permission_uid']);
    }

    $roles = $this->db->get_roles();
    build_manage_permissions($permissions, $roles);
    $this->pager->printPages();
  }

  /**
   * Body::handle_manage_lists()
   *
   * @return
   */
  function handle_manage_lists() {
    //render tickets to mailing list matrix
    if (isset($_GET['manageTickets'])) {
      $num = $this->db->get_num_recs($this->tables['mailingListTicketsTable']); // Get number of records
      $this->pager->setTotRecords($num); // Pager method calls
      $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['mailingListTicketsTable']['tableKey']) : $this->tables['mailingListTicketsTable']['orderBy']);

      $tickets = $this->db->get_all_paged_data($this->tables['mailingListTicketsTable'], $this->pager, $orders);
      for ($i = 0; $i < sizeof($tickets); ++$i) {
        $tickets[$i]['lists'] = $this->db->get_mailing_list_by_ticket_uid($tickets[$i]['ticket_uid']);
      }

      $lists = $this->db->get_lists();
      build_manage_lists($tickets, $lists);
      $this->pager->printPages();
    } else {
      //render regular mailing list management page with mailing lists creation, and user associations
      $lists = $this->db->get_lists();

      for ($i = 0; $i < sizeof($lists); ++$i) {
        $lists[$i]['members'] = $this->db->get_mailing_list_users($lists[$i]['list_uid']);
      }

      /*
       * Get member_id to lists associations with details from logins table
       */

      build_manage_mailing_lists($lists);
      //list selected, display form with content filled if lists still exists
      if (isset($_GET['list_uid']) && isset($lists[$_GET['list_uid']])) {

        $list = $lists[$_GET['list_uid']];
        $logins = $this->db->getUsers();
        //change logins key to use memberid
        foreach ($logins as $uid => $content) {
          $logins[$content['memberid']] = $content;
          unset($logins[$uid]);
        }
        //remove list users from login list
        foreach (array_keys($list['members']) as $memberid) {
          unset($logins[$memberid]);
        }

        build_list_edit($list, $logins);
      } else {
        //no list selected, show empty form and no users associated
        $list = array();
        $logins = $this->db->getUsers();
        //change logins key to use memberid
        foreach ($logins as $uid => $content) {
          $logins[$content['memberid']] = $content;
          unset($logins[$uid]);
        }

        build_list_edit($list, $logins, true);
      }
    }
  }

  /**
   * Body::handle_edit_roles()
   *
   * @return
   */
  function handle_edit_roles() {
    $num = $this->db->get_num_recs($this->tables['rolesTable']); // Get number of records
    $this->pager->setTotRecords($num); // Pager method calls
    $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['rolesTable']['tableKey']) : $this->tables['rolesTable']['orderBy']);

    $roles = $this->db->get_all_paged_data($this->tables['rolesTable'], $this->pager, $orders);
    $roles_counts = $this->db->get_roles_counts();

    build_edit_roles($roles, $roles_counts);
  }

  /**
   * Body::handle_uploadreport()
   *
   * @return
   */
  function handle_uploadreport() {
    build_license_report_upload();
    unset($_SESSION['post'], $rs);
  }

  /**
   * Body::handle_unknownhostids()
   *
   * @return
   */
  function handle_unknownhostids() {
    if (isset($_GET['hostIdValueSearch'])) {
      $where_clause = " AND (host_id1 like '%" . $_GET['hostIdValueSearch'] . "%' "
        . " OR host_id2 like '%" . $_GET['hostIdValueSearch'] . "%' "
        . " OR host_id3 like '%" . $_GET['hostIdValueSearch'] . "%' "
        . " OR host_id4 like '%" . $_GET['hostIdValueSearch'] . "%') ";
    } else {
      $where_clause = "";
    }

    $num = $this->db->get_num_recs($this->tables['unprocessedReportsTable'], 'status = ?' . $where_clause, array('unknown-host-id')); // Get number of records
    $this->pager->setTotRecords($num); // Pager method calls
    $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['unprocessedReportsTable']['tableKey']) : $this->tables['unprocessedReportsTable']['orderBy']);

    $unknownHostIds = $this->db->get_all_paged_data($this->tables['unprocessedReportsTable'], $this->pager, $orders, 'status = ?' . $where_clause, array('unknown-host-id'));
    build_unknownhostids_body($this->pager, $unknownHostIds, $this->db->getLastError());
    $this->pager->printPages();

    build_unknwon_host_id_edit();

    $rs['customerNameList'] = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_name', null, null, 'customer_name');
    $rs['customerIdList'] = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_id', null, null, 'customer_name');
    build_as_unknown_host_id_edit($rs);
  }

  /**
   * Body::handle_reporting()
   *
   * @return
   */
  function handle_reporting() {
    $reportMonths = $this->db->getReportingMonths();
    $serverTypes = $this->db->getEnumValues($this->tables['asClusterTable']['tableName'], 'server_type');
    $licensableEntities = $this->db->get_all_paged_data($this->tables['licensableEntitiesTable'], NULL, $this->tables['licensableEntitiesTable']['orderBy']);
    $products = $this->db->get_products_table();
    $rs = array();
    if (isset($_GET['customer_filter'])) {
      $rs['customer_id'] = $_GET['customer_filter'];
    }
    $rs['customerNameList'] = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_name', null, null, 'customer_name');
    $rs['customerIdList'] = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_id', null, null, 'customer_name');
    $rs['serverTypeList'] = $this->db->getEnumValues($this->tables['asClusterTable']['tableName'], 'server_type');
    $rs['reasonList'] = $this->db->getEnumValues($this->tables['generatedLicensesTable']['tableName'], 'reason');
    $rs['journalAction'] = $this->db->getEnumValues('journal_file_entries', 'action');
    $rs['journalEntry_type'] = $this->db->getEnumValues('journal_file_entries', 'entry_type');
    $rs['logins'] = $this->db->get_data_from_key('login', 'email');
    if (isset($_GET['reportType']) && $_GET['reportType'] === 'bwUsageReporting') {
      $groups = $this->db->get_configured_groups();
      foreach ($groups as $group) {
        $rs['groups_customers'][$group['customer_id']] = $group['customer_name'];
      }
    }
    $rs['releases'] = $this->db->getRows("SELECT * FROM releases AS r ORDER BY r.order DESC");
    $rs['products'] = $products;
    build_reporting_body($this->getContext(), $reportMonths, (isset($_GET['reportType']) ? $_GET['reportType'] : ''), $serverTypes, $licensableEntities, $rs);
    unset($_SESSION['post']);
  }

  /**
   * Body::handle_reporting()
   *
   * @return
   */
  function handle_licensingProgress() {
    $data = array();
    $where_values = array();
    $where_clause = " as_clusters.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id ";
    if (isset($_GET['filterStatus'])) {
      $where_clause .= "AND status =? ";
      $where_values[] = $_GET['filterStatus'];
    } else {
      $where_clause .= "AND status IN ('In Progress','Ready For Review') ";
    }
    if (isset($_GET['filterServerType'])) {
      $where_clause .= "AND server_type =? ";
      $where_values[] = $_GET['filterServerType'];
    }

    $num = $this->db->get_num_recs($this->tables['asClusterSystemMergeTable'], $where_clause, $where_values); // Get number of records
    $this->pager->setTotRecords($num); // Pager method calls
    $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['asClusterSystemMergeTable']['tableKey']) : $this->tables['asClusterSystemMergeTable']['orderBy']);

    $data['inWorkClusters'] = $this->db->get_all_paged_data($this->tables['asClusterSystemMergeTable'], $this->pager, $orders, $where_clause, $where_values);

    $data['progressCounts'] = $this->db->get_as_cluster_license_progress_counts();
    $statusArr = $this->db->getEnumValues('as_clusters', 'status');

    $clusters = $this->db->get_all_paged_data($this->tables['asClusterSystemMergeTable'], NULL, $orders, $where_clause, $where_values);
    $tmpArray = array();

    for ($i = 0; is_array($clusters) && $i < count($clusters); $i++) {
      $tmpArray[$clusters[$i]['customer_id']] = true;
    }
    $data['nbDistinctCustomers'] = count($tmpArray);

    build_licensing_progress_body($this->pager, $data, $this->db->getLastError(), $statusArr);
    unset($_SESSION['post']);
  }

  /**
   * Body::handle_reporting()
   *
   * @return
   */
  function handle_licensingProgressHistory() {
    $data = array();

    $data['byClusterData'] = $this->getContext()->getReportingDB()->getClusterLicenseMigrationHistorySummaryByReportDate();
    $data['byCustomerData'] = $this->getContext()->getReportingDB()->getCustomerLicenseMigrationHistorySummaryByReportDate();
    $data['byClusterStatus'] = $this->db->getEnumValues('as_clusters', 'status');

    $viewByCluster = true;
    if (isset($_GET['reportByType']) && ($_GET['reportByType'] == 'Customers')) {
      $viewByCluster = false;
    }
    build_licensing_progress_history_body($data, $viewByCluster);
    unset($_SESSION['post']);
  }

  /**
   * Body::handle_licenseSfdcOpportiuty()
   *
   * @return
   */
  function handle_licenseSfdcOpportiuty() {
    $filters = array();
    $lastModfiedDays = 180;
    //default filter to disabled
    if (!isset($_GET['modified_date_filter']) && !isset($_GET['status_filter']) && !isset($_GET['statusFilterDisabled'])) {
      $_GET['statusFilterDisabled'] = 1;
    }
    // Pre-process all the filters....
    if (isset($_GET['customer_filter'])) {
      $filters['customer_filter'] = $_GET['customer_filter'];
    }
    //validate integer and max value to prevent SFDC query errors
    if (isset($_GET['modified_date_filter'])) {
      if (is_int((int) $_GET['modified_date_filter']) && (int) $_GET['modified_date_filter'] <= 10000) {
        $lastModfiedDays = (int) $_GET['modified_date_filter'];
      }
    }
    if (isset($_GET['status_filter'])) {
      $filters['status_filter'] = $_GET['status_filter'];
    } else {
      $filters['status_filter'] = "Accepted,Shipped Partial";
    }
    if (isset($_GET['statusFilterDisabled'])) {
      $filters['statusFilterDisabled'] = 1;
    }
    $filters['lastModfiedDays'] = $lastModfiedDays;
    //ignore order start date
    if (!empty($_GET['OrderStartFilterDisabled'])) {
      $filters['OrderStartFilterDisabled'] = 1;
    } else {
      $filters['modified_date_filter'] = "LAST_N_DAYS:" . $lastModfiedDays;
    }
    $lrsOrderedBy = isset($_GET['order']) && preg_match("/^LRS/", $_GET['order']) ? true : false;

    $orderBy = ( (isset($_GET['order']) && !preg_match("/^LRS/", $_GET['order'])) ? trim($_GET['order']) : "Opportunity__r.Order_Start_Date__c");
    $orderBy .= " ";
    $orderBy .= ( (isset($_GET['vert']) && !preg_match("/^LRS/", $_GET['order'])) ? trim($_GET['vert']) : "DESC");
    $filters['order_by'] = $orderBy;

    $orderByProduct = ( (isset($_GET['order']) && !preg_match("/^LRS/", $_GET['order'])) ? trim($_GET['order']) : "Opportunity.Order_Start_Date__c");
    $orderByProduct .= " ";
    $orderByProduct .= ( (isset($_GET['vert']) && !preg_match("/^LRS/", $_GET['order'])) ? trim($_GET['vert']) : "DESC");
    $orderByProduct = str_replace("Opportunity__r", "Opportunity", $orderByProduct);
    $filters['order_by product'] = $orderByProduct;

    // Get the list of all opportinuties based on those filters from SFDC
    $sfdcConn = new SalesforceConnector();
    $sfdcConn->connect();
    $data['opportunityList'] = $sfdcConn->getCustomerOpportunities(false, $filters);
    $data['filters'] = $filters;
    if (isset($_GET['opportunityId'])) {
      // Get the data for one specific opportunity.
      $data['opportunityId'] = $_GET['opportunityId'];
      $data['viewOpportunityId'] = $sfdcConn->getOpportunityFeatures(false, $_GET['opportunityId']);
    }
    $data = $this->sortData($data, $orderByProduct);

    // Lookup in the LRS database for opportunities.
    $data['lrsOpportunitiesData'] = $this->licgen->getOpportunitiesData($data['opportunityList']);
    $oppUtil = new OpportunityUtilities();
    $oppUtil->detectOpportinityModifications($data['lrsOpportunitiesData'], $data['opportunityList']);
    build_licensing_sfdc_opportunities_body($data);
  }

  private function comp($a, $b) {
    $aValue = ($this->customOrderBy == "Opportunity.Id") ? key($a) : $a[$this->customOrderBy];
    $bValue = ($this->customOrderBy == "Opportunity.Id") ? key($b) : $b[$this->customOrderBy];
    if ($this->customVert == "ASC") {
      return ($aValue < $bValue) ? -1 : 1;
    } else {
      return ($aValue > $bValue) ? -1 : 1;
    }
  }

  /**
   * Body::sortData($data, $orderByProduct)
   * Since the query from SalesForce which returns sorted data does not work properly or for List field
   * returns the order in the list, so data must be sorted here manually.
   *
   * @return sorted data
   */
  function sortData($data, $orderByProduct) {
    $arr = explode(" ", $orderByProduct);
    $this->customVert = $arr[1];
    switch ($arr[0]) {
      case "Opportunity.Account.AccountNumber":
        $this->customOrderBy = "CustomerId";
        break;
      case "Opportunity.Account.Name":
        $this->customOrderBy = "CustomerName";
        break;
      case "Opportunity.Id":
        $this->customOrderBy = "Opportunity.Id";
        break;
      case "Opportunity.Reseller__r.AccountNumber":
        $this->customOrderBy = "ResellerId";
        break;
      case "Opportunity.Name":
        $this->customOrderBy = "OpportunityName";
        break;
      case "Opportunity.Order_Number__c":
        $this->customOrderBy = "OpportunityOrderNumber";
        break;
      case "Opportunity.CreatedDate":
        $this->customOrderBy = "OpportunityCreatedDate";
        break;
      case "Opportunity.Order_Start_Date__c":
        $this->customOrderBy = "OpportunityOrderStartDate";
        break;
      case "Opportunity.Order_Status__c":
        $this->customOrderBy = "OpportunityOrderStatus";
        break;
    }
    uasort($data['opportunityList'], array($this, "comp"));
    return $data;
  }

  /**
   * Body::handle_journaling()
   *
   * @return
   */
  function handle_journaling() {
    $where_values = array();
    $where_clause = "";
    $this->buildFilter('filterLogin', 'login', '=', $where_clause, $where_values);
    $this->buildFilter('filterAction', 'action', '=', $where_clause, $where_values);
    $this->buildFilter('filterEntry_type', 'entry_type', '=', $where_clause, $where_values);
    $this->buildFilter('filterFrom', 'journal_file_entry_timestamp', '>=', $where_clause, $where_values);
    $this->buildFilter('filterTo', 'journal_file_entry_timestamp', '<=', $where_clause, $where_values, true);

    if (isset($_GET['customer_filter'])) {
      $journal_ids = $this->db->get_journal_entries_by_customer($_GET['customer_filter']);
      if (!empty($where_clause)) {
        $where_clause .= " AND journal_file_entry_uid IN (" . join(",", $journal_ids) . ") ";
      } else {
        $where_clause .= " journal_file_entry_uid IN (" . join(",", $journal_ids) . ") ";
      }
    }

    $num = $this->db->get_num_recs($this->tables['journalFileEntries'], $where_clause, $where_values); // Get number of records
    //add where clause to filter the records
    $this->pager->setTotRecords($num); // Pager method calls
    $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['journalFileEntries']['tableKey']) : $this->tables['journalFileEntries']['orderBy']);
    $entries = $this->db->get_all_paged_data($this->tables['journalFileEntries'], $this->pager, $orders, $where_clause, $where_values, 'DESC');
    if ($num > 0) {
      for ($i = 0; $i < count($entries); $i++) {
        $tmpData = $this->db->get_all_paged_data($this->tables['journalFileData'], null, $this->tables['journalFileData']['orderBy'], 'journal_file_entry_uid=?',
          $entries[$i]['journal_file_entry_uid']);
        $entries[$i]['data'] = $tmpData;
      }
    }
    $filterLists['action'] = $this->db->getEnumValues($this->tables['journalFileEntries']['tableName'], 'action');
    $filterLists['entry_type'] = $this->db->getEnumValues($this->tables['journalFileEntries']['tableName'], 'entry_type');
    $filterLists['logins'] = $this->db->get_data_from_key('login', 'email');
    build_journaling_body($this->pager, $entries, $this->db->getLastError(), $filterLists);
    unset($_SESSION['post']);
  }

  private function buildFilter($get, $field, $operator, &$where_clause, &$where_values, $isToDateQueryValue = false) {
    if (isset($_GET[$get])) {
      if (!empty($where_clause)) {
        $where_clause .= " AND $field $operator? ";
      } else {
        $where_clause .= " $field $operator? ";
      }
      $value = $_GET[$get];
      if ($isToDateQueryValue) {
        $value = $this->db->getToDateQueryValue($value);
      }
      $where_values[] = $value;
    }
  }

  /**
   * Body::handle_journaling()
   *
   * @return
   */
  function handle_generic_landing_page($operation) {
    build_generic_landing_page_body($operation);
    unset($_SESSION['post']);
  }
  /*
   * Logs the user last activity to monitor when doing maintenance
   *
   *
   */

  function log_activity() {
    //var_dump($_SERVER['QUERY_STRING']);
    $newuser = $this->db->check_existing_user_activity($_SESSION['sessionID']);
    $this->db->update_user_activity($newuser, $_SESSION['sessionID'], $_SERVER['QUERY_STRING']);
  }

  function handle_undelivered_inv() {
    $customer = '';
    if (!empty($_GET['customer_filter'])) {
      $customer = $_GET['customer_filter'];
    }
    $cust_type = !empty($_POST['cust_type']) ? $_POST['cust_type'] : "All";
    $delivered = !empty($_POST['delivered']) ? $_POST['delivered'] : "All";
    $sys_prod_type = !empty($_POST['sys_prod_type']) ? $_POST['sys_prod_type'] : "All";

    $num = count($this->db->get_undelivered_data($customer, $cust_type, $delivered, $sys_prod_type)); // Get number of records
    $this->pager->setTotRecords($num); // Pager method calls
    $inv = $this->db->get_undelivered_data($customer, $cust_type, $delivered, $sys_prod_type, $this->pager);
    $skus = $this->db->get_production_SKUs($sys_prod_type);
    build_undelivered_inv($inv, $skus, $this->pager);
  }

  function handle_sku_profile_import() {
    if (!empty($_SESSION['canManageLicenseInventory'])) {
      $customers = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_name', null, null, 'customer_name');
      $customerIds = $this->db->get_data_from_key($this->tables['customerTable']['tableName'], 'customer_id', null, null, 'customer_name');
      $productsList = $this->db->get_products_table();

      build_import_profile_upload_form($customers, $customerIds, $productsList);
    }
  }

  function handle_welcome() {
    //dummy function, should not be called
  }

  function handle_is_reg_test_locked() {
    echo file_exists(config::getConfig()->getTestLicenseTmpPath("/_regtest.lck"));
  }

  function handle_set_homepage() {
    if (isset($_GET['link']) && isset($_SESSION['sessionID'])) {
      $this->db->update_homepage($_SESSION['sessionID'], $_GET['link']);
      $_SESSION['homepage'] = $_GET['link'];
      echo true;
    } else {
      echo false;
    }
  }

  function handle_git_log() {
    $conf = config::getConfig();
    $logFile = $conf->getTmpPath("/gitLog.txt");
    $repo = $conf->getRepoPath();
    shell_exec("git --git-dir {$repo} log --pretty=format:%h,%ai,%s -10 > {$logFile}");

    build_git_log($logFile);
  }

  function handle_teaser_profiles() {
    if (!empty($_SESSION['canManageTeaserProfiles'])) {
      // releases/quantities managed via ajax overlay
      //get all teasers
      $num = $this->db->get_num_recs($this->tables['teaserProfiles']); // Get number of records
      $this->pager->setTotRecords($num); // Pager method calls
      $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['teaserProfiles']['tableKey']) : $this->tables['teaserProfiles']['orderBy']);
      $teasers = $this->db->get_all_paged_data($this->tables['teaserProfiles'], $this->pager, $orders);
      //get all customers
      for ($i = 0; $i < count($teasers); $i++) {
        $teasers[$i]['use_count'] = $this->db->count_teaser_uses($teasers[$i]['teaser_profile_uid']);
      }
      build_teaser_profiles($teasers, $this->pager);
    }
  }

  function handle_teaser_view() {
    $this->getContext()->obsolete("View teaser profiles by cluster");
    // Not optimal, memory limit is busted in the first loop to build the $view.
    ini_set('memory_limit', '1536M');
    $view = $this->db->get_all_paged_data($this->tables['teaser_profile_cluster_view'], null, $this->tables ['teaser_profile_cluster_view'] ['orderBy'],
      ' teaser_profiles.teaser_profile_uid=teaser_customer_profiles.teaser_profile_uid');
    for ($i = 0; $i < sizeof($view); $i ++) {
      unset($view [$i] ['teaser_customer_profiles_uid']);
      unset($view [$i] ['teaser_profile_content_uid']);
      unset($view [$i] ['sku_profile_uid']);
      $view [$i] ['cluster_info'] = $this->db->get_cluster($view [$i] ['as_cluster_uid']);
      $view [$i] ['customer_info'] = $this->db->getCustomer($view [$i] ['customer_id']);
    }
    $view = array_unique($view, SORT_REGULAR);
    if (isset($_GET ['filter'])) {
      foreach ($view as $key => $data) {
        if ($view [$key] ['teaser_profile_name'] != (string) $_GET ['filter']) {
          unset($view [$key]);
        }
      }
    }

    $showDetails = false;
    if (isset($_GET ['toggleViewDetails']) && ($_GET ['toggleViewDetails'] == 1)) {
      $showDetails = true;
    }

    build_teaser_view($view, $showDetails);
  }

  /**
   * Function getting the clusters data where the license is outdated  to build the view outdated licenses
   */
  function handle_view_outdated_licenses() {
    $where_clause = ' as_clusters.outdated_license="true"';
    $where_values = array();

    $num = $this->db->get_num_recs($this->tables['asClusterSystemMergeTable'],
      ' as_clusters.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values); // Get number of records
    $this->pager->setTotRecords($num); // Pager method calls
    $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['asClusterSystemMergeTable']['tableKey']) : $this->tables['asClusterSystemMergeTable']['orderBy']);

    $clusters = $this->db->get_all_paged_data($this->tables['asClusterSystemMergeTable'], $this->pager, $orders,
      ' as_clusters.system_uid=systems.system_uid AND customers.customer_id=systems.customer_id ' . ($where_clause != '' ? ' AND ' . $where_clause : ''), $where_values);

    if ($num > 0) {
      $countsByClusters = $this->db->get_as_cluster_dependees_counts();

      for ($i = 0; $i < count($clusters); $i++) {
        $as_cluster_uid = $clusters[$i]['as_cluster_uid'];
        $host_id_count = 0;
        $number_of_nodes_count = 0;
        if (isset($countsByClusters[$as_cluster_uid])) {
          $host_id_count = $countsByClusters[$as_cluster_uid]['host_id_count'];
          $number_of_nodes_count = (isset($countsByClusters[$as_cluster_uid]['number_of_nodes_count']) && $countsByClusters[$as_cluster_uid]['number_of_nodes_count'] != "" ? $countsByClusters[$as_cluster_uid]['number_of_nodes_count'] : 0);
        }
        $clusters[$i]['host_id_count'] = $host_id_count;
        $clusters[$i]['node_per_clusters'] = (($clusters[$i]['node_per_clusters'] == NULL || $clusters[$i]['node_per_clusters'] == "") ? $number_of_nodes_count : $clusters[$i]['node_per_clusters']);
        $clusters[$i]['has_license'] = false;
      }
    }
    build_view_outdated_licenses($clusters);
    $this->pager->printPages();
  }

  /**
   * Function getting the clusters data where the license is outdated  to build the view outdated licenses
   */
  function handle_view_duplicated_hostids() {

    $num = $this->db->get_num_recs($this->tables['duplicatedHostIDsTable'],
      ' a.host_id=b.host_id AND a.as_cluster_uid=c.as_cluster_uid AND c.system_uid=d.system_uid AND d.customer_id=e.customer_id and c.server_type IN (\'as\',\'ps-hss\',\'psa\') '); // Get number of records
    $this->pager->setTotRecords($num);
    $orders = (isset($_GET['order']) ? array(trim($_GET['order']), $this->tables['duplicatedHostIDsTable']['tableKey']) : $this->tables['duplicatedHostIDsTable']['orderBy']);

    $data = $this->db->get_all_paged_data($this->tables['duplicatedHostIDsTable'], $this->pager, $orders,
      ' a.host_id=b.host_id AND a.as_cluster_uid=c.as_cluster_uid AND c.system_uid=d.system_uid AND d.customer_id=e.customer_id and c.server_type IN (\'as\',\'ps-hss\',\'psa\') ',
      null);

    $this->pager->printPages();
    build_view_duplicated_hostids($data);
    $this->pager->printPages();
  }

  /**
   * Function exposing various resync scripts with extermal systems
   */
  function handle_resync_tools() {

    if (!empty($_SESSION['canResyncCustomers'])) {
      build_resync_tools();
    }
  }

  /**
   * function getting the filter and data for the product profile view
   */
  function handle_product_profile_view() {
    $uid = isset($_GET['sku_profile_uid']) ? $_GET['sku_profile_uid'] : false;
    $view['clusters'] = $this->db->get_product_profile_view_data($uid);
    $view['groups'] = $this->db->get_product_profile_view_groups_data($uid);
    $view['overAllocations'] = $this->db->get_product_profile_view_over_allocations_data($uid);
    $view['teasers'] = $this->db->get_product_profile_view_teasers_data($uid);
    $showDetails = isset($_GET['toggleViewDetails']) && ($_GET['toggleViewDetails'] == 1);
    build_product_profile_view($view, $showDetails);
  }

  /**
   *
   */
  function handle_reset_customers_view() {
    $conf = config::getConfig();
    //get list of customers ready for reset
    $filter_reset = true;
    if (isset($_GET['show_locked_only']) && $_GET['show_locked_only'] === '1') {
      $filter_reset = false;
    }
    $rs['resetables'] = $this->getContext()->getReportingDB()->get_Resetable_Customers($filter_reset);

    //for each customer complement info with skus consummed to calculate platform
    foreach ($rs['resetables'] as $uid => $customer) {
      $rs['resetables'][$uid]['consumed'] = array();
      if (!empty($customer['customer_id'])) {
        //get all skus per server and merge totals
        $consumed = $this->db->get_consumed_available_sku_count_for_customer_per_server_type($customer['customer_id']);
        $rs['resetables'][$uid]['counts'] = $this->db->getCustomerCounts($customer['customer_id']);
        foreach ($consumed as $server) {
          foreach ($server as $sku_uid => $sku_content) {
            if (!empty($rs['resetables'][$uid]['consumed'][$sku_uid])) {
              $rs['resetables'][$uid]['consumed'][$sku_uid] = max($rs['resetables'][$uid]['consumed'][$sku_uid], (int) $sku_content['consumed']);
            } else {
              $rs['resetables'][$uid]['consumed'][$sku_uid] = (int) $sku_content['consumed'];
            }
          }
        }
      }
    }
    $products = $this->db->get_products_table();

    foreach ($conf->getValue('platform', 'mandatory_platform_products') as $mandatory_product) {
      foreach ($products as $key => $content) {
        if ($content['product_code'] == $mandatory_product) {
          $sku_uid = $key;
        }
      }
      $rs['mandatory_platform'][$mandatory_product]['sku_uid'] = $sku_uid;
      $rs['mandatory_platform'][$mandatory_product]['product_name'] = $products[$sku_uid]['name'];
    }
    foreach ($conf->getValue('platform', 'optional_platform_products') as $optional_product) {
      foreach ($products as $key => $content) {
        if ($content['product_code'] == $optional_product) {
          $sku_uid = $key;
        }
      }
      $rs['optional_platform'][$optional_product]['sku_uid'] = $sku_uid;
      $rs['optional_platform'][$optional_product]['product_name'] = $products[$sku_uid]['name'];
    }
    foreach ($conf->getValue('platform', 'platform_products') as $platform_product) {
      foreach ($products as $key => $content) {
        if ($content['product_code'] == $platform_product) {
          $sku_uid = $key;
        }
      }
      $rs['platform'][$platform_product]['sku_uid'] = $sku_uid;
      $rs['platform'][$platform_product]['product_name'] = $products[$sku_uid]['name'];
    }
    build_reset_customers_view($rs, $filter_reset);
  }

  function handle_auditing_report_view() {
    $audit_util = new Auditing($this->getContext());
    build_audit_report_header();
    if (isset($_GET['from']) && isset($_GET['to'])) {
      $audit_util->set_from_to($_GET['from'], $_GET['to']);
      $customers = $audit_util->select_customers();
      $this->pager->setTotRecords(sizeof($customers));
      $this->pager->printPages();
      $audit_util->set_pager($this->pager);
      $audit_util->build_sku_translate_array();
      $audit_util->preselect_opportunities();
      $audit_util->get_cluster_product_history();
      $audit_util->preselect_clusters();

      $data['customers'] = $audit_util->get_customers();
      $data['opportunities'] = $audit_util->get_opportunities();
      $data['clusters'] = $audit_util->get_clusters();

      $customers_table = $this->db->get_all_paged_data($this->tables['customerTable'], null, array('customer_name'), null);
      foreach ($customers_table as $customer) {
        $data['customers_table'][$customer['customer_id']] = $customer;
      }
      $skus = $this->db->get_all_paged_data($this->tables['SKUsTable'], null, array('product_code'), null);
      foreach ($skus as $sku) {
        $data['skus'][$sku['sku_uid']] = $sku;
      }
      $data['customers_products'] = $audit_util->get_products_by_customers();
      build_audit_report_body($data);
      $this->pager->printPages();
    }
  }

  function handle_usage_report_view() {
    $server_self = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
    $operation = filter_input(INPUT_GET, 'operation', FILTER_SANITIZE_STRING);
    $dateType = filter_input(INPUT_GET, 'dateType');
    if (empty($dateType)) {
      $dateType = "reported_date";
    }
    $temp = filter_input(INPUT_GET, 'phonehome');
    $includePhoneHome = !empty($temp);
    if (!isset($_GET['phonehome'])) {
      $includePhoneHome = true;
    }
    $temp = filter_input(INPUT_GET, 'others');
    $includeOthers = !empty($temp);
    if (!isset($_GET['others'])) {
      $includeOthers = true;
    }
    $customerFilter = filter_input(INPUT_GET, 'customer_filter', FILTER_SANITIZE_STRING);
    $this->baselink = "{$server_self}?operation={$operation}" . (!empty($customerFilter) ? "&customer_filter={$customerFilter}" : "");
    $active = isset($_GET['customer']) ? 2 : 1;
    $tabs = new TabBuilder();
    $fromto = false;
    if (isset($_GET['from']) && isset($_GET['to'])) {
      $fromto = true;
    }
    $tabs->addTab("Recent Reports", 1, $this->baselink . "&recent=1")
      ->addTab("Customer Reports", 2, $this->baselink . "&customer=1")
      ->setActiveTab($active)
      ->renderTabs();
    if ($active === 2) {
      if (!isset($_GET['customer_filter'])) {
        build_license_inventory_body(null);
        return;
      }
      build_usage_report_header(false, $includePhoneHome, $includeOthers, $dateType);
    } else {
      if (isset($_GET['from']) && !isset($_GET['to'])) {
        $_GET['to'] = $_GET['from'];
        $fromto = true;
      }
      build_usage_report_header(true, $includePhoneHome, $includeOthers, $dateType);
    }
    $customer_id = filter_input(INPUT_GET, 'customer_filter', FILTER_SANITIZE_STRING);
    if ($fromto) {
      $from = filter_input(INPUT_GET, 'from');
      $to = filter_input(INPUT_GET, 'to');
      if (empty($to)) {
        $to = $from;
      }
    } else {
      $from = $to = date("Y-m-d");
    }
    $filter = array('report_uid', 'customer_name', 'customer_id', 'system_name', 'as_cluster_name', 'reported_date', 'report_date', 'report_year', 'report_month', 'ra_sender_email');
    //zero based lines
    $lines = array(1, 5, 6, 7, 8, 9);
    $report = new GetCustomerTechSupports($this->getContext(), $customer_id);
    $report->setFromYearMonth($from)
      ->setToYearMonth($to)
      ->setToScreen()
      ->setFilter($filter)
      ->setDateType($dateType)
      ->setLinesFilter($lines);
    if ($includePhoneHome) {
      $report->includePhoneHome();
    }
    if ($includeOthers) {
      $report->includeOthers();
    }
    $reportsCount = $report->getRecords(true);
    $this->pager->setTotRecords($reportsCount);
    $this->pager->printPages();
    if ($reportsCount > 0) {
      $reports = $report->getRecords(false, $this->pager->getOffset(), $this->pager->getLimit());
      build_usage_report_view($reports);
      $this->pager->printPages();
    }
  }

  function handle_licgen_reasons_report_view() {

    if (!isset($_GET['from'])) {
      $_GET['from'] = date('Y-m-d', strtotime("now -90 days"));
    }
    if (!isset($_GET['to'])) {
      $_GET['to'] = date('Y-m-d');
    }
    build_audit_report_header();
    if (isset($_GET['from']) && isset($_GET['to'])) {
      $where = " licenses_generated.as_cluster_uid=as_clusters.as_cluster_uid AND licenses_generated.customer_id=customers.customer_id AND licenses_generated.generated_on_date >= ? AND licenses_generated.generated_on_date <=? ";
      if (empty($_GET['hideDecommissioned'])) {
        $where .= " AND as_clusters.status!='Decommissioned' ";
      }
      $reasons = $this->db->getEnumValues($this->tables['generatedLicensesTable']['tableName'], 'reason');
      $licenses = $this->db->get_all_paged_data($this->tables['generatedLicensesTableMergeCustomers'], null, $this->tables['generatedLicensesTableMergeCustomers']['orderBy'],
        $where, array($_GET['from'], $this->db->getToDateQueryValue($_GET['to'])));
      //build primary report
      $arrReasons = array();
      foreach ($reasons as $reason) {
        $arrReasons[$reason] = array();
      }
      foreach ($licenses as $license) {
        $arrReasons[$license['reason']][$license['license_generated_uid']] = $license;
      }
      build_license_generation_reasons($arrReasons, $_GET['from'], $_GET['to']);
    }
    if (isset($_GET['reason'])) {
      $reasonFilter = urldecode($_GET['reason']);
      $this->pager->setTotRecords(sizeof($arrReasons[$reasonFilter]));
      build_license_generation_reasons_details(array_slice($arrReasons[$reasonFilter], $this->pager->getOffset(), $this->pager->getLimit()), $reasonFilter);
      $this->pager->printPages();
    }
  }

  function handle_vm_report_view() {
    build_audit_report_header();
    if (isset($_GET['from']) && isset($_GET['to'])) {
      $this->getContext()->obsolete("View VM Report");
      $results = $this->getContext()->getReportingDB()->fetch_vm_reporting($_GET['from'], $_GET['to'], $this->pager);
      $this->pager->printPages();
      build_vm_report_body($results);
      $this->pager->printPages();
    }
  }

  function handle_auditing_servers_report_view($default_no_return = true) {
    $types = array('production', 'lab');
    $max_release = null;
    $active_statuses = array('Active', 'Jeopardy - Collections', 'Jeopardy - Maintenance');
    if (!isset($_GET['customer_filter'])) {
      $customers = $this->db->get_all_paged_data($this->tables['customerTable'], null, array('customer_name'), null);
    } else {
      $customers = $this->db->get_all_paged_data($this->tables['customerTable'], null, array('customer_name'), ' customer_id=?', array($_GET['customer_filter']));
    }
    foreach ($customers as $id => $customer) {
      if (in_array($customer['sfdc_status'], $active_statuses, true)) {
        $customers[$id]['inventory'] = $this->db->get_customer_inventory($customer['customer_id'], $types);
        $customers[$id]['completed_servers'] = $this->getContext()->getReportingDB()->fetch_customer_completed_servers($customer['customer_id'], $types);
        $customers[$id]['max_release'] = $this->getContext()->getReportingDB()->fetch_customer_max_release($customer['customer_id'], $types);
        $max_release = $customers[$id]['max_release'];
        if (empty($customers[$id]['inventory'])) {
          unset($customers[$id]);
        }
      } else {
        unset($customers[$id]);
      }
    }
    if (!isset($_GET['customer_filter'])) {
      $product_server_matrix = $this->getContext()->getReportingDB()->fetch_product_to_server_by_release($types);
    } else {
      $product_server_matrix = $this->getContext()->getReportingDB()->fetch_product_to_server_by_release($types, array(), array(), $max_release);
    }

    if ($default_no_return) {
      build_auditing_servers_report_view($customers, $product_server_matrix);
    } else {
      return array('customers' => $customers, 'PSM' => $product_server_matrix);
    }
  }

  function handle_view_groups() {
    $groups = $this->db->get_configured_groups();
    $this->pager->setTotRecords(sizeof($groups));
    $this->pager->printPages();
    $results = array_slice($groups, $this->pager->getOffset(), $this->pager->getLimit(), true);
    //get reports based on reported date between from and to
    //select only reports with report count > 2 or duplicate set to yes
    build_view_groups($results);
    $this->pager->printPages();
  }

//end handle_view_groups

  function handle_reset_clones() {
    $clones = $this->db->getClusterClones();
    $this->pager->setTotRecords(sizeof($clones));
    $this->pager->printPages();
    $results = array_slice($clones, $this->pager->getOffset(), $this->pager->getLimit(), true);
    //get reports based on reported date between from and to
    //select only reports with report count > 2 or duplicate set to yes
    build_reset_clones($results);
    $this->pager->printPages();
  }

  /**
   * display the NFM releases management page
   */
  function handle_nfm_releases() {
    if (Auth::hasPermission(Auth::PERMISSION_MANAGE_INVENTORY)) {
      $nfmReleases = $this->db->getNfmReleases();
      $this->pager->setTotRecords(sizeof($nfmReleases));
      $this->pager->printPages();
      build_nfm_releases($nfmReleases);
      $this->pager->printPages();
      $nfm_release_uid = filter_input(INPUT_GET, 'edit', FILTER_SANITIZE_STRING);
      if (!is_null($nfm_release_uid)) {
        build_nfm_releases_edit("Edit", $nfmReleases, $nfm_release_uid);
      } else {
        build_nfm_releases_edit("Add", $nfmReleases);
      }
    }
  }

  /**
   *
   */
  function getUniqueUUID() {
    $newUUID = $this->db->generate_new_node_UUID();
    header('Cache-Control: no-cache, must-revalidate');
    header('Content-Type: application/json');
    echo json_encode($newUUID);
  }

  function handle_merge_profiles() {
    $settings_for_profile1 = $this->db->getRows(
      "SELECT sku_content.licensable_entity_uid, licensable_entities.licensable_entity_name, sku_profile_content_release_info.sku_profile_content_release_info_uid, sku_profile_content_release_info.applicable_release, sku_profile_content_release_info.relative_quantity, sku_profile_content_release_info.pack_name, sku_profile_content_release_info.include_in_license "
      . "FROM sku_profile_content_release_info, sku_profile_content, sku_content, licensable_entities "
      . "WHERE sku_profile_content_release_info.sku_profile_content_uid=sku_profile_content.sku_profile_content_uid "
      . "AND sku_content.licensable_entity_uid=licensable_entities.licensable_entity_uid "
      . "AND sku_profile_content.sku_profile_uid=" . $_GET['puid1'] . " "
      . "AND sku_content.sku_content_uid=sku_profile_content.sku_content_uid "
      . "ORDER BY sku_profile_content_release_info.applicable_release ASC");

    $settings_for_profile2 = $this->db->getRows(
      "SELECT sku_content.licensable_entity_uid, licensable_entities.licensable_entity_name, sku_profile_content_release_info.sku_profile_content_release_info_uid, sku_profile_content_release_info.applicable_release, sku_profile_content_release_info.relative_quantity, sku_profile_content_release_info.pack_name, sku_profile_content_release_info.include_in_license "
      . "FROM sku_profile_content_release_info, sku_profile_content, sku_content, licensable_entities "
      . "WHERE sku_profile_content_release_info.sku_profile_content_uid=sku_profile_content.sku_profile_content_uid "
      . "AND sku_content.licensable_entity_uid=licensable_entities.licensable_entity_uid "
      . "AND sku_profile_content.sku_profile_uid=" . $_GET['puid2'] . " "
      . "AND sku_content.sku_content_uid=sku_profile_content.sku_content_uid "
      . "ORDER BY sku_profile_content_release_info.applicable_release ASC");

// generic data
    $genData = array();
    $genData['nbLEs'] = 0;
    $genData['nbReleases'] = 0;
    $profile1Data = $this->db->get_sku_profile($_GET['puid1']);
    $genData['p1'] = $profile1Data;
    $profile2Data = $this->db->get_sku_profile($_GET['puid2']);
    $genData['p2'] = $profile2Data;

    foreach ($settings_for_profile1 as $profItem) {
      $LEuid = $profItem['licensable_entity_uid'];
      $LEName = $profItem['licensable_entity_name'];
      $Release = $profItem['applicable_release'];

      if (!isset($genData['LEs'][$LEName])) {
        $genData['LEs'][$LEName]['LEuid'] = $LEuid;
        $genData['nbLEs'] ++;
      }

      if (!isset($genData['Releases'][$Release])) {
        $genData['Releases'][$Release] = $Release;
        $genData['nbReleases'] ++;
      }
    }
    ksort($genData['LEs']);
    krsort($genData['Releases']);

// re-organize profile data
    $LEReleaseItems = array();
    foreach ($settings_for_profile1 as $profItem) {
      $x = $profItem['applicable_release'];
      $y = $profItem['licensable_entity_uid'];
      $LEReleaseItems[$x][$y]['p1'] = $profItem;
    }
    foreach ($settings_for_profile2 as $profItem) {
      $x = $profItem['applicable_release'];
      $y = $profItem['licensable_entity_uid'];
      $LEReleaseItems[$x][$y]['p2'] = $profItem;
    }

    build_profile_merge_form($genData, $LEReleaseItems);
  }

  function handle_sku_configuration() {
    $sku = $this->db->get_all_properties($this->tables['SKUsTable'], isset($_GET['sku_uid']) ? $_GET['sku_uid'] : 0);
    $sku_profile['sku_profile_uid'] = isset($_GET['sku_profile_uid']) ? $_GET['sku_profile_uid'] : 0;
    if (!isset($_GET['sku_profile_uid'])) {
      $where_clause = 'sku_profiles.sku_uid=? and sku_list.sku_uid=sku_profiles.sku_uid';
      $where_values = array($_GET['sku_uid']);
      $SKUProfileList = $this->db->get_all_paged_data($this->tables['SKUsProfileTable'], NULL, $this->tables['SKUsProfileTable']['tableKey'], $where_clause, $where_values);
      $sku_profile['sku_profile_name'] = isset($_GET['sku_profile_uid']) ? $_GET['sku_profile_uid'] : $sku['sku_name'] . " - " . count($SKUProfileList);
    } else {
      $SKUProfileList = array();
      $sku_profile = $this->db->get_all_properties($this->tables['SKUsProfileTable1'], $_GET['sku_profile_uid']);
    }
    build_sku_profile_edition_form($sku, $sku_profile, $this->db->get_all_paged_data($this->tables['customerTable'], null, $this->tables['customerTable']['orderBy']),
      $SKUProfileList);
  }
}
