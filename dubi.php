<?php
	

	
	if (array_key_exists("input", $_POST) &&  $_POST["input"] != '') {
		$input = $_POST["input"];
		$i = 0;
		
		$listOfOpenChars = array("{" => ["start" => false, "value" => 0, "parent"=>""], 
								 "[" => ["start" => false, "value" => 0, "parent"=>""], 
								 "(" => ["start" => false, "value" => 0, "parent"=>""]);
		$listOfCloseChars = array("}" => "{", "]" => "[", ")" => "(");

		
		while ($i < strlen($input) ) {
			$char = substr($input, $i, 1);
			

			if ($char=='{' || $char=='[' || $char=='(') {
				
				$listOfOpenChars[$char]["value"]++;
				$listOfOpenChars[$char]["start"] = true;
				
			} elseif ($char=='}' || $char==']' || $char==')') {
			
				$previousChar = $i == 0 ? "" : substr($input, $i - 1, 1);
				if ($previousChar <> '' && $previousChar <> $listOfCloseChars[$char] && !array_key_exists($previousChar, $listOfCloseChars)) {
					echo "Hatalı parantez kapama. 'pos : $i - karakter : $char' <br />";
					break;
				} elseif ($listOfOpenChars[$listOfCloseChars[$char]]["value"] == 0  ) {
					echo "Parantez açılmadan kapanmış. 'pos : $i - karakter : $char' <br />";
					break;
				} else {
					$listOfOpenChars[$listOfCloseChars[$char]]["value"]--;
				}
			} else {
				echo "Hatalı parametre. <br />"; 
				break;
				
			}
			
			$i++;
		}
		
		
		$bFail = false;
		$bStart = false;
		
		foreach ( $listOfOpenChars as $key=> $value )
		{ 
			if ($value["start"]) {
				$bStart = true;
				if ($value["value"] > 10 ) {
					echo "Çok fazla kapanmamış parantez var.";
					$bFail = true;
					break;
				} elseif ($value["value"] > 0) {
					$bFail = true;
				}
			}
		}
		
		if ($bStart) {
			if ($bFail) {
				echo "Başarısız. <br />";
			} else {
				echo "Başarılı. <br />";
			}
		}
		
	}
?>

<form action="" method="post">
<input type="text" name="input" value ="<?php if (array_key_exists("input", $_POST) &&  $_POST["input"] != '') { echo $_POST['input']; } ?>"/>
<input type="submit" value="Gönder"/>
</form>