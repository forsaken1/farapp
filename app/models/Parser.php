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
            $url="http://vladivostok.farpost.ru/".$url.".html";
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
       
        if(self::FarPostLogin())
        {

            while(1)
            {	
                $Page=self::GetContacts($url);
                if(self::GetBetween($Page,'class="phone">',"span")!='')
                {
                    unlink(public_path()."/cookies.txt");
                    self::FarPostLogin();
                    self::FarPostLogin();
                    usleep(200000);
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
        return strip_tags('<a class="email"'.self::GetBetween($Page,'<a class="email"','</a>')."</a>");
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
     * @param $link Ссылка на страницу
     * @param $params Массив параметров
     * @return array Распарсенные параметры
     */
    public static function getFlatPost($link, $params = array())
    {
        $html = Farapp::getInstance($link . '.html', $params)->getPars();
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

    /**
     * Получение данных со страницы отдельных бесплатный объявлений
     * @param $link Ссылка на страницу
     * @param $params Массив параметров
     * @return array Распарсенные параметры
     */
    public static function getFreePost($link, $params = array())
    {
        $html = Farapp::getInstance($link . '.html', $params)->getPars();
        return array(
            'subject' => $html->find('span[data-field=subject]', 0)->innertext,
            'text' => $html->find('p[data-field=text]', 0)->innertext,
        );
    }

    /**
     * Получение данных со страницы списка квартир
     * @param $link Ссылка на страницу
     * @param $max_pages Количество страниц
     * @param $max_posts Лимит возвращаемых записей (0 = все)
     * @param $params Массив параметров
     * @return array Распарсенные параметры
     */
    public static function getPosts($link, $category_id, $max_pages = 1, $max_posts = 0, $params = array())
    {
        $result = array();
        for ($i = 0; $i < $max_pages; $i++)
        {
            if ($max_posts > 0)
            {
                if (count($result) >= $max_posts) break;
            }

            $html = Farapp::getInstance($link . '?page=' . $i, $params)->getPars();
            $j = 1;
            while ( ! is_null($html->find('table.viewdirBulletinTable>tbody.native>tr', $j)))
            {
                $post = $html->find('tbody.native>tr', $j);
                $j++;
                if (is_null($post->find('a.bulletinLink', 0))) continue;

                if ($max_posts > 0)
                {
                    if (count($result) >= $max_posts) break;
                }
                $key = (is_null($post->find('a.bulletinLink', 0))) ? null : $post->find('a.bulletinLink', 0)->getAttribute('name');
                $result[] = array(
                    'key' => $key,
                    'link' => (is_null($post->find('a.bulletinLink', 0))) ? null : str_replace('.html', '', str_replace('http://vladivostok.farpost.ru/', '', $post->find('a.bulletinLink', 0)->getAttribute('href'))),
                    'img' => (is_null($post->find('td[data-bulletin-id=' .  $key. ']>a>img', 0))) ? null : $post->find('td[data-bulletin-id=' .  $key. ']>a>img', 0)->getAttribute('src'),
                    'subject' => (is_null($post->find('a.bulletinLink', 0))) ? null : $post->find('a.bulletinLink', 0)->innertext,
                    'price' => (is_null($post->find('div.finalPrice', 0))) ? null : $post->find('div.finalPrice', 0)->innertext,
                    'annotation' => (is_null($post->find('div.annotation', 0))) ? null : strip_tags($post->find('div.annotation', 0)->innertext),
                    'category_id' => $category_id,
                );
            }
        }

        return $result;
    }



//Возвращает страницу с контактными данными обявы
    public static function  getJobPost($url)
    {
        if( $curl = curl_init() ) 
        {
            $url="http://vladivostok.farpost.ru/".$url.".html";
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (X11; Linux x86_64; Edition Linux Mint) Presto/2.12.388 Version/12.16");     
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            $Page = curl_exec($curl);

            $firm=trim(self::GetBetween($Page,'<span class="inplace" data-field="firmTitle">','</span>'));
            $branch=trim(self::GetBetween($Page,'span class="inplace" data-field="firmBranch">','</span>'));
            $vacancy=trim(self::GetBetween($Page,'<span class="inplace" data-field="type">','</span>'));
            $employment=trim(self::GetBetween($Page,'<span class="inplace" data-field="employment">','</span>'));

            $author=trim(strip_tags(self::GetBetween($Page,'<span class="userNick ">','</a>')));

            $education=trim(self::GetBetween($Page,'<span class="inplace" data-field="education">','</span>'));
            $experience=trim(self::GetBetween($Page,'<span class="inplace" data-field="experience">','</span>'));

            $obligation=strip_tags(trim(self::GetBetween($Page,'<p class="inplace" data-field="jobObligation">','</p>')));
            $description=strip_tags(trim(self::GetBetween($Page,'<p class="inplace" data-field="text">','</p>')));

            $paymentform=(self::GetBetween($Page,'wageMin-wageMax-wageDescription">','</span></div>'));
            $Page=substr($Page, strpos($Page,$paymentform) . strlen($paymentform));
            $payment=(self::GetBetween($Page,'wageMin-wageMax-wageDescription">','</span></div>'));
            curl_close($curl);
            return array(
                'payment'=>$payment,
                'paymentform'=>$paymentform,
                'firm'=>$firm,
                'branch'=>$branch,
                'vacancy'=>$vacancy,
                'employment'=>$employment,
                'obligation'=>$obligation,
                'description'=>$description,
                'education'=>$education,
                'experience'=>$experience,
                'author'=>$author,
            );
        } 
        return false;
    }

    //Парсит продажу автомобилей
    public static function getCarPost($url)
    {
        if( $curl = curl_init() ) 
        {
            $url="http://vladivostok.farpost.ru/".$url.".html";
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (X11; Linux x86_64; Edition Linux Mint) Presto/2.12.388 Version/12.16");     
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            $Page = curl_exec($curl);

            $price=trim(self::GetBetween($Page,'<span class="inplace" data-field="price">','</span>'));
            $model=trim(self::GetBetween($Page,'<span class="inplace" data-field="model">','</span>'));
            $year=trim(self::GetBetween($Page,'<span class="inplace" data-field="year">','</span>'));
            $displacement=trim(self::GetBetween($Page,'<span class="inplace" data-field="displacement">','</span>'));
            $transmission=trim(self::GetBetween($Page,'<span class="inplace" data-field="transmission">','</span>'));
            $drive=trim(self::GetBetween($Page,'<span class="inplace" data-field="drive">','</span>'));
            $fuel=trim(self::GetBetween($Page,'<span class="inplace" data-field="fuel">','</span>'));
            $hasDocuments=trim(self::GetBetween($Page,'<span class="inplace" data-field="hasDocuments">','</span>'));
            $hasRussianMileage=trim(self::GetBetween($Page,'<span class="inplace" data-field="hasRussianMileage">','</span>'));
            $isAfterCrash=trim(self::GetBetween($Page,'<span class="inplace" data-field="isAfterCrash">','</span>'));
            $condition=trim(self::GetBetween($Page,'<span class="inplace" data-field="condition">','</span>'));

            $author=trim(strip_tags(self::GetBetween($Page,'<span class="userNick ">','</a>')));
            $guarantee=trim(strip_tags(self::GetBetween($Page,'<div class="inplace" data-field="delivery">','</div>')));
            $description=trim(strip_tags(self::GetBetween($Page,'<p class="inplace" data-field="text">','</p>')));

            curl_close($curl);
            return array(
                'price'=>$price,
                'model'=>$model,
                'year'=>$year,
                'displacement'=>$displacement,
                'transmission'=>$transmission,
                'drive'=>$drive,
                'fuel'=>$fuel,
                'hasDocuments'=>$hasDocuments,
                'hasRussianMileage'=>$hasRussianMileage,
                'isAfterCrash'=>$isAfterCrash,
                'condition'=>$condition,
                'guarantee'=>$guarantee,
                'description'=>$description,
                'author'=>$author,
            );
        } 
        return false;
    }


//Ссылки на картинки обявы в массиве
public static function GetImagesUrls($url)
{
	$url="http://vladivostok.farpost.ru/".$url.".html";
  if( $curl = curl_init() ) 
  {
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    $Page = curl_exec($curl);
 
    $temp="temp";	
    $imgs=array();
    $Page=self::GetBetween($Page,'<div class="bulletinImages">','<div class="items">');
    while($temp!='')
    {
    $temp=self::GetBetween($Page,'<img src="','" data-zoom-image="');
    if($temp=="") break;
    $Page=substr($Page, strpos($Page,$temp)+strlen($temp)); 
    $imgs[]= $temp;  
    }

 } 
return $imgs;
}

    //Фотографии+телефоны+имелы поста
    public static function getPostInfo($url)
    {
		$Page=self::TryGetContacts($url);
		return array(
		'images'=>self::GetImagesUrls($url),
		'emails'=>self::ExtractMails($Page),
		'phones'=>self::ExtractPhones($Page),
		);
	}
}
