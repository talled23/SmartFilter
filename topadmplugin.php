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
    private $table_data;

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
                'curcourses'   => __('Current Courses', 'topadm-cookie-consent')
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

    // Get table data
    private function get_table_data() {
        global $wpdb;

        $table = $wpdb->prefix . 'users';

        return $wpdb->get_results(
            "SELECT * from {$table}",
            ARRAY_A
        );
    }

    // Bind table with columns, data and all
    function prepare_items()
    {
        //data
        $this->table_data = $this->get_table_data();

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $primary  = 'display_name';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);
        
        $this->items = $this->table_data;
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
                default:
                    return $item[$column_name];
          }
    }
    
    function column_cb($item)
    {
        return sprintf(
                '<input type="checkbox" name="element[]" value="%s" />',
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

        

        echo '<div class="wrap"><h1>T.O.P. Smart Nutrition Administration Page</h2>';
        echo '
            <form class="row g-2" role="search" onsubmit="return false">
                <div class="col-auto">
                    <input class="form-control me-2" type="search" placeholder="Search by Name" aria-label="Search">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </div>
            </form>
        ';
        echo '
            <h2 class="mb-0">Filter:</h2>
            <form class="row g-3 align-items-center" onsubmit="return false">
                <div class="col-auto mx-1">
                    <input type="checkbox" value="" id="not">
                    <label for="not">
                        Not
                    </label>
                </div>
                <div class="col-auto mx-1">
                    <input type="checkbox" value="" id="sub">
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
            </form>
        ';
        
    // Prepare table
    $table->prepare_items();
    // Display table
    $table->display();
    echo '</div>';

    echo '<h2>Group Actions:</h2>';

    echo '
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#emailModal">Email Students</button>

        <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="emailModalLabel">New email to: Tal, Test McTest, Tal Test2</h1>
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
                    <h1 class="modal-title fs-5" id="discountModalLabel">New Discount For: Tal</h1>
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
                    <h1 class="modal-title fs-5" id="addclassModalLabel">Enrolling: Test McTest</h1>
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
                    <h1 class="modal-title fs-5" id="removeclassModalLabel">Removing: Test McTest</h1>
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
}   
?>
    
</body>
</html>