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
                'subject' => (is_null($post->find('a.bulletinLink', 0))) ? null : $post->find('a.bulletinLink', 0)->innertext,
                'price' => (is_null($post->find('div.finalPrice', 0))) ? null : $post->find('div.finalPrice', 0)->innertext,
                'annotation' => (is_null($post->find('div.annotation', 0))) ? null : $post->find('div.annotation', 0)->innertext,
            );
        }

        return $result;
    }
}