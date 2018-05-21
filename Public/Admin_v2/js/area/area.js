$(function(){
	//返回后显示之前选择的省份城市地区
//	if(province && city && county) {
//		getProvinces('s1', province);
//		getCitys('s2','s1',city);
//		getAreas('s3', 's2', county);
//	} else {
//		getProvinces('s1', '');
//	}
    getProvinces('s1', '');
	$('#s1').change(function() {
		$('#s2').html('<option>选择城市</option>');
		if($(this).val() != '') {
			getCitys('s2','s1','');
		} else {
            $('input[name=province]').val('');
        }
		$('#s3').html('<option>选择地区</option>');
	});
	$('#s2').change(function () {
		if($(this).val() != '') {
			getAreas('s3', 's2', '');
		} else {
			$('#s3').html('<option>选择地区</option>');	
            $('input[name=city]').val('');
		}
	});
    
    $('#s3').change(function () {
		if($(this).val() != '') {
			$('input[name=county]').val($(this).val());
		} else {
            $('input[name=county]').val('');
        }
	});
})


