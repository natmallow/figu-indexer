<?php

namespace gnome\classes;

/**
 * PHP Pagination Class
 * Simple pagination class
 */
error_reporting(-1);
class Paginator
{
	public $current_page;
	public $items_per_page;
	public $limit_end;
	public $limit_start;
	public $num_pages;
	public $total_items;
	protected $ipp_array;
	protected $limit;
	protected $mid_range;
	protected $querystring;
	protected $return;
	protected $get_ipp;
	// added missing declarations
	protected $default_ipp;
	protected $start_range;
	protected $end_range;
	protected $range;



	public function __construct($total = 0, $mid_range = 7, $ipp_array = array(10, 25, 50, 100, "All"))
	{

		$this->total_items = (int)$total;

		// Midrange validation
		$this->mid_range = (int)$mid_range;
		if ($this->mid_range % 2 == 0 || $this->mid_range < 1) {
			exit("Invalid mid_range value (must be an odd integer >= 1)");
		}

		// ipp_array validation
		if (!is_array($ipp_array)) {
			exit("Invalid ipp_array value");
		}

		$this->ipp_array = $ipp_array;
		$this->default_ipp = $this->ipp_array[0];
		$this->items_per_page = (isset($_GET["ipp"])) ? $_GET["ipp"] : $this->default_ipp;

		// Compute num_pages based on items_per_page
		$this->num_pages = ($this->items_per_page == "All") ?
			1 : ceil($this->total_items / $this->items_per_page);

		$this->current_page = (isset($_GET["page"])) ? (int)$_GET["page"] : 1;

		// Build the querystring without the page or ipp parameters
		$this->buildQuerystring();

		// Construct the pagination display
		$this->paginate();
	}

	protected function buildQuerystring()
	{
		if ($_GET) {
			$args = explode("&", $_SERVER["QUERY_STRING"]);
			foreach ($args as $arg) {
				$keyval = explode("=", $arg);
				if ($keyval[0] != "page" && $keyval[0] != "ipp") {
					$this->querystring .= "&" . $arg;
				}
			}
		}

		if ($_POST) {
			foreach ($_POST as $key => $val) {
				if ($key != "page" && $key != "ipp") {
					$this->querystring .= "&$key=$val";
				}
			}
		}
	}

	protected function paginate()
	{
		if ($this->num_pages > 10) {
			$this->return = ($this->current_page > 1 && $this->total_items >= 10) 
				? "<li class=\"page-item\"><a class=\"page-link\" href=\"$_SERVER[PHP_SELF]?page=" . ($this->current_page - 1) . "&ipp=$this->items_per_page$this->querystring\">Previous</a></li>"
				: "<li class=\"page-item disabled\"><span class=\"page-link\">Previous</span></li>";
	
			$this->start_range = $this->current_page - floor($this->mid_range / 2);
			$this->end_range = $this->current_page + floor($this->mid_range / 2);
	
			if ($this->start_range <= 0) {
				$this->end_range += abs($this->start_range) + 1;
				$this->start_range = 1;
			}
	
			if ($this->end_range > $this->num_pages) {
				$this->start_range -= $this->end_range - $this->num_pages;
				$this->end_range = $this->num_pages;
			}
			
			$this->range = range($this->start_range, $this->end_range);
	
			for ($i = 1; $i <= $this->num_pages; $i++) {
				if ($this->range[0] > 2 && $i == $this->range[0]) {
					$this->return .= "<li class=\"page-item disabled\"><span class=\"page-link\">...</span></li>";
				}
	
				if ($i == 1 || $i == $this->num_pages || in_array($i, $this->range)) {
					$this->return .= ($i == $this->current_page && $this->items_per_page != "All") 
						? "<li class=\"page-item active\"><span class=\"page-link\">$i</span></li>"
						: "<li class=\"page-item\"><a class=\"page-link\" href=\"$_SERVER[PHP_SELF]?page=$i&ipp=$this->items_per_page$this->querystring\">$i</a></li>";
				}
	
				if ($this->range[$this->mid_range - 1] < $this->num_pages - 1 && $i == $this->range[$this->mid_range - 1]) {
					$this->return .= "<li class=\"page-item disabled\"><span class=\"page-link\">...</span></li>";
				}
			}
			
			$this->return .= (($this->current_page < $this->num_pages && $this->total_items >= 10) && ($this->items_per_page != "All") && $this->current_page > 0) 
				? "<li class=\"page-item\"><a class=\"page-link\" href=\"$_SERVER[PHP_SELF]?page=" . ($this->current_page + 1) . "&ipp=$this->items_per_page$this->querystring\">Next</a></li>"
				: "<li class=\"page-item disabled\"><span class=\"page-link\">Next</span></li>";
			
			$this->return .= ($this->items_per_page == "All") 
				? "<li class=\"page-item active\"><span class=\"page-link\">All</span></li>"
				: "<li class=\"page-item\"><a class=\"page-link\" href=\"$_SERVER[PHP_SELF]?page=1&ipp=All$this->querystring\">All</a></li>";
			
		} else {
			for ($i = 1; $i <= $this->num_pages; $i++) {
				$this->return .= ($i == $this->current_page) 
					? "<li class=\"page-item active\"><span class=\"page-link\">$i</span></li>"
					: "<li class=\"page-item\"><a class=\"page-link\" href=\"$_SERVER[PHP_SELF]?page=$i&ipp=$this->items_per_page$this->querystring\">$i</a></li>";
			}
			
			$this->return .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$_SERVER[PHP_SELF]?page=1&ipp=All$this->querystring\">All</a></li>";
		}
	
		$this->return = "<ul class=\"pagination\">" . str_replace("&", "&amp;", $this->return) . "</ul>";
		$this->limit_start = ($this->current_page <= 0) ? 0 : ($this->current_page - 1) * (int) $this->items_per_page;
	
		if ($this->current_page <= 0) {
			$this->items_per_page = 0;
		}
	
		$this->limit_end = ($this->items_per_page == "All") ? (int)$this->total_items : (int)$this->items_per_page;
	}
	

	// drop down
	protected function display_items_per_page()
	{
		$items = '';
		$ipp_array = array(10, 25, 50, 100, 'All');
		foreach ($ipp_array as $ipp_opt) {
			$items .= ($ipp_opt == $this->items_per_page) 
				? "<option selected value=\"$ipp_opt\">$ipp_opt</option>"
				: "<option value=\"$ipp_opt\">$ipp_opt</option>";
		}
	
		return '<span class="me-3">Items per page: </span><select class="form-select form-select-sm me-3 --wfc" onchange="window.location=\''. $_SERVER['PHP_SELF'] . '?page=1&ipp=\'+this[this.selectedIndex].value+\''. $this->querystring .'\'">' . $items . '</select>';
	}
	
	public function display_pagination() {
		return '<div class="d-flex align-items-center">' . $this->display_jump_menu() . $this->display_items_per_page() . $this->return . '</div>';
	}

	// drop down to page
	protected function display_jump_menu()
	{
		$option = '';
		for ($i = 1; $i <= $this->num_pages; $i++) {
			$option .= ($i == $this->current_page) 
				? "<option value=\"$i\" selected>$i</option>" 
				: "<option value=\"$i\">$i</option>";
		}
	
		return '<span class="me-3">Page: </span><select class="form-select form-select-sm me-3 --wfc" onchange="window.location=\''. $_SERVER['PHP_SELF'] . '?page=\'+this[this.selectedIndex].value+\'&ipp='. $this->items_per_page . $this->querystring .'\'">' . $option . '</select>';
	}
	

	public function display_pages()
	{
		return $this->return;
	}
}
