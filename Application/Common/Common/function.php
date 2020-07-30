<?php
	function showTable($table){
		$records = M($table)->select();
		if(empty($records)){
			echo '该表为空';
			die();
		}
		$field = count($records[0]);
		echo "<table border='1' align='center'><tr><th align='center' colspan='$field'>$table</th></tr><tr>";
		foreach($records[0] as $key => $value){
			echo "<td>$key</td>";
		}
		echo "</tr>";
		foreach($records as $record){
		echo "<tr>";
			foreach($record as $value){
				echo "<td>$value</td>";
			}
		echo "</tr>";
		}
		echo "</table>";
	}
	
	function dumpDie($data){
		dump($data);
		die();
	}
?>