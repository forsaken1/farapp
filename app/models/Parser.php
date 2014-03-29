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

    /**
     * Получение данных со страницы отдельной квартиры
     */
    public static function getFlatPost($method, $params = array())
    {
        $html = Farapp::getInstance($method . '.html', $params)->getPars();
        return array(
            'subject' => $html->find('span[data-field=subject]', 0)->innertext,
            'price' => $html->find('span[data-field=price]', 0)->innertext,
            'district' => $html->find('span[data-field=district]', 0)->innertext,
            'street' => $html->find('span[data-field=street-buildingId]', 0)->innertext,
            'flatType' => $html->find('span[data-field=flatType]', 0)->innertext,
            'area' => $html->find('span[data-field=areaTotal]', 0)->innertext,
            'text' => $html->find('p[data-field=text]', 0)->innertext,
        );
    }

    public static function getPosts($method, $params = array())
    {
        $html = Farapp::getInstance($method, $params)->getPars();
        $result = array();
        $i = 1;
        while ( ! is_null($html->find('table.viewdirBulletinTable>tbody.native>tr', $i)))
        {
            $post = $html->find('tbody.native>tr', $i);
            $i++;
            if (is_null($post->find('a.bulletinLink', 0))) continue;

            $result[] = array(
                'key' => (is_null($post->find('a.bulletinLink', 0))) ? null : $post->find('a.bulletinLink', 0)->getAttribute('name'),
                'link' => (is_null($post->find('a.bulletinLink', 0))) ? null : str_replace('.html', '', str_replace('http://vladivostok.farpost.ru/', '', $post->find('a.bulletinLink', 0)->getAttribute('href'))),
                'subject' => (is_null($post->find('a.bulletinLink', 0))) ? null : $post->find('a.bulletinLink', 0)->innertext,
                'price' => (is_null($post->find('div.finalPrice', 0))) ? null : $post->find('div.finalPrice', 0)->innertext,
                'annotation' => (is_null($post->find('div.annotation', 0))) ? null : $post->find('div.annotation', 0)->innertext,
            );
        }

        return $result;
    }
}
