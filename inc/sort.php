<?php 
	function sort_array_of_array(&$array, $subfield)
	{
		$sortarray = array();
		foreach ($array as $key => $row)
		{
			$sortarray[$key] = $row[$subfield];
		}
			array_multisort($sortarray, SORT_DESC , $array);
	}
?>