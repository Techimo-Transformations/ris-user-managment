<?php
/**
 * Plugin Name: Plugin User Data
 * Plugin URI: https://www.techimo.co.in/
 * Description: Manage user for LearnDash LMS & Food Safety. Both plugin are saprate each other.  
 * Version: 1.0
 * Author: Techimo Transformations
 * Author URI: https://www.techimo.co.in/
 */

defined( 'ABSPATH' ) || die();
/**
 * Register Custom Menu
 */

function RegisterProjectMenu() {
	add_menu_page('','Plugin User Data', 'manage_options', 'projectmang', 'ProjectDashboard', 'dashicons-welcome-view-site', 6 );
	add_submenu_page('projectmang', '', 'Add sites', 'manage_options', 'projectmang' ,'ProjectDashboard' );
	add_submenu_page('projectmang', '', 'Import User', 'manage_options', 'add-site-plugin' ,'importUserCallback' );
	
}
add_action( 'admin_menu', 'RegisterProjectMenu' );

define('adminDashboard', dirname( __FILE__ ));

/**
 * Display Custom Menu Pages
 */
function ProjectDashboard() {	
	require_once (adminDashboard.'/admin/addSites.php');
}

function importUserCallback() {	
	require_once (adminDashboard.'/admin/importUser.php');
}

//////////////////////////////////////////////////////////////////////////////////////	
   /* Add User Role */ 
add_role('employee',__( 'Employee' ),array('read' => true, 'edit_posts' => true));

//////////////////////////////////////////////////////////////////////////////////////


function AddProjectAssets() {
	wp_enqueue_style( 'DeveloperCss',  plugin_dir_url( __FILE__ ) . 'assets/styleDashboard.css' );		
}
add_action( 'admin_init', 'AddProjectAssets' );

/////////////////////////////////////////

add_action('wp_ajax_nopriv_addSideAction', 'addSideAction');
add_action('wp_ajax_addSideAction', 'addSideAction');

function addSideAction() {
$ldmv2_sites = get_option('ldmv2_sites',true);
$fs_sites = get_option('fs_sites',true);
array_push($ldmv2_sites, $_POST['siteName']);
array_push($fs_sites, $_POST['siteName']);
update_option('ldmv2_sites', $ldmv2_sites);
update_option('fs_sites', $fs_sites);
$responce = wp_send_json(array('responce' => 1, 'message' => 'Site Successfully Added'));
 wp_die();
}



if(isset($_POST['importUserSubmit'])){
    // Allowed mime types
    if ( ! function_exists( 'get_editable_roles' ) ) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }
    if(!function_exists('wp_get_current_user')) {
    include(ABSPATH . "wp-includes/pluggable.php"); 
    }
    if ( ! current_user_can( 'create_users' ) ){
			wp_die( __( 'You do not have sufficient permissions to access this page.' , 'import-users-from-csv') );
		}

   // require( ABSPATH . WPINC . '/user.php' );
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    
    // Validate whether selected file is a CSV file
    if(!empty($_FILES['InputImport']['name']) && in_array($_FILES['InputImport']['type'], $csvMimes)){
        
        // If the file is uploaded
        if(is_uploaded_file($_FILES['InputImport']['tmp_name'])){
            
            // Open uploaded CSV file with read-only mode
            $csvFile = fopen($_FILES['InputImport']['tmp_name'], 'r');

            
            // Skip the first line
            $validatehyphen = "yes";
           $firstrow = fgetcsv($csvFile);
           $colcount = count($firstrow);
           if(trim($firstrow[0]) == "User Name" &&  trim($firstrow[1]) == "Email" && trim($firstrow[2]) == "First Name" && trim($firstrow[3]) == "Last Name" && trim($firstrow[4]) == "Password" && trim($firstrow[5]) == "Sites" && trim($firstrow[6]) == "Group" && trim($firstrow[7]) == "Types" ){

				           
				            global $wpdb;

                             $pattern = '/[\'\/~`\!@#\$%\^&\*\(\)_\\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/';
                             $utype = array("senior-management","management-supervisory","technical-qa","operative","engineering","office-hr","food-safety-quality","sales-customer-service","logistics-distribution");
                             $sites_array = array();
							$sites = get_option( 'fs_sites' );

							foreach ( $sites as $site ) {
								$key = sanitize_key( $site );
								//$sites_array[$key] = $site;
								$sites_array[] = $key;
							}
							//echo "<pre>";
							//print_r($sites_array);
							//echo "</pre>";

                            //check each row if have any space
                            $mycnt = 1;                      
                           
                   
                                $csvFile = fopen($_FILES['InputImport']['tmp_name'], 'r');
					            // Parse data from CSV file line by line
								  // Skip the first line
								fgetcsv($csvFile);
					            while(($line = fgetcsv($csvFile)) !== FALSE){
					                // Get row data
					                 $username   = $line[0];
					                 $email = $line[1];
					                 $first_name = $line[2];
					                 $last_name = $line[3];
					                 $password = $line[4];
					                 $sites = $line[5];
					                 $group = $line[6];
					                 $type = $line[7];
					              //echo "<br>";
									
					                $sites = str_replace( array( '\'', '"', ',' , ';', '<', '>', '-', ' ', '.' ), '', $sites);
					                $sites = strtolower($sites);
					                //$groupname = "learndash_group_users_".$group;
					                

									$user_data = array(
									        'user_login' => $username,
									        'user_email' => $email,
									        'user_pass' => $password,
									        'user_nicename' => $first_name,
									        'display_name' => $first_name,
									        'role' => 'subscriber',
									    );

								    $user_id = wp_insert_user($user_data);
					                if (!is_wp_error($user_id)) { 
					                    wp_update_user([
										    'ID' => $user_id, // this is the ID of the user you want to update.
										    'first_name' => $first_name,
										    'last_name' => $last_name,
										]); 
								    	//update_user_meta($user_id, 'group', $group );
								    	update_user_meta($user_id, 'group', $group );
								    	update_user_meta($user_id, 'fs_site', $sites);
								    	update_user_meta($user_id, 'fs_type', $type);
										$sitesarray[] = str_replace("-"," ",$line[5]);
										update_user_meta($user_id, 'ldmv2_user_sites', $sitesarray);
								    } else {   str_replace("-"," ",$strring);

								    	$errors = $user_id;
								    	
								    }
							    }


			                    fclose($csvFile);
			                    echo "<div class='wrap'><div class='container'><div class='alert alert-success' role='alert'>Data Import Successfully</div></div></div>";

			                 

            } else {
            	echo "<div class='wrap'><div class='container'><div class='alert alert-danger' role='alert'>CSV Fields issue</div></div></div>";
            	//fclose($csvFile);
            	//exit();
            }
            
        }else{
          echo "<div class='wrap'><div class='container'><div class='alert alert-danger' role='alert'>CSV File Faild to upload, Please Try again.</div></div></div>";
        }
    } else {

    echo "<div class='wrap'><div class='container'><div class='alert alert-danger' role='alert'>Isseu find in your CSV File.</div></div></div>";

    }
    
}



