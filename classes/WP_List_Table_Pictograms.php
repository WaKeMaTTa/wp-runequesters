<?php

/* -------------------------------------------- *
* Class Definition								*
* -------------------------------------------- */

// Our class extends the WP_List_Table class, so we need to make sure that it's there
if ( !class_exists('WP_List_Table') ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 * 
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 * 
 * Our theme for this list table is going to be movies.
 */
class WP_List_Table_Pictograms extends WP_List_Table {
	
	/** ************************************************************************
	 * REQUIRED. Set up a constructor that references the parent constructor. We 
	 * use the parent reference to set some default configs.
	 ***************************************************************************/
	function __construct(){
		global $status, $page;
				
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'pictogram',		//singular name of the listed records
			'plural'	=> 'pictograms',	//plural name of the listed records
			'ajax'		=> false			//does this table support ajax?
		) );
		
	}
	
	/** ************************************************************************
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title() 
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as 
	 * possible. 
	 * 
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 * 
	 * For more detailed insight into how columns are handled, take a look at 
	 * WP_List_Table::single_row_columns()
	 * 
	 * @param array $item A singular item (one full row's worth of data)
	 * @param array $column_name The name/slug of the column to be processed
	 * @return string Text or HTML to be placed inside the column <td>
	 **************************************************************************/
	function column_default($item, $column_name){
		switch($column_name){
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}
		
	/** ************************************************************************
	 * Recommended. This is a custom column method and is responsible for what
	 * is rendered in any column with a name/slug of 'title'. Every time the class
	 * needs to render a column, it first looks for a method named 
	 * column_{$column_title} - if it exists, that method is run. If it doesn't
	 * exist, column_default() is called instead.
	 * 
	 * This example also illustrates how to implement rollover actions. Actions
	 * should be an associative array formatted as 'slug'=>'link html' - and you
	 * will need to generate the URLs yourself. You could even ensure the links
	 * 
	 * 
	 * @see WP_List_Table::::single_row_columns()
	 * @param array $item A singular item (one full row's worth of data)
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************/
	function column_col_title($item) {
		$actions = array();
		$return = '';

		//Build row actions
		$actions["edit"] = sprintf('<a href="?page=%s&action=%s&%s=%s">%s</a>',
			$_REQUEST['page'],
			'edit',
			$this->_args['singular'],
			$item['icon_id'],
			__( 'Edit', WPRQ_TEXTDOMAIN )
		);

		$actions["delete"] = sprintf('<a href="?page=%s&action=%s&%s=%s">%s</a>',
			$_REQUEST['page'],
			'delete',
			$this->_args['singular'],
			$item['icon_id'],
			__( 'Delete', WPRQ_TEXTDOMAIN )
		);

		$return .= sprintf('<span style="color:silver">[ID %s]</span> ', $item['icon_id']);

		$return .= sprintf('%s %s', $item['icon_title'], $this->row_actions($actions));
		
		//Return the title contents
		return $return;
	}

	function column_col_slug($item){		
		//Return the description contents
		return $item["icon_slug"];
	}

	function column_col_image($item){		
		$return = '<img src="' . WPRQ_URL_UPLOADS_DIR . 'pictograms/' . $item["icon_image"] . '" />';
		return $return;
	}

	function column_col_author($item){
		//get name user
		$user = get_userdata( $item["icon_author"] );

		if ($user === false) {
			$return = '<span style="color: #a00;">';
			$return .= sprintf( __( 'Author doesn\'t exist (ID Author = %s)' , WPRQ_TEXTDOMAIN ),
				$item["icon_author"]
			);
			$return .= '</span>';
			return $return;
		}

		return $user->data->display_name;
	}

	function column_col_date($item){
		$return = date("d/m/Y H:i", strtotime($item["icon_date_modified"]));
		return $return;
	}

	/** ************************************************************************
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 * 
	 * @see WP_List_Table::::single_row_columns()
	 * @param array $item A singular item (one full row's worth of data)
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************/
	function column_cb($item){
		/*return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie")
			$item['map_id']	  // The value of the checkbox should be the record's id
		);*/
	}
	
	/** ************************************************************************
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value 
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 * 
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a column_cb() method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 * 
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	function get_columns(){
		$columns = array(
			//'cb'				=> '<input type="checkbox" />', //Render a checkbox instead of text
			'col_image'			=> __( 'Pictogram', WPRQ_TEXTDOMAIN ),
			'col_title'			=> __( 'Title', WPRQ_TEXTDOMAIN ),
			'col_slug'			=> __( 'Slug', WPRQ_TEXTDOMAIN ),
			'col_author'		=> __( 'Author', WPRQ_TEXTDOMAIN ),
			'col_date'			=> __( 'Date', WPRQ_TEXTDOMAIN ),
		);
		return $columns;
	}
	
	/** ************************************************************************
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
	 * you will need to register it here. This should return an array where the 
	 * key is the column that needs to be sortable, and the value is db column to 
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 * 
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 * 
	 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
	 **************************************************************************/
	function get_sortable_columns() {
		$sortable_columns = array(
			'col_date'	 => array('icon_date_modified', false),	 //true means it's already sorted
		);
		return $sortable_columns;
	}

	/** ************************************************************************
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 * 
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 * 
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 * 
	 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	/*
	function get_bulk_actions() {
		$actions = array(
			'delete'	=> 'Delete'
		);
		return $actions;
	}
	*/
	
	/** ************************************************************************
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here...
	 * 
	 * @global WPDB $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 **************************************************************************/
	function prepare_items() {
		global $wpdb, $_wp_column_headers; //This is used only if making any database queries

		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 30;

		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example 
		 * package slightly different than one you might build on your own. In 
		 * this example, we'll be using array manipulation to sort and paginate 
		 * our data. In a real-world implementation, you will probably want to 
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$query = "SELECT * FROM " . $wpdb->prefix . WPRQ_NAME . "_maps_pictograms";

		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 * 
		 * In a real-world situation involving a database, you would probably want 
		 * to handle sorting by passing the 'orderby' and 'order' values directly 
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
		$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
		if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
		
		
		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		
		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column 
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array($columns, $hidden, $sortable);
				
		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently 
		 * looking at. We'll need this later, so you should always include it in 
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();
		
		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array. 
		 * In real-world use, this would be the total number of items in your database, 
		 * without filtering. We'll need this later, so you should always include it 
		 * in your own package classes.
		 */
		$total_items = $wpdb->query($query);

		/**
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to 
		 */
		if(!empty($current_page) && !empty($per_page)){ 
			$offset=($current_page-1)*$per_page;
			$query.=' LIMIT '.(int)$offset.','.(int)$per_page;
		}
		
		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where 
		 * it can be used by the rest of the class.
		 */
		$this->items = $wpdb->get_results($query, ARRAY_A);

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,				  //WE have to calculate the total number of items
			'per_page'	=> $per_page,					 //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		) );
	}
	
}

?>