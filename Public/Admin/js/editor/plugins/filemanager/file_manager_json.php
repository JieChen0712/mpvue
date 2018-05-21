<?php
	if($_POST["action"]=="delete"){//如果action=delete
    $url=$_POST["url"];
    if(empty($url)){//如果url为空
        die(0);    
    }
    $url = "../..".strstr($url,"/attached/");//替换路径
    if(file_exists($url)){//检查文件是否存在
        $result=unlink($url);//删除文件
        if($result){//如果成功删除
            echo 1;    
        }else{
            echo 0;    
        }
    }else{
        echo 0;    
    }
    exit();
}