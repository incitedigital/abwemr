<?php
include("FusionCharts.php");

$NoRecordset = NULL;

function renderFusionChart($instance, $width, $height) {
	$instance->render($width, $height);
}

class DWFChart {

	// Chart name and type
	var $chartName;
	var $chartType;
	
	var $version;
	
	// The category that the chart type belongs to (singleSeries, multipleSeries)
	var $chartCategory;
	
	// Chart dimensions
	var $chartWidth;
	var $chartHeight;
	
	// Chart captions and titles
	var $caption;
	var $subCaption;
	var $xAxisTitle;
	var $yAxisTitle;
	var $pyAxisTitle;
	var $syAxisTitle;
	
	// String containing the XML configuration of the chart
	var $xmlStr;
	
	// Object containing the properties of the data source that contains the category data
	var $categories;
	
	// Array of objects containing the properties of the series data sources
	var $series;
	
	// Array of strings containing the XML attributes of each series (color, etc)
	var $seriesProps;
	
	// Array containing the category data
	var $categ_data;
	
	var $has_static_categ;
	
	var $static_categ_str;
	
	// Array of associative arrays containing the series data and the correspondances with the categories
	var $series_data;
	
	var $extra_data;
	
	// Array of associative arrays containing the display strings for some types of data
	var $display;
	
	// Array of arrays containing the indexes of the series in a group (e.g "1,2","3,4,5") - only userd for some combination charts
	var $groups;
	
	// Array containing the 
	var $categ_arr;
	
	// The XML string that describes the category data (this will be added to the congiguration XML prior to rendering)
	var $categoryXML;
	
	// Array of XML strings that describe the series data (this will be added to the congiguration XML prior to rendering)
	var $seriesXML;
	
	// Array of XML strings that describe the line series data (this will be added to the congiguration XML prior to rendering)
	var $lineSetXML;
	
	// String that specifies which set of data will establish the order (none, the category, one of the series)
	var $arrangeType;
	
	// String that specifies the ordering direction (asc/desc)
	var $arrangeOrder;

	// The relative path to the chart swf files
	var $relativePath;
	
	// The type of plot (date based/interval based/category based or none (direct data))
	var $plotType;
	
	var $useExport;
	
//	---------------------------------- PUBLIC METHODS -------------------------------------------------------------


	/**
	* Class constructor. Sets some properties and initializes some arrays
	*
	*
	* @returns - nothing
	*/
	
	function DWFChart($name, $type, $relativePath, $width, $height, $caption, $subcaption, $xtitle, $ytitle, $pytitle, $sytitle) {
		$this->chartName = $name;
		$this->chartType = $type;
		$this->relativePath = $relativePath;
		$this->chartWidth = $width;
		$this->chartHeight = $height;
		$this->caption = $caption;
		$this->subCaption = $subcaption;
		$this->xAxisTitle = $xtitle;
		$this->yAxisTitle = $ytitle; 
		$this->pyAxisTitle = $pytitle; 
		$this->syAxisTitle = $sytitle; 
		
		$this->categories = array();
		$this->series = array();
		$this->seriesProps = array();
		
		$this->categ_data = array();
		$this->series_data = array();
		$this->extra_data = array();
		
		$this->groups = array();
		
		$this->arrangeType = "none";
		$this->arrangeOrder = "asc";
		
		$this->plotType = "directValues";

		if (strstr(strtolower($type), 'combi') != false || strstr(strtolower($type), 'ms') != false || strstr(strtolower($type), 'stacked') != false || strstr(strtolower($type), 'scroll') != false) {
			$this->chartCategory = "multipleSeries";
		} else {
			$this->chartCategory = "singleSeries";
		}
		
		$this->has_static_categ = false;
		$this->useExport = false;
		
		$this->version = "1.0.0";
	}
	
	function setVersion($ver) {
		$this->version = $ver;
	}

	/**
	* Sets the configuration XML and adds dynamic elements (titles, captions, etc)
	*
	* @str - String containing the configuration XML for the chart
	*
	* @returns - nothing
	*/	
	function setConfigXML($str) {
		$this->xmlStr = $str;
		$this->xmlStr = str_replace("\"", "'", $this->xmlStr);
		$this->xmlStr = str_replace("\r\n", "", $this->xmlStr);
		
		$this->xmlStr = str_replace("{FCDW_CAPTION}", $this->caption, $this->xmlStr);
		$this->xmlStr = str_replace("{FCDW_SUBCAPTION}", $this->subCaption, $this->xmlStr);
		$this->xmlStr = str_replace("{FCDW_XAXISTITLE}", $this->xAxisTitle, $this->xmlStr);
		$this->xmlStr = str_replace("{FCDW_YAXISTITLE}", $this->yAxisTitle, $this->xmlStr);
		$this->xmlStr = str_replace("{FCDW_PYAXISTITLE}", $this->pyAxisTitle, $this->xmlStr);
		$this->xmlStr = str_replace("{FCDW_SYAXISTITLE}", $this->syAxisTitle, $this->xmlStr);
	}
	
	/**
	* Sets the properties that define the category data
	*
	* @rs - the source recordset
	* @row_rs - the row of the source recordset
	* @val_col - the value column in the recordset
	* @label_col - the label column in the recordset
	* @min_val - the minimum value for the category
	* @max_val - the maximum value for the category
	*
	* @returns - nothing
	*/
	function setCategory($rs, $label_col, $config_str, $type = "directValues") {
		$this->plotType = $type;
		if ($config_str != NULL && $config_str != '') {
			$configArr = explode(';', $config_str);
			for ($i=0;$i<count($configArr);$i++) {
				$str = $configArr[$i];
				$arr = explode("=", $str);
				$key = $arr[0];
				$value = $arr[1];
				$this->categories[$key] = $value;
				
				if ($key == "useExport" && $value == "true") {
					$this->useExport = true;
				}
			}
		}
		if (isset($rs) && $rs != NULL) {
			$this->categories['rs'] = $rs;
			if (mysql_num_rows($rs) > 0) mysql_data_seek($rs, 0);
			$this->categories['row'] = mysql_fetch_assoc($rs);
			$this->categories['label_col'] = $label_col;
		} else {
			$this->categories['rs'] = NULL;
		}
	}
	
	function setStaticCategory($val_str) {
		$this->static_categ_data = $val_str;
		$this->has_static_categ = true;
	}
	
	/**
	* Sets the properties that define a series
	*
	* @rs - the source recordset
	* @row_rs - the row of the source recordset
	* @val_col - the value column in the recordset
	* @cat_col - the column that holds the corresponding category
	* @props_str - string containing the XML properties of the series (color, etc.)
	* @group - the group that the series belongs to
	*
	* @returns - nothing
	*/
	function addSeries($rs, $val_col, $config_str, $props_str, $group, $skip) {
		$curr_series = array();
		
		// Add the properties from props_str to the series object
		if ($config_str != NULL && $config_str != '') {
			$configArr = explode(';', $config_str);
			for ($i=0;$i<count($configArr);$i++) {
				$str = $configArr[$i];
				$arr = explode("=", $str);
				$key = $arr[0];
				$value = $arr[1];
				$curr_series[$key] = $value;
			}
		}
		
        if (!isset($curr_series['linkColumn'])) $curr_series['linkColumn'] = '';
        if (!isset($curr_series['colorColumn'])) $curr_series['colorColumn'] = '';
		if (!isset($curr_series['targetColumn'])) $curr_series['targetColumn'] = '';
		
		// Add the recordset to the series object and reset it
		$curr_series['rs'] = $rs;
		if (mysql_num_rows($rs) > 0) mysql_data_seek($rs, 0);
		$curr_series['row'] = mysql_fetch_assoc($rs);
		$curr_series['value_col'] = $val_col;
		//echo $curr_series['link_col']."__".count($this->series);

		$this->series[count($this->series)] = $curr_series;
		$this->seriesProps[count($this->seriesProps)] = $props_str;	
		
		// Add the series to the group
		if (isset($this->groups[$group])) {
			array_push($this->groups[$group], count($this->series) - 1);
		} else {
			$this->groups[$group] = array();
			array_push($this->groups[$group], count($this->series) - 1);
		}
	}

	/**
	* Sets the ordering type for the chart (none/by category/by one of the series)
	*
	* @returns - nothing
	*/	
	function setOrdering($type, $order = "asc") {
		$this->arrangeType = strtolower($type);
		$this->arrangeOrder = strtolower($order);
	}
	
	/**
	* Prepares the data to be plotted (adds zero values where needed, arranges the data, creates the datasets, etc
	*
	* @returns - nothing
	*/
	function prepareData() {
		// prepare category data
		if (!$this->has_static_categ) {
			if ($this->plotType == "interval") {
				$this->addCategoryDataFromInterval();
			} else {
				if ($this->categories['rs'] != NULL) {
					$this->addCategoryDataFromRS();
				} else {
					$this->addCategoryDataFromSeries();
				}
			}
		} else {
			$this->addStaticCategoryData();

		}
		
		// prepare series data
		for ($i=0;$i<count($this->series);$i++) {
			if ($this->chartType != "Scatter" && $this->chartType != "Bubble") {
			$this->addSeriesData($i);
			} else {
				$this->addSeriesDataForXY($i);
			}
		}
		
		// arrange the data
		$this->arrangeData($this->arrangeType);
		
		// create the xml sets
		if ($this->chartCategory == "multipleSeries" || $this->chartType == "Scatter" || $this->chartType == "Bubble") {
			$this->setCategoryXML();
			for ($i=0;$i<count($this->series);$i++) {
				if ($this->chartType != "Scatter" && $this->chartType != "Bubble") {
					$this->setSeriesXML($i);
				} else {
					$this->setSeriesXMLForXY($i);
				}
			}
		}
		
		// put the data in the configuration XML
		if ($this->chartCategory == "multipleSeries" || $this->chartType == "Scatter" || $this->chartType == "Bubble") {
			$data = (!$this->needGroups()) ? $this->getMultiSeriesDataXML() : $this->getGroupedDataXML();
		} else {
			$data = $this->getSingleSeriesDataXML();
		}

		$this->xmlStr = str_replace("<dynamicData>{FCDW_DATA}</dynamicData>", $data, $this->xmlStr);
	}
	
	/**
	* Renders the chart
	*
	* @returns - nothing
	*/
	function render() {
		echo '<!-- FusionCharts for Dreamweaver version '. $this->version .' -->';
		echo renderChart($this->relativePath.$this->chartType.".swf", "", preg_replace("/\n/e", "", preg_replace("/\r/e", "", $this->xmlStr)), $this->chartName, $this->chartWidth, $this->chartHeight, false, true);
		if ($this->useExport) {
			echo '<div id="fc_exp_div_'.$this->chartName.'" align="center">FusionCharts Export Handler Component</div>
	<script type="text/javascript">
					var exportComponent'.$this->chartName.' = new FusionChartsExportObject("fc_exp_'.$this->chartName.'", "'.$this->relativePath.'FCExporter.swf");
					exportComponent'.$this->chartName.'.Render("fc_exp_div_'.$this->chartName.'");
	</script>';
		}
	}
	
	

// 	-------------------------------------- PRIVATE METHODS -------------------------------------------------------
	
	
	function addStaticCategoryData() {
		$values = explode(';', $this->static_categ_data);
		for ($i=0;$i<count($values);$i++) {
			$this->categ_data[$values[$i]] = $values[$i];
		}
	}
	
	
	/**
	* Gets the category data form the recordset and places it into an associative array
	* (used for direct values plot type)
	*
	* @returns - nothing
	*/
	function addCategoryDataFromRS() {
		$category = $this->categories;
		$index = 0;

		do {
			if (isset($category['value_col'])) {
				// if we have a value column defined we use that for the key values in  the associative array
				$key = $category['row'][$this->getCleanColumnName($category['value_col'])];
								
				$this->categ_data[$key] = $category['row'][$this->getCleanColumnName($category['label_col'])];
			} else {
				// if we don't have a value column defined we use an index as the category array key
				$key = $index;		
				
				$this->categ_data[$key] = $category['row'][$this->getCleanColumnName($category['label_col'])];	
			} 
			
			$index++;
		} while ($category['row'] = mysql_fetch_assoc($category['rs']));
		if (mysql_num_rows($category['rs']) > 0) mysql_data_seek($category['rs'], 0);
	}
	
	/**
	* Gets the category data form the series recordsets and places it into an associative array
	* (used for date, interval and category plot types)
	*
	* @returns - nothing
	*/	
	function addCategoryDataFromSeries() {
		$cat = array();
		
		for ($i=0;$i<count($this->series);$i++) {
			$row = $this->series[$i]['row'];
			$rs = $this->series[$i]['rs'];
			
			$cat_id_col = $this->getCategoryIDColumn($i);
			$cat_label_col = $this->getCategoryDisplayColumn($i);
			
			do {
				if (!array_search($row[$cat_id_col], $this->categ_data)) {
					$this->categ_data[$row[$cat_id_col]] = $row[$cat_label_col]; //$this->getCategoryLabel($row[$cat_label_col], $i); 
				}
			} while ($row = mysql_fetch_assoc($rs));
			if (mysql_num_rows($rs) > 0) mysql_data_seek($rs, 0);
		}
	}
	
	/**
	* Gets the category data form an interval of values with a given step
	*
	* @returns - nothing
	*/	
	function addCategoryDataFromInterval() {
		$min = $this->series[0]['interval_min'];
		$max = $this->series[0]['interval_max'];
		$step = $this->series[0]['interval_step'];
			
		$m = 0;
		
		do {
			$this->categ_data[$m] = $m; //$this->getCategoryLabel($m, 0);
			$m++;
		} while ($min + $m * $step < $max);
	}
	
	/**
	* Gets the category display column from a series. This is used when getting the category data from a series
	*
	* @returns - string - the name of the display column
	*/	
	function getCategoryDisplayColumn($i) {
		switch ($this->plotType) {
			case "interval":
				return "xtd_interval";
			break;
			case "date":
				return "xtd_date";
			break;
			case "category":
//                $ret = str_replace('`', '', $this->series[$i]["category_label_col"]);
//                $arr = explode('\.', $ret);
//                $ret = $arr[1];
				return $this->getCleanColumnName($this->series[$i]["category_label_col"]);
			break;
			default:
				return NULL;
			break;
		}
	}
	
	function getCleanColumnName($str) {
		$ret = str_replace('`', '', $str);
		$arr = explode('\.', $ret);
        if (isset($arr[1])) {
            return $arr[1];
        } else {
            return $ret;
        }
	}
	
	/**
	* Gets the category display column from a series. This is used when getting the category data from a series
	*
	* @returns - string - the name of the display column
	*/	
	function getCategoryIDColumn($i) {
		switch ($this->plotType) {
			case "interval":
				return "xtd_interval";
			break;
			case "date":
				return "xtd_date";
			break;
			case "category":
//                $ret = str_replace('`', '', $this->series[$i]["category_id_col"]);
//                $arr = explode('\.', $ret);
//                $ret = $arr[1];
				return $this->getCleanColumnName($this->series[$i]["category_label_col"]);
			break;
			default:
				return NULL;
			break;
		}	
	}
	
	/**
	* Gets the category label based on the corresponding values and the series index. 
	* This is used when getting the category data from a series
	*
	* @returns - string - the value that will be displayed in the categories
	*/	
	function getCategoryLabel($val, $series_index) {
		$ret = "";
		switch ($this->plotType) {
			case "date":
				$ret = $this->getDateDisplayValue($val, $this->series[$series_index]['series_date_display']);
			break;		
			case "interval":
				$min = $this->series[$series_index]['interval_min'];
				$max = $this->series[$series_index]['interval_max'];
				$step = $this->series[$series_index]['interval_step'];
				$start = $min + $val * $step;
				$end = (($start + $step) > $max) ? $max : $start + $step;
				$ret = $start . ' - ' . $end;
			break;
			case "category":
				$ret = $val;
			break;
			default:
				$ret = $val;
			break;
		}
		
		return $ret;
	}
	
	/**
	* Gets the category label based on the corresponding values and the series index. 
	* This is used when getting the category data from a series
	*
	* @returns - string - the value that will be displayed in the categories
	*/	
	function getDateDisplayValue($val, $format) {
		$mo_sh = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		$mo_lo = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		
		switch ($format) {
			// Hour formats
			case "hour_sh":
				$arr = explode(' ', $val);
				return (isset($arr[3])) ? $arr[3] : $val;
			break;
			case "hour_lo":
				$arr = explode(' ', $val);
				return (isset($arr[3])) ? $arr[3] . ":00" : $val . ":00";
			break;
			case "hour_ampm":
				$arr = explode(' ', $val);
				$hour = (isset($arr[3])) ? $arr[3] : $val;
				$suffix = ($hour >= 12) ? "AM" : "PM";
				$no = ($hour >= 12) ? $hour - 12 : $hour;
				return $no . " " . $suffix;
			break;	
		
			// Day formats
			case "day_no":
				$arr = explode(' ', $val);
				return (isset($arr[2])) ? $arr[2] : $val;	
			break;
			case "day_sl_month_no":
				$arr = explode(' ', $val);
				return (isset($arr[2]) && isset($arr[1])) ? $arr[2].'/'.$arr[1] : $val;
			break;
			case "day_sl_month_no_year":
				$arr = explode(' ', $val);
				return (isset($arr[2]) && isset($arr[1])) ? $arr[2].'/'.$arr[1].'/'.$arr[0] : $val;
			break;
			case "day_no_month_sh":
				$arr = explode(' ', $val);
				return (isset($arr[2]) && isset($arr[1])) ? $arr[2].' '.$mo_sh[(int)$arr[1] - 1] : $val;			
			break;
			case "day_no_month_sh_year":
				$arr = explode(' ', $val);
				return (isset($arr[2]) && isset($arr[1])) ? $arr[2].' '.$mo_sh[(int)$arr[1] - 1].' '.$arr[0] : $val;			
			break;
			case "day_no_month_lo":
				$arr = explode(' ', $val);
				return (isset($arr[2]) && isset($arr[1])) ? $arr[2].' '.$mo_lo[(int)$arr[1] - 1] : $val;						
			break;
			case "month_sh_day_no":
				$arr = explode(' ', $val);
				return (isset($arr[2]) && isset($arr[1])) ? $mo_sh[(int)$arr[1] - 1].' '.$arr[2] : $val;			
			break;
			case "month_no_day_sl":
				$arr = explode(' ', $val);
				return (isset($arr[2]) && isset($arr[1])) ? $arr[1].'/'.$arr[2] : $val;
			break;
			case "month_no_sl_day_no_sl_year":
				$arr = explode(' ', $val);
				return (isset($arr[2]) && isset($arr[1])) ? $arr[1].'/'.$arr[2].'/'.$arr[0] : $val;
			break;
			
			
			
			// Month formats
			case "month_no":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $arr[1] : $val;
			break;
			case "month_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $mo_sh[$arr[1] - 1] : $mo_sh[$val - 1];
			break;
			case "month_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $mo_lo[$arr[1] - 1] : $mo_lo[$val - 1];
			break;
			
			case "month_no_year_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $arr[1]. ' ' . substr($arr[0], 2, 2) : $val;
			break;
			case "month_sh_year_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $mo_sh[$arr[1] - 1]. ' ' . substr($arr[0], 2, 2) : $mo_sh[$val - 1];
			break;
			case "month_lo_year_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $mo_lo[$arr[1] - 1]. ' ' . substr($arr[0], 2, 2) : $mo_lo[$val - 1];
			break;

			case "month_no_sl_year_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $arr[1]. '/' . substr($arr[0], 2, 2) : $val;
			break;
			case "month_sh_sl_year_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $mo_sh[$arr[1] - 1]. '/' . substr($arr[0], 2, 2) : $mo_sh[$val - 1];
			break;
			case "month_lo_sl_year_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $mo_lo[$arr[1] - 1]. '/' . substr($arr[0], 2, 2) : $mo_lo[$val - 1];
			break;

			case "month_no_year_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $arr[1]. ' ' . $arr[0] : $val;
			break;
			case "month_sh_year_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $mo_sh[$arr[1] - 1]. ' ' . $arr[0] : $mo_sh[$val - 1];
			break;
			case "month_lo_year_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $mo_lo[$arr[1] - 1]. ' ' . $arr[0] : $mo_lo[$val - 1];
			break;

			case "month_no_sl_year_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $arr[1]. '/' . $arr[0] : $val;
			break;
			case "month_sh_sl_year_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $mo_sh[$arr[1] - 1]. '/' . $arr[0] : $mo_sh[$val - 1];
			break;
			case "month_lo_sl_year_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? $mo_lo[$arr[1] - 1]. '/' . $arr[0] : $mo_lo[$val - 1];
			break;
			
			// Quarter formats
			case "quarter_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? 'Q' . ($arr[1] + 1) : 'Q' . ($val + 1);
			break;

			case "quarter_sh_year_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? 'Q' . ($arr[1] + 1). ' ' . substr($arr[0], 2, 2) : 'Q' . ($val + 1);
			break;

			case "quarter_sh_year_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? 'Q' . ($arr[1] + 1) . ' ' . $arr[0] : 'Q' . ($val + 1);
			break;


			case "quarter_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? 'Quarter' . ($arr[1] + 1) : 'Quarter' . ($val + 1);
			break;
			
			case "quarter_lo_year_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? 'Quarter' . ($arr[1] + 1). ' ' . substr($arr[0], 2, 2) : 'Quarter' . ($val + 1);
			break;

			case "quarter_lo_year_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? 'Quarter' . ($arr[1] + 1) . ' ' . $arr[0] : 'Quarter' . ($val + 1);
			break;
			
			
			
			// Semester formats
			case "sem_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? 'S' . ($arr[1] + 1) : 'S' . ($val + 1);
			break;

			case "sem_sh_year_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? 'S' . ($arr[1] + 1). ' ' . substr($arr[0], 2, 2) : 'S' . ($val + 1);
			break;

			case "sem_sh_year_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? 'S' . ($arr[1] + 1) . ' ' . $arr[0] : 'S' . ($val + 1);
			break;


			case "sem_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? 'Semester' . ($arr[1] + 1) : 'Semester' . ($val + 1);
			break;
			
			case "sem_lo_year_sh":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? 'Semester' . ($arr[1] + 1). ' ' . substr($arr[0], 2, 2) : 'Semester' . ($val + 1);
			break;

			case "sem_lo_year_lo":
				$arr = explode(' ', $val);
				return (isset($arr[0]) && isset($arr[1])) ? 'Semester' . ($arr[1] + 1) . ' ' . $arr[0] : 'Semester' . ($val + 1);
			break;
			
			
			
			// Year formats
			case "year_short":
				return substr($val, 2, 2);
			break;
			case "year_long":
				return $val;
			break;			
			
			default: 
				return $val;
			break;
		}
	}
	
	/**
	* Gets the series data form the series recordsets and stores it in assoriative arrays
	* using the category values as keys
	*
	* @returns - nothing
	*/	
	function addSeriesData($i) {
		$rs = $this->series[$i]['rs'];
		$row = $this->series[$i]['row'];
		$cat_col = $this->getCategoryIDColumn($i); 
		$val_col = ($this->plotType != 'directValues' && $this->plotType != '') ? 'xtd_value' : $this->getCleanColumnName($this->series[$i]['value_col']);
		$link_col =  $this->getCleanColumnName($this->series[$i]['linkColumn']);
		$color_col =  $this->getCleanColumnName($this->series[$i]['colorColumn']);
        $target_col =  $this->getCleanColumnName($this->series[$i]['targetColumn']);
        
		$index = 0;
		
		$data = array();
		$extra = array();
		
		if (mysql_num_rows($rs) > 0) mysql_data_seek($rs, 0);
		$row = mysql_fetch_assoc($rs);
		// retrive data to an asscociative array
		do {
			if ($cat_col != '' && $cat_col != NULL) {
	      		$data[$row[$cat_col]] = $row[$val_col];
				$extra[$row[$cat_col]] = array();
				$extra[$row[$cat_col]]['link'] = $this->getDynamicContents($link_col, $row);
				$extra[$row[$cat_col]]['color'] = $this->getDynamicContents($color_col, $row);
                $extra[$row[$cat_col]]['target'] = $this->getDynamicContents($target_col, $row);
			} else {
				$data[$index] = $row[$val_col];
				$extra[$index] = array();
				//die(join('xx',$row)."__________".join(',', array_keys($row)));
				$extra[$index]['link'] = $this->getDynamicContents($link_col, $row);
				$extra[$index]['color'] = $this->getDynamicContents($color_col, $row);
                $extra[$index]['target'] = $this->getDynamicContents($target_col, $row);
				
				$index++;
				
			}
		} while ($row = mysql_fetch_assoc($rs));
		if (mysql_num_rows($rs) > 0) mysql_data_seek($rs, 0);
		
		// send data to the series_data array with correspondence to the categories
		$index = 0;
		foreach ($this->categ_data as $key=>$value) {
			if ($cat_col != '' && $cat_col != NULL) {
				$this->series_data[$i][$key] = (isset($data[$key])) ? $data[$key] : '';
				$this->extra_data[$i][$key] = (isset($extra[$key])) ? $extra[$key] : 0;
			} else {
				$this->series_data[$i][$key] = (isset($data[$index])) ? $data[$index] : '';
				$this->extra_data[$i][$key] = (isset($extra[$index])) ? $extra[$index] : 0;
				$index++;
			}
		}
	}
	
	/**
	* Gets the series data form the series recordsets and stores it in assoriative arrays
	* using the category values as keys
	*
	* @returns - nothing
	*/	
	function addSeriesDataForXY($i) {
		$rs = $this->series[$i]['rs'];
		$row = $this->series[$i]['row'];
		$cat_col = $this->getCategoryIDColumn($i); 
		$xcol = $this->getCleanColumnName($this->series[$i]['value_col']);
        $yColumn = "";
        $zColumn = "";
        
        if (isset($this->series[$i]['yColumn'])) {
		    $yColumn = $this->getCleanColumnName($this->series[$i]['yColumn']);
        }
        if (isset($this->series[$i]['zColumn'])) {
		    $zColumn = $this->getCleanColumnName($this->series[$i]['zColumn']);
        }
		
		$link_col =  (isset($this->series[$i]['linkColumn'])) ? $this->getCleanColumnName($this->series[$i]['linkColumn']) : "";
		$color_col =  (isset($this->series[$i]['colorColumn'])) ? $this->getCleanColumnName($this->series[$i]['colorColumn']) : "";		
      		$target_col =  (isset($this->series[$i]['targetColumn'])) ? $this->getCleanColumnName($this->series[$i]['targetColumn']) : "";		
		
		$index = 0;
		$extra  = array();
					
		if (mysql_num_rows($rs) > 0) mysql_data_seek($rs, 0);
		$row = mysql_fetch_assoc($rs);
		// retrive data to an asscociative array
		do {
			$value = $row[$xcol];
			if ($yColumn != '') $value .= ";". $row[$yColumn];
			if ($zColumn != '') $value .= ";". $row[$zColumn];
			
			$extra[$index] = array();
			$extra[$index]['link'] = ($link_col != '') ? $this->getDynamicContents($link_col, $row) : "";
			$extra[$index]['color'] = ($color_col != '') ? $this->getDynamicContents($color_col, $row) : "";
           		$extra[$index]['target'] = ($target_col != '') ? $this->getDynamicContents($target_col, $row) : "";
			
			$this->series_data[$i][$index] = $value;
			$this->extra_data[$i][$index] = $extra[$index];
			$index++;
		} while ($row = mysql_fetch_assoc($rs));
		if (mysql_num_rows($rs) > 0) mysql_data_seek($rs, 0);		
	}
	
	/**
	* Arranges the data corresponding the the ordering options (depending on arrangeType and arrangeOrder)
	*
	* @returns - nothing
	*/		
	function arrangeData() {
		switch ($this->arrangeType) {
			case "none":
				$this->categ_arr = array_keys($this->categ_data);	
			break;
			
			case "category": 
				if ($this->arrangeOrder == "asc") {
					asort($this->categ_data);
				} else {
					arsort($this->categ_data);
				}
				$this->categ_arr = array_keys($this->categ_data);
			break;
			
			default: 
				$index = str_replace('series', '', $this->arrangeType);
				if ($this->arrangeOrder == "asc") {
					asort($this->series_data[$index]);
				} else {
					arsort($this->series_data[$index]);
				}
				$this->categ_arr = array_keys($this->series_data[$index]);
			break;
		}
	}
	
	// --------------------------------- XML creation functions ---------------------------------
	
	/**
	* Creates the categories XML
	*
	* @returns - nothing
	*/		
	function setCategoryXML() {
		$retStr = "<categories>";
		for ($i=0;$i<count($this->categ_arr);$i++) {
			//$retStr .= "<category label='".$this->categ_data[$this->categ_arr[$i]]."' />";
			if ($this->chartType != "Scatter" && $this->chartType != "Bubble") {
            $retStr .= "<category label='".$this->getCategoryLabel($this->categ_data[$this->categ_arr[$i]], 0)."' />";
			} else {
				$parts = explode('|', $this->getCategoryLabel($this->categ_data[$this->categ_arr[$i]], 0));
				$label = str_replace('XTD_PIPE', '|', str_replace('XTD_DEL', ';', $parts[0]));
				$xval = (isset($parts[1])) ? str_replace('XTD_PIPE', '|', str_replace('XTD_DEL', ';', $parts[1])) : $label;
				$retStr .= "<category label='".$label."' x='".$xval."' />";
			}
		}
		$retStr .= "</categories>";
		
		$this->categoryXML = $retStr;
	}
	
	/**
	* Creates the XML for one series
	*
	* @returns - nothing
	*/		
	function setSeriesXML($index) {
		$tagName = ($this->chartType == "MSStackedColumn2DLineDY" && strstr($this->seriesProps[$index], "renderAs='Line'") != false) ? "lineset" : "dataset";
		$retStr = "<" . $tagName . " ".$this->seriesProps[$index].">";
		//$retStr = "<dataset ".$this->seriesProps[$index].">";
		
		for ($i=0;$i<count($this->categ_arr);$i++) {
			$val = $this->series_data[$index][$this->categ_arr[$i]];

			$retStr .= "<set value='".$val."'";			
			
			if ($this->extra_data[$index] != 0) {
				$link = $this->extra_data[$index][$this->categ_arr[$i]]['link'];
				$color = $this->extra_data[$index][$this->categ_arr[$i]]['color'];
				$target = $this->extra_data[$index][$this->categ_arr[$i]]['target'];
                
                $target_prefix = "";
                if ($target != "") {
                    switch(strtolower($target)) {
                        case "_self": $target_prefix = ""; break;
                        case "_blank": $target_prefix = "n-"; break;
                        default: $target_prefix = "F-".$target; break;
                    }
                }
				
				if ($link != "") { 
					$retStr.=" link='".$target_prefix.$link."'";
				}
				
				if ($color != "") { 
					$retStr.=" color='0x".str_replace("#", "",str_replace("0x", "", $color))."'";
				}
			}
			$retStr.="/>";
		}
		$retStr .= "</" . $tagName . ">";
		
		if ($tagName == "dataset") {
			$this->seriesXML[$index] = $retStr;
		} else {
			$this->lineSetXML[$index] = $retStr;
		}
	}
	
	/**
	* Creates the XML for one series
	*
	* @returns - nothing
	*/		
	function setSeriesXMLForXY($index) {
		$tagName = "dataset";
		$retStr = "<" . $tagName . " ".$this->seriesProps[$index].">";
		
		for ($i=0;$i<count($this->series_data[$index]);$i++) {
			$parts = explode(';', $this->series_data[$index][$i]);
			$x = $parts[0];
			$y = (isset($parts[1])) ? $parts[1] : '';
			$z = (isset($parts[2])) ? $parts[2] : '';

			$extraStr = "";
			if ($this->extra_data[$index] != 0) {
				$link = $this->extra_data[$index][$i]['link'];
				$color = $this->extra_data[$index][$i]['color'];
				$target = $this->extra_data[$index][$i]['target'];
                
                $target_prefix = "";
                if ($target != "") {
                    switch(strtolower($target)) {
                        case "_self": $target_prefix = ""; break;
                        case "_blank": $target_prefix = "n-"; break;
                        default: $target_prefix = "F-".$target; break;
                    }
                }
				
				if ($link != "") { 
					$extraStr .= " link='".$target_prefix.$link."'";
				}
				if ($color != "") { 
					$extraStr.=" color='0x".str_replace("#", "",str_replace("0x", "", $color))."'";
				}	
			}
			
			if ($this->chartType == "Scatter") {
				$retStr .= "<set x='".$x."' y='".$y."' ".$extraStr." />";
			}
			if ($this->chartType == "Bubble") {
				$retStr .= "<set x='".$x."' y='".$y."' z='".$z."' ".$extraStr." />";
			}			
		}
		$retStr .= "</" . $tagName . ">";
		
		$this->seriesXML[$index] = $retStr;
	}
	
	// --------------------------------- XML retrival functions ---------------------------------
	
	/**
	* Gets the XML for a single series chart
	*
	* @returns - nothing
	*/		
	function getSingleSeriesDataXML() {
		$retStr = "";
		
		for ($i=0;$i<count($this->categ_arr);$i++) {
			$val = $this->series_data[0][$this->categ_arr[$i]];
            
            $extraStr = "";

				$link = $this->extra_data[0][$this->categ_arr[$i]]['link'];
				$color = $this->extra_data[0][$this->categ_arr[$i]]['color'];
				$target = $this->extra_data[0][$this->categ_arr[$i]]['target'];
                
                $target_prefix = "";
                if ($target != "") {
                    switch(strtolower($target)) {
                        case "_self": $target_prefix = ""; break;
                        case "_blank": $target_prefix = "n-"; break;
                        default: $target_prefix = "F-".$target; break;
                    }
                }
				
				if ($link != "") { 
					$extraStr .= " link='".$target_prefix.$link."'";
				}
				if ($color != "") { 
					$extraStr.=" color='0x".str_replace("#", "",str_replace("0x", "", $color))."'";
				}	

			
			//$retStr .= "<set label='".$this->categ_data[$this->categ_arr[$i]]."' value='".$val."' />";
            $retStr .= "<set label='".$this->getCategoryLabel($this->categ_data[$this->categ_arr[$i]], 0)."' value='".$val."' ".$extraStr." />";
		}
		
		return $retStr;
	}
	
	/**
	* Gets the XML for a multiple series chart
	*
	* @returns - nothing
	*/	
	function getMultiSeriesDataXML() {
		$retStr = "";
		$retStr .= $this->categoryXML;
		$retStr .= join('', $this->seriesXML);
		if (isset($this->lineSetXML) && is_array($this->lineSetXML)) {
			$retStr .= join('', $this->lineSetXML);
		}
		
		return $retStr;
	}
	
	/**
	* Gets the XML for a multi series chart that uses dataset groups
	*
	* @returns - nothing
	*/	
	function getGroupedDataXML() {
		$retStr = "";
		$retStr .= $this->categoryXML;
		foreach ($this->groups as $key=>$value) {
			$sets = $value;
			$retStr .= "<dataset>";
			for ($j=0;$j<count($sets);$j++) {
				if (isset($this->seriesXML[$sets[$j]])) {
					$retStr .= $this->seriesXML[$sets[$j]];
				}
			}
			$retStr .= "</dataset>";
		}
		if (isset($this->lineSetXML) && is_array($this->lineSetXML)) {
			$retStr .= join('', $this->lineSetXML);
		}
		
		return $retStr;
	}
	
	function needGroups() {
		if ($this->chartType == "MSStackedColumn2DLineDY" || $this->chartType == "MSStackedColumn2D") {
			return true;
		}
		
		return false;
	}
	
	
	/**
	* Replaces the dynamic fields with data from database
	*
	* @str - content string
	* @data - data object 
	*
	* @returns - modified string
	*/
	
	function getDynamicContents($str, $data_) {
		$prefix = '{';
		$suffix = '}';
		$cols = $this->getUsedColumns($str, $prefix, $suffix);
		$res = $str;
		
		for ($i=0;$i<count($cols);$i++) {
			$res = str_replace($prefix.$cols[$i].$suffix, $data_[$cols[$i]], $res);
			
		}
		
		return $res;
	}
	
	/**
	* Returns the columns that occur in a custom html string
	*
	* @str - custom html string
	* @prefix, suffix - the columns footprints;
	*
	* @returns - array of columns names
	*/
	
	function getUsedColumns($str, $prefix, $suffix) {
		$source = $str;
		$cols = array();
		
		while ($source = strstr($source, $prefix)) {
			$pos = strpos($source, $suffix);
			$col = substr($source, strlen($prefix), $pos - strlen($suffix));
			$source = substr($source, strlen($prefix));

			$cols[] = $col;
		}
		
		return $cols;
	}	
}

?>