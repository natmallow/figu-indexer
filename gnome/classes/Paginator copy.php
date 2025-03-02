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

		$this->total_items = (int) $total;

		// if($this->total_items <= 0) exit("Unable to paginate: Invalid total value (must be an integer > 0)");

		$this->mid_range = (int) $mid_range; // midrange must be an odd int >= 1

		if ($this->mid_range % 2 == 0 or $this->mid_range < 1) exit("Unable to paginate: Invalid mid_range value (must be an odd integer >= 1)");

		if (!is_array($ipp_array)) exit("Unable to paginate: Invalid ipp_array value");

		$this->ipp_array = $ipp_array;
		$this->items_per_page = (isset($_GET["ipp"])) ? $_GET["ipp"] : $this->ipp_array[0];
		$this->default_ipp = $this->ipp_array[0];

		if ($this->items_per_page == "All") {
			$this->num_pages = 1;
		} else {
			if (!is_numeric($this->items_per_page) or $this->items_per_page <= 0) $this->items_per_page = $this->ipp_array[0];
			$this->num_pages = ceil($this->total_items / $this->items_per_page);
		}
		$this->current_page = (isset($_GET["page"])) ? (int) $_GET["page"] : 1; // must be numeric > 0

		if ($_GET) {
			$args = explode("&", $_SERVER["QUERY_STRING"]);
			foreach ($args as $arg) {
				$keyval = explode("=", $arg);
				if ($keyval[0] != "page" and $keyval[0] != "ipp") $this->querystring .= "&" . $arg;
			}
		}

		if ($_POST) {
			foreach ($_POST as $key => $val) {
				if ($key != "page" and $key != "ipp") $this->querystring .= "&$key=$val";
			}
		}

		if ($this->num_pages > 10) {
			// if current page is greater than one
			$this->return = ($this->current_page > 1 and $this->total_items >= 10) ? "<a class=\"btn btn-light\" href=\"$_SERVER[PHP_SELF]?page=" . ($this->current_page - 1) . "&ipp=$this->items_per_page$this->querystring\">Previous</a> " : "<span class=\"inactive\" href=\"#\">Previous</span> ";
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
				if ($this->range[0] > 2 and $i == $this->range[0]) $this->return .= " ... ";
				// loop through all pages. if first, last, or in range, display
				if ($i == 1 or $i == $this->num_pages or in_array($i, $this->range)) $this->return .= ($i == $this->current_page and $this->items_per_page != "All") ? "<a title=\"Go to page $i of $this->num_pages\" class=\"btn btn-light active\" href=\"#\">$i</a> \n" : "<a class=\"btn btn-light\" title=\"Go to page $i of $this->num_pages\" href=\"$_SERVER[PHP_SELF]?page=$i&ipp=$this->items_per_page$this->querystring\">$i</a> \n";
				if ($this->range[$this->mid_range - 1] < $this->num_pages - 1 and $i == $this->range[$this->mid_range - 1]) $this->return .= " ... ";
			}
			$this->return .= (($this->current_page < $this->num_pages and $this->total_items >= 10) and ($this->items_per_page != "All") and $this->current_page > 0) ? "<a class=\"btn btn-light\" href=\"$_SERVER[PHP_SELF]?page=" . ($this->current_page + 1) . "&ipp=$this->items_per_page$this->querystring\">Next</a>\n" : "<span class=\"inactive\" href=\"#\">Next</span>\n";
			$this->return .= ($this->items_per_page == "All") ? "<a class=\"btn btn-light active\" style=\"margin-left:10px\" href=\"#\">All</a> \n" : "<a class=\"btn btn-light\" style=\"margin-left:10px\" href=\"$_SERVER[PHP_SELF]?page=1&ipp=All$this->querystring\">All</a> \n";
		} else {
			for ($i = 1; $i <= $this->num_pages; $i++) {
				// active current
				$this->return .= ($i == $this->current_page) ?
					"<a class=\"btn btn-light active\" href=\"#\">$i</a> " :
					"<a class=\"btn btn-light\" href=\"$_SERVER[PHP_SELF]?page=$i&ipp=$this->items_per_page$this->querystring\">$i</a> ";
			}
			$this->return .= "<a class=\"btn btn-light\" href=\"$_SERVER[PHP_SELF]?page=1&ipp=All$this->querystring\">All</a> \n";
		}
		$this->return = str_replace("&", "&amp;", $this->return);
		$this->limit_start = ($this->current_page <= 0) ? 0 : ($this->current_page - 1) * (int) $this->items_per_page;
		if ($this->current_page <= 0) $this->items_per_page = 0;
		$this->limit_end = ($this->items_per_page == "All") ? (int) $this->total_items : (int) $this->items_per_page;
	}

	// drop down
	public function display_items_per_page()
	{
		$items = NULL;
		natsort($this->ipp_array); // This sorts the drop down menu options array in numeric order (with 'all' last after the default value is picked up from the first slot
		foreach ($this->ipp_array as $ipp_opt) {
			$items .= ($ipp_opt == $this->items_per_page) ? "<option selected value=\"$ipp_opt\">$ipp_opt</option>\n" : "<option value=\"$ipp_opt\">$ipp_opt</option>\n";
		}
		return "<span class=\"paginate\">Items per page:</span>
				<select class=\"form-select form-select-sm --wfc\" 
				onchange=\"window.location='$_SERVER[PHP_SELF]?page=1&amp;ipp='+this[this.selectedIndex].value+'$this->querystring';return false\">$items</select>\n";
	}

	// drop down to page
	public function display_jump_menu()
	{
		$option = NULL;
		for ($i = 1; $i <= $this->num_pages; $i++) {
			$option .= ($i == $this->current_page) ? "<option value=\"$i\" selected>$i</option>\n" : "<option value=\"$i\">$i</option>\n";
		}
		return "<span class=\"paginate\">Page:</span>
					<select class=\"form-select form-select-sm --wfc\" 
							onchange=\"window.location='$_SERVER[PHP_SELF]?
							page='+this[this.selectedIndex].value+'&amp;ipp=$this->items_per_page$this->querystring';return false\">$option</select>\n";
	}

	public function display_pages()
	{
		return $this->return;
	}
}
