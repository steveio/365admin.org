<?php

/*
 * Takes a list of elements and sorts them into an array of X columns
 * 
 */


class ColumnSort {
	
	private $elements;
	private $total_elements;
	private $cols;
	private $elements_per_col;
	
	private $aCols;
	
	public function __Construct() {
		$this->aCols = array();
	}
	
	public function SetElements($a) {
		$this->elements = $a;
		$this->SetTotalElements();
	}
	
	private function SetTotalElements() {
		$this->total_elements = count($this->elements);
	}
	
	public function SetCols($cols) {
		$this->cols = $cols;
		$this->SetElementsPerCol();
	}
	
	private function SetElementsPerCol() {
		$this->elements_per_col = floor($this->total_elements / $this->cols);
	} 
	
	public function Sort() {

		$i = 0; // current element index
		$j = 1; // current column index
		$k = 0; // items per column count
		do {
			if (($k++ <= $this->elements_per_col) || ($j == $this->cols)) {
				$this->aCols[$j][] = $this->elements[$i++];
			} else {
				$k = 0;
				$j++;
			}
		} while ($i <  $this->total_elements);
				
		return $this->aCols;
	}
	
}