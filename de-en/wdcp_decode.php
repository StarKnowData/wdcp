<?php
/*
Authou: JoyChou
Date: 2014年12月7日14:50
All of wdcp version
*/
error_reporting(0);

function wdcp_decode($filename) {

    if (!file_exists($filename)) {
        echo '文件名不存在';
        exit(); //文件名不存在
    }
        
    $data = unpack('C*', substr(file_get_contents($filename), 9)) ;  //从第10个字符串开始解密
    $nine_bytes = '09574154574443504D';  // wdcp加密后的前9个字节数据
    $str_nice_bytes =  pack('H*', $nine_bytes);  // 16进制转字符串
   
    // 判断是否是wdcp加密的，如果不是直接退出
    if(strncmp(file_get_contents($filename), $str_nice_bytes, 9) != 0){
        echo '文件'. $filename. '不是wdcp加密' . '<br>';
        // exit();
    }
    $key = array(0xB8, 0x35, 0x6, 0x2, 0x88, 0x1, 0x5B, 0x7, 0x44, 0x0);
    $j = count($data);
    foreach($data as $k => &$v) {
            $v = $key [ 2 * ($j % 5) ] ^ ~$v;
            // $v = sprintf('%u', $v);
            $v &= 0xFF;  //此时$v是int类型
          
            $v = pack('C*', $v); //pack后变成了string字符串
            
            -- $j;
    }
    return gzuncompress(join('', $data));  // join 将一个一维数组的值转化为字符串
}


function Traversal_Files($path = '.'){

    if (!is_dir($path)) {
        echo '参数不是目录';
        exit();
    }
    // //opendir()返回一个目录句柄,失败返回false
    if ($current_dir = opendir($path)) {
        while (false !== ($file = readdir($current_dir))) { // readdir获取当前目录的文件名喝目录名以及. ..
            $sub_dir = $path . DIRECTORY_SEPARATOR . $file;  // DIRECTORY_SEPARATOR \或者/
            
            if ($file == '.' || $file == '..') {
                continue;
            }
            else if (is_dir($sub_dir)) {
                echo "<h3> Directory Name  $file </h3>";
                Traversal_Files($sub_dir); //如果是目录，进行递归判断。
            }
            else{
                 $file_ext = substr($file, strrpos($file,".")+1);

                 if ($file_ext == 'php' || $file_ext == 'PHP') {
                    
                    file_put_contents($sub_dir . '_decode', wdcp_decode($sub_dir));
                    unlink($sub_dir); 
                    rename($sub_dir . '_decode', $sub_dir);
                 }
            }
        }
    }

    closedir($current_dir); 
}


// 在这修改wdcp目录即可
Traversal_Files("D:\wdcp\mysql");

?>