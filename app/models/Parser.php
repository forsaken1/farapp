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

    //Возвращает страницу с контактными данными обявы
    public static function GetContacts($url)
    {
     if( $curl = curl_init() ) 
     {
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        $Page = curl_exec($curl);
     
        $URL=self::GetBetween($Page,'class="bigbutton viewAjaxContacts" href="','">');
        
        curl_setopt($curl, CURLOPT_URL, "http://vladivostok.farpost.ru/".$URL."?ajax=1");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        $Page = curl_exec($curl);
        curl_close($curl);

     } 
        return $Page;
    }

    //Берет первый найденный телефон
    public static function ExtractPhone($Page)
    {
    return self::GetBetween($Page,'<span class="phone">','</span>');
    }
}