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
                'display_name'          => __('DisplayName', 'topadm-cookie-consent'),
                'user_email'         => __('Email', 'topadm-cookie-consent'),
                'user_registered'   => __('UserRegistered', 'topadm-cookie-consent'),
                'payment'   => __('Payment', 'topadm-cookie-consent'),
                'courses'   => __('Courses', 'topadm-cookie-consent')
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
                case 'payment':
                case 'courses':
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

      echo '<div class="wrap"><h2>TOPAdm List Table</h2>';
        echo '
            <form class="row g-2" role="search" onsubmit="return false">
                <div class="col-auto">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </div>
            </form>
        ';
        echo '
            <h2>Filter:</h2>
            <form class="row g-3" onsubmit="return false">
                <div class="col form-check">
                    <input class="form-check-input" type="checkbox" value="" id="not">
                    <label class="form-check-label" for="not">
                        Not
                    </label>
                </div>
                <div class="col form-check">
                    <input class="form-check-input" type="checkbox" value="" id="sub">
                    <label class="form-check-label" for="sub">
                        Subscriber
                    </label>
                </div>
                <div class="col">
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
                <div class="col">
                    <div class="dropdown btn-group">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            In Course
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">In Course</a></li>
                            <li><a class="dropdown-item" href="#">Test Course 1</a></li>
                            <li><a class="dropdown-item" href="#">Test Course 2</a></li>
                            <li><a class="dropdown-item" href="#">Test Course 3</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col">
                    <button class="btn btn-outline-success" type="submit">Filter</button>
                </div>
            </form>
        ';
        
      // Prepare table
      $table->prepare_items();
      // Display table
      $table->display();
      echo '</div>';
      echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>';
      echo '<script>
        $(".dropdown-menu li a").click(function(){
            var selText = $(this).text();
            $(this).parents(".btn-group").find(".dropdown-toggle").html(selText);
        });
      </script>';
    // echo '<script>

    //     var links = document.getElementsByClassName("payment");
    //     for(var i = 0; i < links.length; i++){
    //         links[i].addEventListener("click",
    //         function(){
    //             console.log(links[i].text);
    //         })
    //     }

    // </script>';
      
}   
?>
    
</body>
</html>