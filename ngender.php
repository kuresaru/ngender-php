<?php
//检查是否有参数
if (!isset($_GET['name'])) {
    die('没有指定name参数');
}
$keepFirst = false;
if (isset($_GET['keepFirst']) && $_GET['keepFirst'] == "true") {
    $keepFirst = true;
}
//取名字
$name = $_GET['name'];
//打开文件
$fhandle = fopen('charfreq.csv','r');
//跳过第一行
fgets($fhandle);
//读数据库
$total = 0;
$total_male = 0;
$total_female = 0;
$freq = array();
while (!feof($fhandle)) {
    $line = explode(',', fgets($fhandle));
    if (count($line) == 3) {
        $total_male += intval($line[1]);
        $total_female += intval($line[2]);
        $freq[$line[0]] = array(floatval($line[2]), floatval($line[1]));
    }
}
$total = $total_male + $total_female;
foreach ($freq as $char=>$value) {
    $freq[$char] = array(1.0 * $value[0] / $total_female, 1.0 * $value[1] / $total_male);
}

//计算一个性别的概率
function probForGender($firstname, $gender) {
    global $total_male;
    global $total_female;
    global $total;
    global $freq;
    $p;
    if ($gender == 0) {
        $p = 1.0 * $total_female / $total;
    } else {
        $p = 1.0 * $total_male / $total;
    }
    //遍历汉字字符串
    preg_match_all('/[\x{4e00}-\x{9fa5}]/u', $firstname, $chinese);
    preg_match_all('/[^\x{4e00}-\x{9fa5}]/u', $firstname, $string);
    $result = array_merge(current($chinese), current($string));
    foreach ($result as $word) {
        //直接忽略非汉字字符
        if (preg_match('/^[\x{4e00}-\x{9fa5}]$/u', $word)) {
            if (array_key_exists($word, $freq)) {
                $p *= $freq[$word][$gender];
            }
        }
    }
    return $p;
}

//开始计算
$firstname = '';
$actualname = '';
$firstFlag = !$keepFirst;
//遍历汉字字符串
preg_match_all('/[\x{4e00}-\x{9fa5}]/u', $name, $chinese);
preg_match_all('/[^\x{4e00}-\x{9fa5}]/u', $name, $string);
$result = array_merge(current($chinese), current($string));
foreach ($result as $word) {
    //直接忽略非汉字字符
    if (preg_match('/^[\x{4e00}-\x{9fa5}]$/u', $word)) {
        $actualname = $actualname . $word;
        //去掉第一个字
        if ($firstFlag) {
            $firstFlag = false;
        } else {
            $firstname = $firstname . $word;
        }
    }
}
$pFemale = probForGender($firstname, 0);
$pMale = probForGender($firstname, 1);
if ($pMale > $pFemale) {
    echo 
'{
    "result": [
        "male", 
        ' . (1.0 * $pMale / ($pMale + $pFemale)) . '
    ], 
    "keepFirst": ' . ($keepFirst ? "true" : "false") . ',
    "actualName": "' . $actualname . '"
}';
} else {
    echo 
'{
    "result": [
        "female", 
        ' . (1.0 * $pFemale / ($pMale + $pFemale)) . '
    ], 
    "keepFirst": ' . ($keepFirst ? "true" : "false") . ',
    "actualName": "' . $actualname . '"
}';
}
?>