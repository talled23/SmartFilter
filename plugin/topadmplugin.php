<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>
  <body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    

<?php

/*
Plugin Name:  TOP Administration Plugin
Description: It is a test plug in
Author: Tal
Version: 1.0
*/

// Loading WP_List_Table class file
// We need to load it as it's not automatically loaded by WordPress

$dir = plugin_dir_path( __FILE__ );
require_once($dir.'emails.php');

if (!class_exists('WP_List_Table')) {
      require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

if(isset($_GET['action']) && isset($_GET['element'])){
    $action = $_GET['action'];
    $element = $_GET['element'];
    if($action == 'edit2'){
        doChanges($action, $element);
    }
}


// Extending class
class TOPAdm_List_Table extends WP_List_Table
{

    // static function edit_displayname()
    // {
    //     echo "<script>alert(20);</script>";

    //     $element = $_REQUEST['element'];

    //     echo "<script>alert($element)</script>";

    //     global $wpdb;

    //     $table = $wpdb->prefix . 'users';

    //     $wpdb->query(
    //         "UPDATE {$table} SET display_name = CONCAT(display_name, '_') WHERE ID={$element}",
    //         ARRAY_A
    //     );
    // }

    // public function __construct(){
    //     parent::__construct();
    //     add_action('edit2', array(get_called_class(), 'edit_displayname'));
    // }

    // define $table_data property
    public $table_data;

    public $filters = array();

    // Define table columns
    function get_columns()
    {
        $columns = array(
                'cb'            => '<input type="checkbox" />',
                'ID'          => __('ID', 'topadm-cookie-consent'),
                'display_name'          => __('Display Name', 'topadm-cookie-consent'),
                'user_email'         => __('Email', 'topadm-cookie-consent'),
                'user_registered'   => __('User Registered', 'topadm-cookie-consent'),
                'payment'   => __('Payment', 'topadm-cookie-consent'),
                'capabilities'   => __('Capabilities', 'topadm-cookie-consent'),
                'access2'   => __('Course Enrollment', 'topadm-cookie-consent')
        );
        return $columns;
    }
    
    // Adding action links to column
    function column_display_name($item)
    {
        // $actions = array(
        //         'edit2'      => sprintf('<a href="?page=%s&action=%s&element=%s">' . __('Edit', 'plugintest') . '</a>', $_REQUEST['page'], 'edit2', $item['ID']),
        // );
        
        //return sprintf('%1$s %2$s', $item['display_name'], $this->row_actions($actions));
        return sprintf('%1$s', $item['display_name']);
    }
    private function get_table_data() {
        global $wpdb; 

        

        $table = $wpdb->prefix . 'usersFake';
        $table2 = $wpdb->prefix . 'usermeta';
        $table3 = $wpdb->prefix . 'posts';
        $not = "";

        if(isset($_POST['filtersss'])){
            $this->filters = $_POST['filtersss'];
        }
        if(isset($_POST['not'])){
            $not = "NOT ";
        }

        if(isset($_POST['sub'])){
            array_push($this->filters, "" . $not . "SUBSCRIBER");
        }

        if(isset($_POST['s']) && $_POST['s'] != ''){
            $sString = $_POST['s'];
            array_push($this->filters, "" . $not . "NAME LIKE " . $sString);
        }

        if(isset($_POST['money']) && $_POST['money'] != ''){
            switch($_POST['money']){
                case 'pf':
                    $str = "FULL";
                    break;
                // case 'pc':
                //     $str = "COUPON";
                //     break;
                case 'np':
                    $str = "NOTHING";
                    break;
            }
            array_push($this->filters, "" . $not . "PAYMENT OF " . $str);
        }

        if(isset($_POST['s4c']) && $_POST['s4c'] != ''){
            $s4cString = $_POST['s4c'];
            array_push($this->filters, "" . $not . "COURSE LIKE " . $s4cString);
        }

        //SELECT ID, display_name, user_email, user_registered, pastcourses, curcourses, capabilities, access FROM wp_users JOIN (SELECT meta_value as capabilities FROM wp_usermeta WHERE meta_key='wp_capabilities') AS T1 ON ID=user_id JOIN (SELECT meta_value as access FROM wp_usermeta WHERE meta_key like 'course_%_access_From') AS T2 ON ID=user_id
        //SELECT ID, display_name, user_email, user_registered, pastcourses, curcourses, capabilities, access FROM wp_users JOIN (SELECT user_id, meta_key, meta_value as capabilities FROM wp_usermeta WHERE meta_key='wp_capabilities') AS T1 ON ID=user_id JOIN (SELECT user_id, meta_key, meta_value as access FROM wp_usermeta WHERE meta_key like 'course_%_access_From') AS T2 ON ID=user_id
        //SELECT ID, display_name, user_email, user_registered, pastcourses, curcourses, access FROM wp_users LEFT JOIN (SELECT user_id, meta_key as access FROM wp_usermeta WHERE meta_key like 'course_%_access_From') AS T2 ON ID=user_id
        //SELECT ID, display_name, user_email, user_registered, pastcourses, curcourses, access, capabilities FROM (SELECT * FROM wp_users LEFT JOIN (SELECT user_id as user_id2, meta_key as access FROM wp_usermeta WHERE meta_key like 'course_%_access_From') AS T2 ON ID=user_id2) AS T3 JOIN (SELECT user_id as user_id1, meta_key, meta_value as capabilities FROM wp_usermeta WHERE meta_key='wp_capabilities') AS T1 ON ID=user_id1
        //SELECT ID, display_name, user_email, user_registered, pastcourses, curcourses, access, capabilities FROM (SELECT * FROM wp_users LEFT JOIN (SELECT user_id as user_id2, SUBSTRING(meta_key, 8, (LENGTH(meta_key) - 19))  as access FROM wp_usermeta WHERE meta_key like 'course_%_access_From') AS T2 ON ID=user_id2) AS T3 JOIN (SELECT user_id as user_id1, meta_key, meta_value as capabilities FROM wp_usermeta WHERE meta_key='wp_capabilities') AS T1 ON ID=user_id1
        
        //SELECT ID, display_name, user_email, user_registered, pastcourses, curcourses, access2, capabilities FROM (SELECT * FROM wp_users LEFT JOIN (SELECT * FROM (SELECT user_id as user_id2, SUBSTRING(meta_key, 8, (LENGTH(meta_key) - 19)) as access FROM wp_usermeta WHERE meta_key like 'course_%_access_From') AS T1 JOIN (SELECT ID, post_title as access2 FROM wp_posts) AS T2 ON access=ID) AS T2 ON ID=user_id2) AS T3 JOIN (SELECT user_id as user_id1, meta_key, meta_value as capabilities FROM wp_usermeta WHERE meta_key='wp_capabilities') AS T1 ON ID=user_id1
        
        // $stringy =  "SELECT ID, display_name, user_email, user_registered, pastcourses, curcourses, meta_value
        // FROM {$table} JOIN {$table2} ON ID=user_id WHERE meta_key='wp_capabilities' "
        // . (isset($_POST['sub']) ? "AND " . $not . "meta_value LIKE '%subscriber%' " : "")
        // . ((isset($_POST['s']) && $_POST['s'] != '') ? "AND " . $not . "display_name LIKE '%{$_POST['s']}%' " : "")
        // . ((isset($_POST['s4c']) && $_POST['s4c'] != '') ? "AND " . $not . "curcourses LIKE '%{$_POST['s4c']}%' " : "")
        // . ((isset($_POST['money']) && $_POST['money'] != '') ? "AND " . $not . "curcourses LIKE '%{$_POST['money']}%' " : "");

        $sqlQuery = "SELECT * FROM (SELECT ID, display_name, user_email, user_registered, payment, GROUP_CONCAT(access1 ORDER BY access1 ASC SEPARATOR ', ') as access2, capabilities 
        FROM (SELECT * FROM `wp_usersFake`  LEFT JOIN 
        (SELECT * FROM 
        (SELECT user_id as user_id2, SUBSTRING(meta_key, 8, (LENGTH(meta_key) - 19)) as access FROM `wp_usermeta`  WHERE meta_key like 'course_%_access_From') AS T1 
        JOIN (SELECT ID as ID2, post_title as access1 FROM `wp_posts`) AS T2 ON access=ID2) AS T2 ON ID=user_id2) AS T3 
        JOIN (SELECT user_id as user_id1, meta_key, meta_value as capabilities FROM `wp_usermeta`  WHERE meta_key='wp_capabilities') AS T1 ON ID=user_id1 WHERE ID IS NOT NULL GROUP BY ID) AS TF
        WHERE ID IS NOT NULL ";
        

        $filterList = $this->filters;
        $n = count($filterList);

        for($i=0; $i < $n; $i++){
            $stringy = $filterList[$i];
            $wordList = explode(' ', $stringy);
            if($wordList[0] == 'NOT'){
                if($wordList[1] == 'SUBSCRIBER'){
                    $sqlQuery .= "AND NOT capabilities LIKE '%subscriber%' ";
                }
                elseif($wordList[1] == 'PAYMENT'){
                    if($wordList[3] == 'FULL'){
                        $sqlQuery .= "AND NOT payment LIKE 'Paid' ";
                    }
                    // if($wordList[3] == 'COUPON'){
                    //     $sqlQuery .= "AND NOT curcourses LIKE '%pc%' ";
                    // }
                    if($wordList[3] == 'NOTHING'){
                        $sqlQuery .= "AND NOT payment LIKE 'Not Paid' ";
                    }
                }
                elseif($wordList[1] == 'NAME'){
                    $s = "AND NOT display_name LIKE '%" . substr($stringy, 14) . "%' ";
                    $sqlQuery .= $s;
                }
                elseif($wordList[1] == 'COURSE'){
                    $s = "AND NOT access2 LIKE '%" . substr($stringy, 16) . "%' ";
                    $sqlQuery .= $s;
                }
            }
            else{
                if($wordList[0] == 'SUBSCRIBER'){
                    $sqlQuery .= "AND capabilities LIKE '%subscriber%' ";
                }
                elseif($wordList[0] == 'PAYMENT'){
                    if($wordList[2] == 'FULL'){
                        $sqlQuery .= "AND payment LIKE 'Paid' ";
                    }
                    // if($wordList[2] == 'COUPON'){
                    //     $sqlQuery .= "AND curcourses LIKE '%pc%' ";
                    // }
                    if($wordList[2] == 'NOTHING'){
                        $sqlQuery .= "AND payment LIKE 'Not Paid' ";
                    }
                }
                elseif($wordList[0] == 'NAME'){
                    $s = "AND display_name LIKE '%" . substr($stringy, 10) . "%' ";
                    $sqlQuery .= $s;
                }
                elseif($wordList[0] == 'COURSE'){
                    $s = "AND access2 LIKE '%" . substr($stringy, 12) . "%' ";
                    $sqlQuery .= $s;
                }
            }
        }

        // . (isset($_POST['sub']) ? "AND " . $not . "capabilities LIKE '%subscriber%' " : "")
        // . ((isset($_POST['s']) && $_POST['s'] != '') ? "AND " . $not . "display_name LIKE '%{$_POST['s']}%' " : "")
        // . ((isset($_POST['s4c']) && $_POST['s4c'] != '') ? "AND " . $not . "curcourses LIKE '%{$_POST['s4c']}%' " : "")
        // . ((isset($_POST['money']) && $_POST['money'] != '') ? "AND " . $not . "curcourses LIKE '%{$_POST['money']}%' " : "");

        $sqlQuery .= " GROUP BY ID ";

        //echo $sqlQuery;

        return $wpdb->get_results(
            $sqlQuery
            ,
            ARRAY_A
        );
    }

    protected function get_sortable_columns(){
        $sortable_columns = array(
            'display_name'  => array('display_name', false),
            'user_registered'  => array('user_registered', false)
        );
        return $sortable_columns;
    }

    // Sorting function
    function usort_reorder($a, $b){
        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'user_registered';

        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';

        // Determine sort order
        $result = strcmp(strtoupper($a[$orderby]), strtoupper($b[$orderby]));

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

    // Bind table with columns, data and all
    function prepare_items()
    {
        $this->table_data = $this->get_table_data();

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $primary  = 'display_name';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        usort($this->table_data, array(&$this, 'usort_reorder'));

        //array_push($this->names, $item['display_name']);
        /* pagination */
        $per_page = 5;
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        //$this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
                'total_items' => $total_items, // total number of items
                'per_page'    => $per_page, // items to show on a page
                'total_pages' => ceil( $total_items / $per_page ) // use ceil to round up
        ));
        
        $this->items = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);
    }

    function column_default($item, $column_name)
    {
          switch ($column_name) {
                case 'ID':
                    return $item[$column_name];
                case 'display_name':
                    return $item[$column_name];
                case 'user_email':
                    return $item[$column_name];
                case 'user_registered':
                    return $item[$column_name];
                case 'payment':
                    return $item[$column_name];
                case 'access2':
                    return $item[$column_name];
                case 'capabilities':
                    $crop1 = substr($item[$column_name], strpos($item[$column_name], '"')+1);
                    return ucfirst(substr($crop1, 0, -7));
                default:
                    return $item[$column_name];
          }
    }
    
    function column_cb($item)
    {
        return sprintf(
                '<input type="checkbox" name="element[]" value="%s"/>',
                $item['ID']
        );
    }

}

function doChanges($action, $element){
    global $wpdb;

    $table = $wpdb->prefix . 'usersFake';

    $sqlCode = "UPDATE {$table} 
        SET display_name = 
        case when RIGHT(display_name, 1) = '_'
            then SUBSTRING(display_name, 1, LENGTH(display_name)-1)
            else CONCAT(display_name, '_') 
        end 
        WHERE ID={$element}";

    $wpdb->query(
        $sqlCode,
        ARRAY_A
    );
}

// Adding menu
function my_add_menu_items()
{
    add_menu_page('SmartFilter', 'SmartFilter', 'activate_plugins', 'topadm_list_table', 'topadm_list_init');
}
add_action('admin_menu', 'my_add_menu_items');

// Plugin menu callback function
function topadm_list_init()
{

        global $wpdb; 

        $tablee = $wpdb->prefix . 'usersFake';
        $tablee2 = $wpdb->prefix . 'usermeta';
        $tablee3 = $wpdb->prefix . 'posts';


        if(isset($_POST['coursesA']) && $_POST['coursesA']!=' '){
            //echo '<script>alert(' . $_POST['coursesA'] . ') </script>';
            if(isset($_POST['selectedStudentsA'])){
                $studientes = $_POST['selectedStudentsA'];
                $course = $_POST['coursesA'];
                for($i = 0; $i < count($studientes); $i++){

                    $test = "SELECT 
                    CASE WHEN EXISTS(
                    SELECT * FROM {$tablee2} 
                    WHERE user_id =" . $studientes[$i] . " AND meta_key = 'course_" . $course . "_access_from'
                    ) then 1 else 0 end as found";

                    $found = $wpdb->get_results($test, ARRAY_A);

                    if(! $found[0]['found']){
                        $maxthingy = $wpdb->get_results(
                            "SELECT MAX(umeta_id) AS b FROM {$tablee2}"
                            ,
                            ARRAY_A
                        );
                        $maxthingy = $maxthingy[0]['b'];
                        $maxthingy++;

                        $stringyyy = "INSERT INTO wp_usermeta 
                        (umeta_id, user_id, meta_key, meta_value) 
                        VALUES (" . $maxthingy . ", " . $studientes[$i] . ", 'course_" . $course . "_access_from', 
                        UNIX_TIMESTAMP())";
        
                        $wpdb->query(
                            $stringyyy
                            ,
                            ARRAY_A
                        );
                    }
    
                    
                }
            }
        }

        if(isset($_POST['coursesR']) && $_POST['coursesR']!=' '){
            //echo '<script>alert(' . $_POST['coursesA'] . ') </script>';
            if(isset($_POST['selectedStudentsR'])){
                $studientes = $_POST['selectedStudentsR'];
                $course = $_POST['coursesR'];
                for($i = 0; $i < count($studientes); $i++){
    
                    $test = "SELECT 
                    CASE WHEN EXISTS(
                    SELECT * FROM {$tablee2} 
                    WHERE user_id =" . $studientes[$i] . " AND meta_key = 'course_" . $course . "_access_from'
                    ) then 1 else 0 end as found";

                    $found = $wpdb->get_results($test, ARRAY_A);

                    if($found[0]['found']){
                        $stringyyy = "DELETE FROM {$tablee2}
                        WHERE user_id = " . $studientes[$i] . " AND meta_key = 'course_" . $course . "_access_from'";

                        $wpdb->query(
                            $stringyyy
                            ,
                            ARRAY_A
                        );
                    }
                }
            }
        }

        // Creating an instance
        $table = new TOPAdm_List_Table();
        
        $table->prepare_items();

        //$sqlCode = "SELECT ID, display_name, user_email, user_registered, pastcourses, curcourses, meta_value
        //FROM {$tablee} JOIN {$tablee2} ON ID=user_id WHERE meta_key='wp_capabilities' ";

        $sqlCode = "SELECT * FROM {$tablee3} WHERE post_status='publish' and post_type='sfwd-courses'";
        
        $ret = $wpdb->get_results(
            $sqlCode,
            ARRAY_A
        );





        // for($i = 0; $i < count($ret); $i++){
        //     print_r($ret[$i]['post_title']);
        // }


        echo '<iframe name="votar" style="display:none;"></iframe>';

        echo '<div class="wrap"><h1 class="display-1" style="font-size:80px;"><i>SmartFilter</i></h1>';
        echo '<form method="POST">';
        echo '
            <div class="row g-2" role="search">
                <div class="col-auto">
                    <input class="form-control me-2" id="search_id-search-input" type="search" placeholder="Search by Name" aria-label="Search" name="s">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-success" id="search-submit" value="search" type="submit">Search</button>
                </div>
            </div>
        ';
        echo '
            <h2 class="mb-0">Filter:</h2>
            <div class="row g-3 align-items-center">
                <div class="col-auto mx-1">
                    <input type="checkbox" value="" id="not" name="not">
                    <label for="not">
                        Not
                    </label>
                </div>
                <div class="col-auto mx-1">
                    <input type="checkbox" value="" id="sub" name="sub">
                    <label for="sub">
                        Subscriber
                    </label>
                </div>
                <div class="col-auto mx-1">
                    <select name="money" id="money">
                        <option value="">Payment Type</option>
                        <option value="pf">Paid Full</option>';
                        //<option value="pc">Paid Coupon</option>
                        echo '<option value="np">Not Paid</option>
                    </select>
                </div>
                <div class="col-auto mx-1">
                    <div class="row g-2" role="search">
                        <div class="col-auto">
                            <input class="form-control me-2" id="search_id-search-input" type="search" placeholder="Search for Course" aria-label="Search" name="s4c">
                        </div>
                    </div>
                </div>
                <div class="col-auto mx-1">
                    <button class="btn btn-outline-success" type="submit">Filter</button>
                </div>
            </div>
        ';


    echo '
        <div>
            <h2 class="my=2">List of current filters applied (click to remove):</h2>
            <div class="list-group">';
                $filter_list = $table->filters;
            for($i = 0; $i < count($filter_list); $i++){
                echo'
                <label class="checkbox" for="flexCheckChecked'. $i .'">
                    <div class="form-group align-items-center list-group-item form-check my-1">
                        <input type="checkbox" name="filtersss[]" value="' . $filter_list[$i] . '" id="flexCheckChecked'. $i .'" checked>'; 
                        echo '<span>' . htmlspecialchars($filter_list[$i]) . '</span>
                    </div>
                </label>
                ';
            }
            echo'
            </div>
        </div>
    ';

    echo '</form>';

    
        
    // Prepare table
    echo '<form method="POST">';


    if(isset($_POST['element'])){
        $stu = $_POST['element'];
        $n = count($stu);
        for ($i=0; $i<$n; $i++){
            echo '<input hidden type="checkbox" name="finalSelections[]" value="' . $stu[$i] . '" checked>';
        }
    }

    if(isset($_POST['finalSelections'])){
        $stu = $_POST['finalSelections'];
        $n = count($stu);
        for ($i=0; $i<$n; $i++){
            echo '<input hidden type="checkbox" name="finalSelections[]" value="' . $stu[$i] . '" checked>';
        }
    }
    
    
    // Display table
    $table->display();

    echo '</div>';

    echo '<button type="submit" id="selectStudents" class="btn btn-secondary">Select Students</button>';

    echo '<h2>Group Actions:</h2>';

    echo '
        <button type="button" onclick="sendEmails()" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#emailModal">Email Students</button>

        <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">';


                echo'<h1 class="modal-title fs-5" id="emailModalLabel">New email to '; 
                        //echo implode(", ", $selectedStudents);
                        if(isset($_POST['element'])){
                            $elementss = $_POST['element'];
                            //echo var_dump($elementss);
                            $N = count($elementss);

                            echo("$N student(s): ");
                            for($i=0; $i < $N-1; $i++)
                            {
                            /*
                            echo '1:     ';
                            echo $elementss[$i];
                            echo '2:    ';
                            echo $elementss[$i]-1;

                            echo var_dump($table->table_data);
                            echo var_dump($table->table_data[$elementss[$i]-1]);
                            echo var_dump($table->table_data[$elementss[$i]-1]['display_name']);
                            
                            */
                            
                            echo($table->table_data[$elementss[$i]-1]['display_name']  . ", ");
                            }
                            echo($table->table_data[$elementss[$N - 1]-1]['display_name']);
                        }
                        else{
                            echo("nobody. You didn't select any students.");
                        }
                    echo '</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                    <div class="mb-3">
                        <label for="subject" class="col-form-label">Subject:</label>
                        <input type="text" class="form-control" id="subject">
                    </div>
                    <div class="mb-3">
                        <label for="body" class="col-form-label">Body:</label>
                        <textarea class="form-control" id="body"></textarea>
                    </div>
                    </form>
                    <p>List of emails: ';
                        if(isset($_POST['element'])){
                            $elementss = $_POST['element'];
                            $N = count($elementss);
                            for($i=0; $i < $N-1; $i++){                           
                                echo($table->table_data[$elementss[$i]-1]['user_email']  . ", ");
                            }
                            echo($table->table_data[$elementss[$N - 1]-1]['user_email']);
                        }
                        else{
                            echo("nobody. You didn't select any students.");
                        }
                    echo '</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Send email</button>
                </div>
                </div>
            </div>
        </div>
    
    ';

    // echo '
    //     <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#discountModal">Apply Discount</button>

    //     <div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true">
    //         <div class="modal-dialog modal-dialog-centered">
    //             <div class="modal-content">
    //             <div class="modal-header">
    //                 <h1 class="modal-title fs-5" id="discountModalLabel">New Discount For '; 
    //                 if(isset($_POST['element'])){
    //                     $elementss = $_POST['element'];
    //                     $N = count($elementss);

    //                     echo("$N student(s): ");
    //                     for($i=0; $i < $N-1; $i++){
    //                         echo($table->table_data[$elementss[$i]-1]['display_name']  . ", ");
    //                     }
    //                     echo($table->table_data[$elementss[$N - 1]-1]['display_name']);
    //                 }
    //                 else{
    //                     echo("nobody. You didn't select any students.");
    //                 }
    //             echo '</h1>
    //                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    //             </div>
    //             <div class="modal-body">
    //                 <form>
    //                     <div class="mb-3">
    //                         <label for="subject" class="col-form-label">Discount of:</label>
    //                         <input type="number" class="mx-1" id="subject" min="1" max="100">
    //                         <label for="subject" class="col-form-label">Percent</label>
    //                     </div>
    //                 </form>
    //             </div>
    //             <div class="modal-footer">
    //                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    //                 <button type="button" class="btn btn-primary">Apply discount</button>
    //             </div>
    //             </div>
    //         </div>
    //     </div>
    
    // ';

    // echo '
    //     <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addclassModal">Enroll to Class</button>

    //     <div class="modal fade" id="addclassModal" tabindex="-1" aria-labelledby="addclassModalLabel" aria-hidden="true">
    //         <div class="modal-dialog modal-dialog-centered">
    //             <div class="modal-content">
    //                 <div class="modal-header">
    //                     <h1 class="modal-title fs-5" id="addclassModalLabel">Enrolling '; 
    //                     if(isset($_POST['element'])){
    //                         $elementss = $_POST['element'];
    //                         $N = count($elementss);

    //                         echo("$N student(s): ");
    //                         for($i=0; $i < $N-1; $i++){
    //                             echo($table->table_data[$elementss[$i]-1]['display_name']  . ", ");
    //                         }
    //                         echo($table->table_data[$elementss[$N - 1]-1]['display_name']);
    //                     }
    //                     else{
    //                         echo("nobody. You didn't select any students.");
    //                     }
    //                 echo '</h1>
    //                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    //                 </div>
    //                 <form method="POST">
    //                     <div class="modal-body">
    //                         <div class="mb-3">
    //                             <h3>ENROLL these students into:                    
    //                             <select name="courses" id="courses">
    //                                 <option value="">Select Course</option>';
    //                                 for($i = 0; $i < count($ret); $i++){
    //                                     echo "<option value='" . $ret[$i]['ID'] ."'>" . $ret[$i]['post_title'] . "</option>";
    //                                 }
    //                             echo '                         
    //                             </select>
                                    
    //                         </div>
    //                     </div>
    //                     <div class="modal-footer">
    //                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    //                         <button type="button" class="btn btn-primary">Enroll students</button>
    //                     </div>
    //                 </form>
    //             </div>
    //         </div>
    //     </div>
    
    // ';

    echo '
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#enrollclassModal">Enroll to Class</button>

        <div class="modal fade" id="enrollclassModal" tabindex="-1" aria-labelledby="enrollclassModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="enrollclassModalLabel">Enrolling '; 
                        if(isset($_POST['element'])){
                            $elementss = $_POST['element'];
                            $N = count($elementss);

                            echo("$N student(s): ");
                            for($i=0; $i < $N-1; $i++){
                                echo($table->table_data[$elementss[$i]-1]['display_name']  . ", ");
                            }
                            echo($table->table_data[$elementss[$N - 1]-1]['display_name']);
                        }
                        else{
                            echo("nobody. You didn't select any students.");
                        }
                        echo '</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">';
                        if(isset($_POST['element'])){
                            $elementss = $_POST['element'];
                            $N = count($elementss);
                            for($i=0; $i < $N; $i++){
                                echo '<input hidden type="checkbox" name="selectedStudentsA[]" value="' . $elementss[$i] . '" checked>';
                            }
                        }
                        echo '
                        <div class="modal-body">
                            <div class="mb-3">
                                <h3>ENROLL these students into:
                                <select name="coursesA" id="coursesA">
                                    <option value=" ">Select Course</option>';
                                    for($i = 0; $i < count($ret); $i++){
                                        echo "<option value='" . $ret[$i]['ID'] ."'>" . $ret[$i]['post_title'] . "</option>";
                                    }
                        echo '                         
                                </select>
                            </div>
                        </div>
                    
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Enroll students</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
    ';

    echo '
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#removeclassModal">Remove from Class</button>

        <div class="modal fade" id="removeclassModal" tabindex="-1" aria-labelledby="removeclassModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="removeclassModalLabel">Removing '; 
                        if(isset($_POST['element'])){
                            $elementss = $_POST['element'];
                            $N = count($elementss);

                            echo("$N student(s): ");
                            for($i=0; $i < $N-1; $i++){
                                echo($table->table_data[$elementss[$i]-1]['display_name']  . ", ");
                            }
                            echo($table->table_data[$elementss[$N - 1]-1]['display_name']);
                        }
                        else{
                            echo("nobody. You didn't select any students.");
                        }
                        echo '</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">';
                        if(isset($_POST['element'])){
                            $elementss = $_POST['element'];
                            $N = count($elementss);
                            for($i=0; $i < $N; $i++){
                                echo '<input hidden type="checkbox" name="selectedStudentsR[]" value="' . $elementss[$i] . '" checked>';
                            }
                        }
                        echo '
                        <div class="modal-body">
                            <div class="mb-3">
                                <h3>REMOVE these students from:
                                <select name="coursesR" id="coursesR">
                                    <option value=" ">Select Course</option>';
                                    for($i = 0; $i < count($ret); $i++){
                                        echo "<option value='" . $ret[$i]['ID'] ."'>" . $ret[$i]['post_title'] . "</option>";
                                    }
                        echo '                         
                                </select>
                            </div>
                        </div>
                    
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Remove students</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
    ';

    

    // if(isset($_POST['coursesR']) && $_POST['coursesR']!=' '){
    //     //echo '<script>alert(' . $_POST['coursesR'] . ') </script>';
    //     if(isset($_POST['selectedStudentsR'])){
    //         echo '<script>alert("REMOVED STUDENTS: ';
    //             $studientes = $_POST['selectedStudentsR'];
    //             for($i = 0; $i < count($studientes); $i++){
    //                 echo $studientes[$i] . " ";
    //             }
    //             echo ' into class ' . $_POST['coursesR'];
    //         echo '")</script>';
    //     }
    // }

    // if($_POST['courses'] != ''){
    //     echo '<script>alert("selected course lmao");</script>';
    // }


    echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>';
    echo '<script>
        $(".dropdown-menu li a").click(function(){
            var selText = $(this).text();
            $(this).parents(".btn-group").find(".dropdown-toggle").html(selText);
        });
    </script>';
    echo '<script>
        var column = document.getElementsByClassName("column-ID");
        for(var i = 0; i < column.length; i++){
            column[i].style.width="5em";
        }
        var emails = document.getElementsByClassName("column-user_email");
        for(var i = 0; i < emails.length; i++){
            emails[i].style.width="15em";
        }
        var names = document.getElementsByClassName("column-display_name");
        for(var i = 0; i < names.length; i++){
            names[i].style.width="10em";
        }
    </script>';

    echo '</form>';

    //echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">';
    
    //echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>';
    
    echo '
    
    <script>

        function sendEmails() {
            console.log("test");
            var selected = "hey";
            var event_id = 3;
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "./admin.php",
                data: "action=signup&selected=" + selected + "&event_id=" + event_id,
                complete: function(data) {
                    console.log("it worked!");
                    console.log(data);
                }
            });
        }
    </script>
    ';
    
    //var json = JSON.parse(JSON.stringify(data));

}   
?>

<?php
    // $selected = filter_input(INPUT_POST, "selected");
    // $event_id = filter_input(INPUT_POST, "event_id");
    // $data = "hey";
    // echo $data;
?>


</body>
</html>