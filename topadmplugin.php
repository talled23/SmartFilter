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

    private $filters = array();

    // Define table columns
    function get_columns()
    {
        $columns = array(
                'cb'            => '<input type="checkbox" />',
                'ID'          => __('ID', 'topadm-cookie-consent'),
                'display_name'          => __('Display Name', 'topadm-cookie-consent'),
                'user_email'         => __('Email', 'topadm-cookie-consent'),
                'user_registered'   => __('User Registered', 'topadm-cookie-consent'),
                'pastcourses'   => __('Past Courses', 'topadm-cookie-consent'),
                'curcourses'   => __('Current Courses', 'topadm-cookie-consent'),
                'meta_value'   => __('Capabilities', 'topadm-cookie-consent')
        );
        return $columns;
    }
    
    // Adding action links to column
    function column_display_name($item)
    {
        $actions = array(
                'edit2'      => sprintf('<a href="?page=%s&action=%s&element=%s">' . __('Edit', 'plugintest') . '</a>', $_REQUEST['page'], 'edit2', $item['ID']),
        );
        
        return sprintf('%1$s %2$s', $item['display_name'], $this->row_actions($actions));
    }
    private function get_table_data( $search = '' ) {
        global $wpdb;

        $table = $wpdb->prefix . 'users';
        $table2 = $wpdb->prefix . 'usermeta';
        $not = "";
        if(isset($_POST['not'])){
            $not = "NOT";
        }

        if(isset($_POST['sub']) && isset($_POST['s'])){
            return $wpdb->get_results(
                "SELECT * FROM {$table} JOIN {$table2} ON ID=user_id 
                 WHERE meta_key='wp_capabilities' 
                 AND $not meta_value LIKE '%subscriber%'
                 AND display_name LIKE '%{$search}%'",
                ARRAY_A
            );
        }
        if(isset($_POST['sub'])){
            return $wpdb->get_results(
                "SELECT * FROM {$table} JOIN {$table2} ON ID=user_id 
                 WHERE meta_key='wp_capabilities' 
                 AND $not meta_value LIKE '%subscriber%'",
                ARRAY_A
            );
        }
        if (isset($_POST['s'])) {
            return $wpdb->get_results(
                "SELECT * FROM {$table} JOIN {$table2} ON ID=user_id 
                 WHERE meta_key='wp_capabilities' AND display_name LIKE '%{$search}%'",
                ARRAY_A
            );
        } else {
            return $wpdb->get_results(
                "SELECT * FROM {$table} JOIN {$table2} ON ID=user_id WHERE meta_key='wp_capabilities'",
                ARRAY_A
            );
        }
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
        if ( isset($_POST['s']) ) {
            $this->table_data = $this->get_table_data($_POST['s']);
        }
        else {
            $this->table_data = $this->get_table_data();
        }

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
                case 'display_name':
                case 'user_email':
                case 'user_registered':
                case 'pastcourses':
                case 'curcourses':
                case 'meta_value':
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

    $table = $wpdb->prefix . 'users';

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
    add_menu_page('TOPAdm List Table', 'TOPAdm List Table', 'activate_plugins', 'topadm_list_table', 'topadm_list_init');
}
add_action('admin_menu', 'my_add_menu_items');

// Plugin menu callback function
function topadm_list_init()
{
        // Creating an instance
        $table = new TOPAdm_List_Table();

        echo '<iframe name="votar" style="display:none;"></iframe>';

        echo '<div class="wrap"><h1>T.O.P. Smart Nutrition Administration Page</h2>';
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
                    <div class="dropdown btn-group">
                        <button class="btn btn-secondary dropdown-toggle money" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Payment Type
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item payment" href="#">Payment Type</a></li>
                            <li><a class="dropdown-item payment" href="#">Paid Full</a></li>
                            <li><a class="dropdown-item payment" href="#">Paid Coupon</a></li>
                            <li><a class="dropdown-item payment" href="#">Not Paid</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-auto mx-1">
                    <div class="dropdown btn-group">
                        <button class="btn btn-secondary dropdown-toggle courses" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            In Course
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item course" href="#">In Course</a></li>
                            <li><a class="dropdown-item course" href="#">Test Course 1</a></li>
                            <li><a class="dropdown-item course" href="#">Test Course 2</a></li>
                            <li><a class="dropdown-item course" href="#">Test Course 3</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-auto mx-1">
                    <button class="btn btn-outline-success" type="submit">Filter</button>
                </div>
            </div>
        ';
        echo '</form>';
        
    // Prepare table
    echo '<form method="POST">';
    $table->prepare_items();
    // Display table
    $table->display();

    echo '</div>';

    echo '<button type="submit" id="selectStudents" class="btn btn-secondary">Select Students</button>';

    echo '<h2>Group Actions:</h2>';

    echo '
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#emailModal">Email Students</button>

        <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="emailModalLabel">New email to '; 
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

    echo '
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#discountModal">Apply Discount</button>

        <div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="discountModalLabel">New Discount For '; 
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
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="subject" class="col-form-label">Discount of:</label>
                            <input type="number" class="mx-1" id="subject" min="1" max="100">
                            <label for="subject" class="col-form-label">Percent</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Apply discount</button>
                </div>
                </div>
            </div>
        </div>
    
    ';

    echo '
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addclassModal">Enroll to Class</button>

        <div class="modal fade" id="addclassModal" tabindex="-1" aria-labelledby="addclassModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addclassModalLabel">Enrolling '; 
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
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <h3>Enroll these students into:
                            <div class="dropdown btn-group">
                                <button class="btn btn-secondary dropdown-toggle courses" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Select Course
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item course" href="#">Select Course</a></li>
                                    <li><a class="dropdown-item course" href="#">Test Course 1</a></li>
                                    <li><a class="dropdown-item course" href="#">Test Course 2</a></li>
                                    <li><a class="dropdown-item course" href="#">Test Course 3</a></li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Enroll students</button>
                </div>
                </div>
            </div>
        </div>
    
    ';

    echo '
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#removeclassModal">Remove from Class</button>

        <div class="modal fade" id="removeclassModal" tabindex="-1" aria-labelledby="removeclassModalLabel" aria-hidden="true">
            <div class="modal-dialog">
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
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <h3>Remove these students from:
                            <div class="dropdown btn-group">
                                <button class="btn btn-secondary dropdown-toggle courses" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Select Course
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item course" href="#">Select Course</a></li>
                                    <li><a class="dropdown-item course" href="#">Test Course 1</a></li>
                                    <li><a class="dropdown-item course" href="#">Test Course 2</a></li>
                                    <li><a class="dropdown-item course" href="#">Test Course 3</a></li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Enroll students</button>
                </div>
                </div>
            </div>
        </div>
    
    ';


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
    </script>';

    echo '</form>';
}   
?>
</body>
</html>