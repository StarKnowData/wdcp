<?php

/*
Author: JoyChou
Date: 2014.12.13 17:11
*/
function wdcp_encode($filename){

    $data = file_get_contents($filename); // 获取文件内容

    $nine_bytes = '09574154574443504D';  // wdcp加密后的前9个字节数据
    $str_nice_bytes =  pack('H*', $nine_bytes);  // 16进制转字符串
   
    // 判断是否是wdcp加密的，如果是直接退出
    if(strncmp($data, $str_nice_bytes, 9) == 0){
        echo '文件'. $filename. '已经被wdcp加密，不能再加密' . '<br>';
        exit();
    }
    $gz_data = gzcompress($data);   // gzcompress编码


    $length  = strlen($gz_data);
    $array_gz_data = unpack('C*', $gz_data);
    $secret = array(0xB8, 0x35, 0x6, 0x2, 0x88, 0x1, 0x5B, 0x7, 0x44, 0x0);

    foreach ($array_gz_data as $key => &$v) {
         
         $v = $secret [ 2 * ($length % 5) ] ^ ~$v;
         $v &=0xff;
         $v = pack('C*', $v);
         
         --$length;

    }

    $result =  join('',$array_gz_data);  //数组转换为字符串
    file_put_contents('encode_' . $filename ,$str_nice_bytes.$result);

}

wdcp_encode('add_user.php');
echo 'Encode Success';


?>