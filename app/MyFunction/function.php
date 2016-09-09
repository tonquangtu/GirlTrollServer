<?php
	//Hàm định dạng chuỗi tiếng việt có dấu thành không dấu
	function formatName($str){
		if(!$str) return false;
		$unicode = array(
			'a' => 'á|à|ả|ã|ạ|ă|ằ|ắ|ẳ|ẵ|ặ|â|ầ|ấ|ẩ|ẫ|ậ',
			'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ằ|Ắ|Ẳ|Ẵ|Ặ|Â|Ầ|Ấ|Ẩ|Ẫ|Ậ',
			'd' => 'đ',
			'D' => 'Đ',
			'e' => 'è|é|ẻ|ẽ|ẹ|ê|ề|ế|ể|ễ|ệ',
			'E' => 'È|É|Ẻ|Ẽ|Ẹ|Ê|Ề|Ế|Ể|Ễ|Ệ',
			'i' => 'ì|í|ỉ|ĩ|ị',
			'I' => 'Ì|Í|Ỉ|Ĩ|Ị',
			'o' => 'ò|ó|ỏ|õ|ọ|ô|ồ|ố|ổ|ỗ|ộ|ơ|ờ|ớ|ở|ỡ|ợ',
			'O' => 'Ò|Ó|Ỏ|Õ|Ọ|Ô|Ồ|Ố|Ổ|Ỗ|Ộ|Ơ|Ờ|Ớ|Ở|Ỡ|Ợ',
			'u' => 'ù|ú|ủ|ũ|ụ|ư|ừ|ứ|ử|ữ|ự',
			'U' => 'Ù|Ú|Ủ|Ũ|Ụ|Ư|Ừ|Ứ|Ử|Ữ|Ự',
			'y' => 'ỳ|ý|ỷ|ỹ|ỵ',
			'Y' => 'Ỳ|Ý|Ỷ|Ỹ|Ỵ'
		);
		foreach($unicode as $khongdau=>$codau) {
			$arr = explode("|",$codau);
			$str = str_replace($arr,$khongdau,$str);
		}
		return $str;
	}

	//Hàm để dịnh dạng lại chuỗi theo dạng abc-defg
	function changeTitle($str) {
		$str = trim($str);
		if($str=="") return "";
		$str = str_replace('"','',$str);
		$str = str_replace("'",'',$str);
		$str=formatName($str);
		$str=mb_convert_case($str,MB_CASE_LOWER,'utf-8');
		$str=str_replace(' ','-',$str);
		$str=str_replace('/','-',$str);
		$str=str_replace('_','-',$str);
		return $str;
	}

	//Hàm sử dụng trong việc phân cấp menu (đã hủy chức năng quản lý menu nên không sử dụng nữa)
	function showItemCompany($data,$company_id=0, $str='--',$selected=0){
		foreach($data as $item) {
			if($item->company_id==$company_id){
				if($item->id==$selected)
					echo "<option value = '$item->id' selected='selected'>$str$item->name</option>";
				else
					echo "<option value = '$item->id'>$str$item->name</option>";
				showItemMenu($data, $item->id,$str.'------');
			}
				
		}
	}

	//Hàm cắt chuỗi mà không bị cắt phải từ, sau chuỗi được cắt là dấu 3 chấm nếu còn các kí tự phía sau
	function _substr($str, $length, $minword = 3){
		$sub = '';
		$len = 0;
		foreach (explode(' ', $str) as $word)
		{
		    $part = (($sub != '') ? ' ' : '') . $word;
		    $sub .= $part;
		    $len += strlen($part);
		    if (strlen($word) > $minword && strlen($sub) >= $length)
		    {
		      break;
		    }
		 }
	    return $sub . (($len < strlen($str)) ? '...' : '');
	}
?>