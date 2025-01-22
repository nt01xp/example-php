<?php
error_reporting(0);
header("Content-Type:application/json;charset=utf-8");
function f_curl($url,$hdr,$data,$hosts,$ist){
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    if($hdr!="") curl_setopt($ch,CURLOPT_HTTPHEADER,$hdr);
    if($data!="") curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    if($hosts!="") curl_setopt($ch,CURLOPT_RESOLVE,$hosts);
    if($ist==1) curl_setopt($ch,CURLOPT_HEADER,1);
    $cj_tempz=curl_exec($ch);
    if($ist!="") $cj_tempz=curl_getinfo($ch);
    curl_close($ch);
    return $cj_tempz;
}
function f_ppei($id){
    $ids=[
        "cctv1"=>"Live1717729995180256",
        "cctv2"=>"Live1718261577870260",
        "cctv3"=>"Live1718261955077261",
        "cctv4"=>"Live1718276148119264",
        "cctv5"=>"Live1719474204987287",
        "cctv5p"=>"Live1719473996025286",
        "cctv7"=>"Live1718276412224269",
        "cctv8"=>"Live1718276458899270",
        "cctv9"=>"Live1718276503187272",
        "cctv10"=>"Live1718276550002273",
        "cctv11"=>"Live1718276603690275",
        "cctv12"=>"Live1718276623932276",
        "cctv13"=>"Live1718276575708274",
        "cctv14"=>"Live1718276498748271",
        "cctv15"=>"Live1718276319614267",
        "cctv16"=>"Live1718276256572265",
        "cctv17"=>"Live1718276138318263",
        "cgtnen"=>"Live1719392219423280",
        "cgtndoc"=>"Live1719392360336281",
        "cgtnru"=>"Live1719392779653284",
        "cgtnsp"=>"Live1719392560433282",
        "cgtnar"=>"Live1719392885692285",
        "cgtnfr"=>"Live1719392670442283",
        "cctv16_4k"=>"Live1704966749996185",
        "cctv4k"=>"Live1704872878572161",
        "cctv8k_36m"=>"Live1688400593818102"
    ];
    $idz=$ids[$id];
    if($idz=="") die("404");
    return $idz;
}
$uid="1231231231";
if($_GET["ts"]!=""){
    $url="http://liveali-tpgq.cctv.cn/live/".str_replace("@","&",str_replace("!","?",$_GET["ts"]));
    $hdr=["User-Agent:cctv_app_tv","uid:".$uid];
    $gda=f_curl($url,$hdr,"","",0);
    header('Content-Type:application/vnd.apple.mpegurl');
    die($gda);
}
$id=str_replace(".m3u8","",$_GET["id"]);
if($id=="") die("404");
if($id=="cctv6"){
    header('location:http://...');
}else{
    $id=f_ppei($id);
    $url=json_decode(f_curl("https://ytpaddr.cctv.cn/gsnw/live","",'{"id":"'.$id.'","clientSign":"cctvVideo"}',"",0))->data->videoList[0]->url;
    if($url=="") die("404");
    $aid="5f39826474a524f95d5f436eacfacfb67457c4a7";
    $rid=mt_rand(1000000000,9999999999);
    $sign=md5($aid."e7e259bbb0ac4848ba70921c860a1216".$rid);
    $hdr=["User-Agent:cctv_app_tv","uid:".$uid,"appid:".$aid,"appsign:".$sign,"apprandomstr:".$rid];
    $purl=json_decode(f_curl("http://ytpvdn.cctv.cn/cctvmobileinf/rest/cctv/videoliveUrl/getstream",$hdr,"url=".$url,"",0))->url;
    if($purl=="") die("404");
    $hdr=["User-Agent:cctv_app_tv","uid:".$uid];
    $purl=f_curl($purl,$hdr,"","",0);
    $purl=str_replace("&","@",str_replace("?","!",$purl));
    $purl=preg_replace('/(.*?.ts)/i','http://your domian/xxtv.php?ts='.'$1',$purl);
    header('Content-Type:application/vnd.apple.mpegurl');
    echo $purl;
}
die();
?>
