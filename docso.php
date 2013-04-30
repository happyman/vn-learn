<html>
<head><meta charset="utf-8" />
<title>đọc số VN</title>
<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed&subset=latin,vietnamese' rel='stylesheet' type='text/css'>
<style>
body {font-family:'Roboto Condensed', sans-serif; font-size:5em;}
input {font-size:40px;}
.ans { color: green; }
</style>
<body>
<form method="POST">
số:<input type="text" name="num" value="<?php echo $_REQUEST['num'];?>">
<input type="submit" value="đọc">
</form>
<?php

$num = $_REQUEST['num'];
if (empty($num)) {
	exit;
}
$so = new VN_Num($num);
echo number_format($num);
echo " đọc <span class='ans'>". $so->doit() . "</span>";

class VN_Num {

	var $table = array(
		"0" => "không",
		"1" => "một",
		"2" => "hai",
		"3" => "ba",
		"4" => "bốn",
		"5" => "năm",
		"6" => "sáu",
		"7" => "bảy",
		"8" => "tám",
		"9" => "chín",
		"10" => "mười",
		"11" => "mười một",
		"12" => "mười hai",
		"13" => "mười ba",
		"14" => "mười bốn",
		"15" => "mười lăm",
		"16" => "mười sáu",
		"17" => "mười bảy",
		"18" => "mười tám",
		"19" => "mười chín"
	);
	var $table2 = array("4" => "tư", "5" => "lăm", "1" => "mốt");
	var $ss = array("mươi","trăm");
	var $tss = array("", "nghìn","triệu","tỷ", "nghìn tỷ" );
	var $tss_1 = array("một", "mười", "một chăm","một nghìn", "", "", "một triệu","","","một tỷ","","","một nghìn tỷ");

	var $num;
	var $len;
	var $force_pad;
	var $debug=0;
	function __construct($num) {
		if (!is_numeric($num))
			return false;
		$this->num = $num;
		$this->len = strlen($num);
		if ($this->len > 15 ) {
			return false;
		}
	}

	function doit() {
		if ($this->len > 15 ) return "số này dài quá, không dọc được";
		if ($this->len ==0) return "cái gì đấy";


		$num_str = number_format($this->num);
		if ($this->len < 4  ){
			$ans[] = $this->read_nums($this->num);
		}
		else {
			$nghins = array_reverse(preg_split("/,/",$num_str));

			if ($this->debug)
				print_r($nghins);
			for($i=0; $i<count($nghins); $i++) {
				if ($i != count($nghins)-1) 
					$this->force_pad =1; else $this->force_pad =0;
				if (intval($nghins[$i] == 1) ) {
					if ($this->force_pad == 1 && $i == 0 )
						$ans[] = "linh một";
					else
						$ans[] = $this->tss_1[$i*3];
				}
				else if (intval($nghins[$i] == 0 ))
					;
				else {
					if ($i != count($nghins)-1) 
						$this->force_pad =1; else $this->force_pad =0;
					$ans[] = $this->read_nums($nghins[$i]) . " " . $this->tss[$i];
				}
			}
		}
		if ($this->debug) {
			echo "num=$num_str\n";
			echo "read=".implode(" * ", array_reverse($ans));
			echo "\n";
		}
		return implode(" * ", array_reverse($ans));
	}


	function read_nums($num) {
		$num_str = str_pad($num, 3, 0, STR_PAD_LEFT);
		// 百位數
		if ($this->debug) {
			echo "read_nums: $num_str \nforce_pad= ".$this->force_pad;
		}
		if ($num > 100 || $this->force_pad == 1) $pad = 1; else $pad = 0;
		if ($this->debug)
			echo "pad = $pad\n";
		$a = array();
		switch($num_str{0}) {
		case '0':
			if ($pad == 1) $a[] = "không trăm";
			break;
		case '1':
			$a[] = $this->tss_1[2];
			break;
		default:
			$a[] = $this->table[$num_str{0}];
			$a[] = $this->ss[1]; // 百
			break;
		}
		$num_10 = substr($num_str, 1);
		if (isset($this->table[$num_10]))
			$a[] = $this->table[$num_10];
		else {
			switch($num_str{1}) {
			case '0':
				if ($pad == 1 && $num_str{2} != 0) 
					$a[] = "linh";
				break;
			case '1':
				$a[] = $this->tss_1[1];
				break;
			default:
				$a[] = $this->table[$num_str{1}];
				$a[] = $this->ss[0]; // 十

				break;
			}
			switch($num_str{2}) {
			case 0:
				break;
			case 1:
				if ($num_str{1} >= 1 )
					$a[] = "mốt";
				else
					$a[] = $this->table[$num_str{2}];
				break;
			case 4:
				if ($num_str{1} >= 2 )
					$a[] = "tư";
				else
					$a[] = $this->table[$num_str{2}];
				break;
			case 5:
				if ($num_str{1} >= 2 )
					$a[] = "lăm";
				else
					$a[] = $this->table[$num_str{2}];
				break;
			default:

				$a[] = $this->table[$num_str{2}];
				break;
			}
		}
		if (empty($a)) $a[] = $this->table[0];
		return implode(" ", $a);
	}
}
