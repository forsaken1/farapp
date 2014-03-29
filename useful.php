<?php
/*
FarPost Parser Example
*/

//мега гига функция парсинга
function GetBetween($content,$start,$end){
    $r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}


//Возвращает массив с парой name=> Название раздела, url=> Ссылка раздела
function GetRazdely($url='http://vladivostok.farpost.ru?ajax=1')
{
 if( $curl = curl_init() ) 
 {
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    $Page = curl_exec($curl);
    curl_close($curl);
    $temp="temp";	
    $Razdely=array();
    while($temp!='')
    {
    $temp=GetBetween($Page,'<a class="l1" href=','</a');
    if($temp=="") break;
    $URL=GetBetween($temp,'"','">');
    $name=GetBetween($temp,'">','>');
    $Page=substr($Page, strpos($Page,$name)+50); 
    $Razdely[]= array('name'=>$name,'url'=>$URL);  
    }
 } 
return $Razdely;
}

//Возвращает страницу с контактными данными обявы
function GetContacts($url)
{
 if( $curl = curl_init() ) 
 {
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    $Page = curl_exec($curl);
 
    $URL=GetBetween($Page,'class="bigbutton viewAjaxContacts" href="','">');
    
    curl_setopt($curl, CURLOPT_URL, "http://vladivostok.farpost.ru/".$URL."?ajax=1");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    $Page = curl_exec($curl);
    curl_close($curl);

 } 
return $Page;
}

//Берет первый найденный телефон
function ExtractPhone($Page)
{
return GetBetween($Page,'<span class="phone">','</span>');
}

//Ссылки на картинки обявы в массиве
function GetImagesUrls($url)
{
 if( $curl = curl_init() ) 
 {
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    $Page = curl_exec($curl);
 
    $temp="temp";	
    $imgs=array();
    $Page=GetBetween($Page,'<div class="bulletinImages">','<div class="items">');
    while($temp!='')
    {
    $temp=GetBetween($Page,'<img src="','" data-zoom-image="');
    if($temp=="") break;
    $Page=substr($Page, strpos($Page,$temp)+strlen($temp)); 
    $imgs[]= $temp;  
    }

 } 
return $imgs;
}
var_dump(ExtractPhone(GetContacts("http://vladivostok.farpost.ru/samye-shikarnye-limuziny-na-dalnem-vostoke-infiniti-chrysler-vipavto-20112047.html")));
var_dump(GetImagesUrls("http://vladivostok.farpost.ru/samye-shikarnye-limuziny-na-dalnem-vostoke-infiniti-chrysler-vipavto-20112047.html"));

?>
