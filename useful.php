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

//Возвраащет массив с обявами name=>имя, url=>ардес, image=>картинка
function GetPosts($url)
{
 if( $curl = curl_init() ) 
 {
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    $Page = curl_exec($curl);
    $Page2=$Page;
    $temp="temp";	
    $posts=array();
    //"большие блоки"
    while($temp!='')
    {
    $temp=GetBetween($Page,'<div class="image">','<table class="bottom">');
    if($temp=="") break;
    $image=GetBetween($temp,'<img src="','" alt="');
    $URL=GetBetween($temp,'<a href="',' >');
    $name=GetBetween($temp,'<div class="title">','</a>');
    $name=GetBetween($name,$URL.' >','</a>');
    $Page=substr($Page, strpos($Page,$temp)+strlen($temp)); 
    $posts[]= array('name'=>$name,'url'=>$URL,'image'=>$image);  
    }
    $Page=$Page2;
    $temp="temp";
    //обычные блоки
    while($temp!='')
    {
    $temp=GetBetween($Page,'imageCell','<td class="dateCell"');
    if($temp=="") break;
    $image=GetBetween($temp,'<img src="','" alt="');
    $URL=GetBetween($temp,'href="','"');
    $name=GetBetween($temp,'class="bulletinLink','</div>');
    $name=GetBetween($name,$URL.'','</a>');
    $name=str_replace(array('" >','"','>'), "", $name);
    $Page=substr($Page, strpos($Page,$temp)+strlen($temp)); 
    $posts[]= array('name'=>$name,'url'=>$URL,'image'=>$image);  
    }


 } 
return $posts;
}

//var_dump(ExtractPhone(GetContacts("http://vladivostok.farpost.ru/samye-shikarnye-limuziny-na-dalnem-vostoke-infiniti-chrysler-vipavto-20112047.html")));
//var_dump(GetImagesUrls("http://vladivostok.farpost.ru/samye-shikarnye-limuziny-na-dalnem-vostoke-infiniti-chrysler-vipavto-20112047.html"));

var_dump(GetPosts("http://vladivostok.farpost.ru/service/celebrate/?page=3"));
var_dump(GetPosts("http://vladivostok.farpost.ru/service/celebrate/"));
?>
