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



//Логин на фарпосте. Нужно вызывать перед запросом контактных данных. Создает файл cookies.txt
function FarPostLogin($login="Hackaton",$password="EHtvRXABI0",$url='https://vladivostok.farpost.ru/sign?return=%2F')
{
 if( $curl = curl_init() ) 
 { 
    //unlink("cookies.txt");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_COOKIEFILE, "cookies.txt"); 
    curl_setopt($curl, CURLOPT_COOKIEJAR, "cookies.txt");
    curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (X11; Linux x86_64; Edition Linux Mint) Presto/2.12.388 Version/12.16"); 
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "radio=sign&sign=".$login."&password=".$password);
    
    $Page = curl_exec($curl);
    curl_close($curl);
 //   var_dump($Page);
    if(strlen($Page)!=0) return false;
    return true;
 } 
}



//Возвращает страницу с контактными данными обявы
function GetContacts($url)
{
 if( $curl = curl_init() ) 
 {
    
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_COOKIEFILE, "cookies.txt"); 
    curl_setopt($curl, CURLOPT_COOKIEJAR, "cookies.txt");
    curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (X11; Linux x86_64; Edition Linux Mint) Presto/2.12.388 Version/12.16");    
    $Page = curl_exec($curl);
 
    $URL=GetBetween($Page,'class="bigbutton viewAjaxContacts" href="','">');
    
    curl_setopt($curl, CURLOPT_REFERER, $url);
    //curl_setopt($curl,CURLOPT_FOLLOWLOCATION,1);
   
   
    curl_setopt($curl, CURLOPT_URL, "http://vladivostok.farpost.ru/");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    $Page = curl_exec($curl);
    
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
return GetBetween($Page,"phone'>",'</span>');
}

//Отдает массив с телефонами
function ExtractPhones($Page)
{
$phones=array();
    $temp="temp";	
    while($temp!='')
    {
    $temp=ExtractPhone($Page);
    if($temp=="") break;
    $Page=substr($Page, strpos($Page,$temp)+strlen($temp));
    $temp=str_replace(array('(','-',')',' '), "", $temp);
    $temp=str_replace('+7', "8", $temp);
    $phones[]=$temp;
    }
    return $phones;
}



//Берет первый найденный имейл
function ExtractMail($Page)
{
return GetBetween($Page,'mailto:','?');
}

//Отдает массив с имейлами
function ExtractMails($Page)
{
$mailes=array();
    $temp="temp";	
    while($temp!='')
    {
    $temp=ExtractMail($Page);
    if($temp=="") break;
    $Page=substr($Page, strpos($Page,$temp)+strlen($temp));
    $mailes[]=$temp;
    }
    return $mailes;
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

//мега костыль, получает контактные данные инфа 146%
function TryGetContacts($url)
{
if(FarPostLogin())
{

	while(1)
	{	
	$Page=GetContacts($url);
	if(GetBetween($Page,'class="phone">',"span")!='')
		{
			unlink("cookies.txt");
			FarPostLogin();
			FarPostLogin();
			sleep(1);
		}
	else
	break;

	}
	
}
return $Page;
}


	
$Page=TryGetContacts("http://vladivostok.farpost.ru/dostupnoe-stroitelstvo-iz-sendvicha-individualnye-proekty-svai-12446791.html");
var_dump($Page);
var_dump(ExtractPhones($Page));
var_dump(ExtractMails($Page));

//var_dump(GetImagesUrls("http://vladivostok.farpost.ru/samye-shikarnye-limuziny-na-dalnem-vostoke-infiniti-chrysler-vipavto-20112047.html"));

//var_dump(GetPosts("http://vladivostok.farpost.ru/service/celebrate/?page=3"));
//var_dump(GetPosts("http://vladivostok.farpost.ru/service/celebrate/"));

?>
