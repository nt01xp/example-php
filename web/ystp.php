<?php

$cctv_list = array(
    "cctv1_10m" => "Live1717729995180256",
    "cctv2_10m" => "Live1718261577870260",
    "cctv3_10m" => "Live1718261955077261",
    "cctv4_10m" => "Live1718276148119264",
    "cctv5_10m" => "Live1719474204987287",
    "cctv5p_10m" => "Live1719473996025286",
    "cctv7_10m" => "Live1718276412224269",
    "cctv8_10m" => "Live1718276458899270",
    "cctv9_10m" => "Live1718276503187272",
    "cctv10_10m" => "Live1718276550002273",
    "cctv11_10m" => "Live1718276603690275",
    "cctv12_10m" => "Live1718276623932276",
    "cctv13_10m" => "Live1718276575708274",
    "cctv14_10m" => "Live1718276498748271",
    "cctv15_10m" => "Live1718276319614267",
    "cctv16_10m" => "Live1718276256572265",
    "cctv164k_36m" => "Live1704966749996185",
    "cctv164k_10m" => "Live1704966749996185",
    "cctv17_10m" => "Live1718276138318263",
    "cctv4k_36m" => "Live1704872878572161",
    "cctv4k_10m" => "Live1704872878572161",
    "cctv8k_36m" => "Live1688400593818102",
    "cgtn_10m" => "Live1719392219423280",
    "cgtnfr_10m" => "Live1719392670442283",
    "cgtnru_10m" => "Live1719392779653284",
    "cgtnar_10m" => "Live1719392885692285",
    "cgtnsp_10m" => "Live1719392560433282",
    "cgtnen_10m" => "Live1719392360336281"
);

$channel = "cctv1_10m"; //example
$uid =rand(1000000001,1230000001);
$datas = explode(",", $cctv_list[$channel]);
$data = get_url($channel, $datas[0], $uid);
header("content-type=application/x-mpegURL;filename=" . $channel . ".m3u8");
echo $data; exit;
function get_url($id, $hid, $uid)
{
  
    $playUrl = '';  
    $url = "https://ytpaddr.cctv.cn/gsnw/live";
    $dd = '{
			"rate": "",
			"systemType": "android",
			"id": "",
			"userId": "",
			"clientSign": "cctvVideo",
			"deviceId": {
				"serial": "",
				"imei": "",
				"android_id": "42e4f5c90dc7f060"
			}
		}';
        $requestData = json_decode($dd, true);
        $requestData['id'] = $hid;
        $jsonData = json_encode($requestData);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'cURL 错误: ' . curl_error($ch);
        }
        curl_close($ch);
		//print_r($response);exit;
        $retData = json_decode($response, true);
        $retData = json_decode($response, true);
		//print_r($retData); exit;
		//print_r($retData['data']['videoList']);exit;
        $videoList=$retData && isset($retData['data']['videoList']);
        $url = $videoList ? $retData['data']['videoList'][0]['url'] : '';
        $bstrURL = "https://ytpvdn.cctv.cn/cctvmobileinf/rest/cctv/videoliveUrl/getstream";
        $postData = 'appcommon={"ap":"cctv_app_tv","an":"央视投屏助手","adid":" ' . $uid . '","av":"1.1.7"}&url=' . $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $bstrURL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $APPRANDOMSTR = uniqid();
        $secretKey = "5f39826474a524f95d5f436eacfacfb67457c4a7";
        $rawSign = "5f39826474a524f95d5f436eacfacfb67457c4a7e7e259bbb0ac4848ba70921c860a1216" . $APPRANDOMSTR;
        $APPSIGN = md5($rawSign);
        $headers = [
            "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36",
            "Referer: https://www.yangshipin.cn/",
            "UID: $uid",
            "APPID: $secretKey",
            "APPSIGN: $APPSIGN",
            "APPRANDOMSTR: $APPRANDOMSTR"
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        curl_close($ch);
        $obj = json_decode($data);
        $playUrl = $obj->url;

    
    $ch = curl_init();
    $path = substr($playUrl, 0, strrpos($playUrl, '/') + 1);
    while (true) {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $playUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: cctv_app_tv", "Referer: api.cctv.cn", "UID: " . $uid));
        $data = curl_exec($ch);
        preg_match('/(.*\.m3u8\?.*)/', $data, $matches);
        if (!empty($matches)) {
            $m3u8_url = $matches[0];
            $playUrl = $path . $m3u8_url;
        } else {
            break;
        }
    }
    $data = preg_replace('/(.*?.ts)/i', $path . '$1', $data);
    curl_close($ch);
    return $data;
}

?>
