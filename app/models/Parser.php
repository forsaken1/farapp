<?php
/*
FarPost Parser Example
*/

class Parser
{

    //мега гига функция парсинга
    private static function GetBetween($content,$start,$end){
        $r = explode($start, $content);
        if (isset($r[1])){
            $r = explode($end, $r[1]);
            return $r[0];
        }
        return '';
    }


    //Возвращает массив с парой name=> Название раздела, url=> Ссылка раздела
    public static function GetRazdely($url='http://vladivostok.farpost.ru?ajax=1')
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
        $temp=self::GetBetween($Page,'<a class="l1" href=','</a');
        if($temp=="") break;
        $URL=self::GetBetween($temp,'"','">');
        $name=self::GetBetween($temp,'">','>');
        $Page=substr($Page, strpos($Page,$name)+50); 
        $Razdely[]= array('name'=>$name,'url'=>$URL);  
        }
     } 
    return $Razdely;
    }
    

//Логин на фарпосте. Нужно вызывать перед запросом контактных данных. Создает файл cookies.txt
public static function FarPostLogin($login="Hackaton",$password="EHtvRXABI0",$url='https://vladivostok.farpost.ru/sign?return=%2F')
{
 if( $curl = curl_init() ) 
 {
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_COOKIEFILE, public_path()."/cookies.txt"); 
    curl_setopt($curl, CURLOPT_COOKIEJAR, public_path()."/cookies.txt");
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:11.0) Gecko/20100101 Firefox/11.0"); 
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "radio=sign&sign=".$login."&password=".$password);
    
    $Page = curl_exec($curl);
    curl_close($curl);
    //var_dump($Page);
    if(strlen($Page)!=0) return false;
    return true;
 } 
}


//Возвращает страницу с контактными данными обявы
public static function GetContacts($url)
{
 if( $curl = curl_init() ) 
 {
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_COOKIEFILE, public_path()."/cookies.txt"); 
    curl_setopt($curl, CURLOPT_COOKIEJAR, public_path()."/cookies.txt");
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:11.0) Gecko/20100101 Firefox/11.0");    
    $Page = curl_exec($curl);
 
    $URL=self::GetBetween($Page,'class="bigbutton viewAjaxContacts" href="','">');
    
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

//мега костыль, получает контактные данные инфа 146%
public static function TryGetContacts($url)
{
if(FarPostLogin())
{

	while(1)
	{	
	$Page=self::GetContacts($url);
	if(self::GetBetween($Page,'class="phone">',"span")!='')
		{
			unlink(public_path()."/cookies.txt");
			self::FarPostLogin();
			self::FarPostLogin();
			sleep(1);
		}
	else
	break;

	}
	
}
return $Page;
}


    //Берет первый найденный телефон
    public static function ExtractPhone($Page)
    {
    return self::GetBetween($Page,"phone'>",'</span>');
    }
    
    //Отдает массив с телефонами
   public static function ExtractPhones($Page)
   {
   $phones=array();
    $temp="temp";	
    while($temp!='')
    {
    $temp=self::ExtractPhone($Page);
    if($temp=="") break;
    $Page=substr($Page, strpos($Page,$temp)+strlen($temp));
    $temp=str_replace(array('(','-',')',' '), "", $temp);
    //$temp=str_replace('+7', "8", $temp);
    $phones[]=$temp;
    }
    return $phones;
   }


//Берет первый найденный имейл
public static function ExtractMail($Page)
{
return self::GetBetween($Page,'mailto:','?');
}

//Отдает массив с имейлами
public static function ExtractMails($Page)
{
$mailes=array();
    $temp="temp";	
    while($temp!='')
    {
    $temp=self::ExtractMail($Page);
    if($temp=="") break;
    $Page=substr($Page, strpos($Page,$temp)+strlen($temp));
    $mailes[]=$temp;
    }
    return $mailes;
}

    public static function getFlatPost($method)
    {
        $page = iconv('cp1251', 'UTF8', Farapp::getInstance($method)->getPars());
        return array(
            'title' => self::GetBetween($page, '<span data-field="subject" class="inplace">', '</span>'),
            'price' => self::GetBetween($page, '<span class="inplace" data-field="price">', '</span>'),
            'district' => self::GetBetween($page, '<span class="inplace" data-field="district">', '</span>'),
            'street' => self::GetBetween($page, '<span class="inplace" data-field="street-buildingId">', '</span>'),
            'flatType' => self::GetBetween($page, '<span class="inplace" data-field="flatType">', '</span>'),
            'area' => self::GetBetween($page, '<span class="inplace" data-field="areaTotal">', '</span>'),
            'text' => self::GetBetween($page, '<p class="inplace" data-field="text">', '</p>'),
        );
    }
}
