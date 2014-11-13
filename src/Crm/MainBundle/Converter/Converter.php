<?php
namespace Crm\MainBundle\Converter;

class Converter{

    public $char = array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya');

    public $bigChar = array('А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Ё' => 'Yo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch', 'Ы' => 'Y', 'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya');


    public function charRusToEn($rus){


        $rus = str_replace('Е','Ye',$rus);
        $rus = str_replace('oe','oye',$rus);
        $rus = str_replace('ae','aye',$rus);
        $rus = str_replace('яе','yaye',$rus);
        $rus = str_replace('ие','iye',$rus);
        $rus = str_replace('ее','eye',$rus);
        $rus = str_replace('ье','ye',$rus);
        $rus = str_replace('ъе','ye',$rus);

        $rus = $this->mbStringToArray($rus);

        $string = '';
        foreach ($rus as $key => $val){
            if (isset($this->char[$val])){
                $string .= $this->char[$val];
            }else if (isset($this->bigChar[$val])){
                $string .= $this->bigChar[$val];
            }else{
                $string .= $val;
            }
        }

        return $string;
    }

    public function wordRusToEn($rus){
        if (!$rus) return null;
        switch ($rus){
            # Города
            case 'Абакан': return 'Abakan';
            case 'Анадырь': return 'Anadyr';
            case 'Архангельск': return 'Arkhangelsk';
            case 'Астрахань': return 'Astrakhan';
            case 'Барнаул': return 'Barnaul';
            case 'Белгород': return 'Belgorod';
            case 'Благовещенск': return 'Blagoveshchensk';
            case 'Брянск': return 'Bryansk';
            case 'Великий Новгород': return 'Veliky Novgorod';
            case 'Владивосток': return 'Vladivostok';
            case 'Владикавказ': return 'Vladikavkaz';
            case 'Владимир': return 'Vladimir';
            case 'Волгоград': return 'Volgograd';
            case 'Вологда': return 'Vologda';
            case 'Воронеж': return 'Voronezh';
            case 'Грозный': return 'Groznyy';
            case 'Екатеринбург': return 'Yekaterinburg';
            case 'Иваново': return 'Ivanovo';
            case 'Ижевск': return 'Izhevsk';
            case 'Йошкар-Ола': return 'Yoshkar-Ola';
            case 'Иркутск': return 'Irkutsk';
            case 'Казань': return 'Kazan';
            case 'Калининград': return 'Kaliningrad';
            case 'Киров': return 'Kirov';
            case 'Кострома': return 'Kostroma';
            case 'Краснодар': return 'Krasnodar';
            case 'Красноярск': return 'Krasnoyarsk';
            case 'Курган': return 'Kurgan';
            case 'Курск': return 'Kursk';
            case 'Кызыл': return 'Kyzyl';
            case 'Липецк': return 'Lipetsk';
            case 'Магадан': return 'Magadan';
            case 'Майкоп': return 'Maykop';
            case 'Махачкала': return 'Makhachkala';
            case 'Москва': return 'Moscow';
            case 'Мурманск': return 'Murmansk';
            case 'Нальчик': return 'Nalchik';
            case 'Нарьян-Мар': return 'Naryan-Mar';
            case 'Нижний Новгород': return 'Nizhny Novgorod';
            case 'Новосибирск': return 'Novosibirsk';
            case 'Омск': return 'Omsk';
            case 'Орёл': return 'Oryol';
            case 'Оренбург': return 'Orenburg';
            case 'Пенза': return 'Penza';
            case 'Пермь': return 'Perm';
            case 'Петропавловск-Камчатский': return 'Petropavlovsk-Kamchatskiy';
            case 'Псков': return 'Pskov';
            case 'Ростов-на-Дону': return 'Rostov-on-Don';
            case 'Рязань': return 'Ryazan';
            case 'Самара': return 'Samara';
            case 'Санкт-Петербург': return 'St.Petersburg';
            case 'Саранск': return 'Saransk';
            case 'Саратов': return 'Saratov';
            case 'Смоленск': return 'Smolensk';
            case 'Ставрополь': return 'Stavropol';
            case 'Сыктывкар': return 'Syktyvkar';
            case 'Тамбов': return 'Tambov';
            case 'Тверь': return 'Tver';
            case 'Томск': return 'Tomsk';
            case 'Тула': return 'Tula';
            case 'Тюмень': return 'Tyumen';
            case 'Улан-Удэ': return 'Ulan-Ude';
            case 'Ульяновск': return 'Ulyanovsk';
            case 'Уфа': return 'Ufa';
            case 'Хабаровск': return 'Khabarovsk';
            case 'Ханты-Мансийск': return 'Khanty-Mansiysk';
            case 'Чебоксары': return 'Cheboksary';
            case 'Челябинск': return 'Chelyabinsk';
            case 'Черкесск': return 'Cherkessk';
            case 'Чита': return 'Chita';
            case 'Элиста': return 'Elista';
            case 'Южно-Сахалинск': return 'Yuzhno-Sakhalinsk';
            case 'Якутск': return 'Yakutsk';
            case 'Ярославль': return 'Yaroslavl';

            #Районы
            case 'Агаповский': return  'Agapovskiy';
            case 'Азовcкий': return 'Azovskiy';
            case 'Аксайский': return 'Aksayskiy';
            case 'Алапаевский': return 'Alapayevskiy';
            case 'Александрово-Гайский': return 'Alexandrovo-Gayskiy';
            case 'Алексеевский': return 'Alekseyevskiy';
            case 'Андроповский': return 'Andropovskiy';
            case 'Аргаяшский': return 'Argayashskiy';
            case 'Арзамасский': return 'Arzamasskiy';
            case 'Артинский': return 'Artinskiy';
            case 'Ачитский': return 'Achitskiy';
            case 'Базарно-Карабулакский': return 'Bazarno-Karabulakskiy';
            case 'Байкаловский': return 'Baykalovskiy';
            case 'Балтайcкий': return 'Baltayskiy';
            case 'Батыревский': return 'Batyrevskiy';
            case 'Белгородский': return 'Belgorodskiy';
            case 'Большечерниговский': return 'Bolshechernigovskiy';
            case 'Борисовcкий': return 'Borisovskiy';
            case 'Борисоглебский': return 'Borisoglebskiy';
            case 'Брединский': return 'Bredinskiy';
            case 'Брейтовский': return 'Breytovskiy';
            case 'Буйнакский': return 'Buynakskiy';
            case 'Варненский': return 'Varnenskiy';
            case 'Великолукский': return 'Velikolukskiy';
            case 'Вешкаимский': return 'Veshkaimskiy';
            case 'Волгодонский': return 'Volgodonskiy';
            case 'Волоконовский': return 'Volokonovskiy';
            case 'Воскресенский': return 'Voskresenskiy';
            case 'Вяземский': return 'Vyazemskiy';
            case 'Дебесский': return 'Debesskiy';
            case 'Дергачёвский': return 'Dergachyovskiy';
            case 'Долгоруковский': return 'Dolgorukovskiy';
            case 'Дубенский': return 'Dubenskiy';
            case 'Духовнитский': return 'Dukhovnitskiy';
            case 'Екатериновcкий': return 'Yekaterinovskiy';
            case 'Елховский': return 'Yelkhovskiy';
            case 'Еткулский': return 'Yetkulskiy';
            case 'Жарковский': return 'Zharkovskiy';
            case 'Жуковкский': return 'Zhukovskiy';
            case 'Задонский': return 'Zadonskiy';
            case 'Западнодвинский': return 'Zapadnodvinskiy';
            case 'Захаровcкий': return 'Zakharovskiy';
            case 'Ивантеевский': return 'Ivanteyevskiy';
            case 'Ивнявский': return 'Ivnyavskiy';
            case 'Исаклинский': return 'Isaklinskiy';
            case 'Камешковский': return 'Kameshkovskiy';
            case 'Камышлинский': return 'Kamyshlinskiy';
            case 'Кашинский': return 'Kashinskiy';
            case 'Кизилский': return 'Kizilskiy';
            case 'Кимовский': return 'Kimovskiy';
            case 'Кинелский': return 'Kinelskiy';
            case 'Киясовский': return 'Kiyasovskiy';
            case 'Коломенский': return 'Kolomenskiy';
            case 'Кондинский': return 'Kondinskiy';
            case 'Кораблинский': return 'Korablinskiy';
            case 'Крапивинский': return 'Krapivinskiy';
            case 'Красногвардейский': return 'Krasnogvardeyskiy';
            case 'Красноармейский': return 'Krasnoarmeyskiy';
            case 'Красноселькупский': return 'Krasnoselkupskiy';
            case 'Красноуфимский': return 'Krasnoufimskiy';
            case 'Красноярский': return 'Krasnoyarskiy';
            case 'Кунинский': return 'Kuninskiy';
            case 'Лев-Толстовский': return 'Lev-Tolstovskiy';
            case 'Лежневский': return 'Lezhnevskiy';
            case 'Ленинский': return 'Leninskiy';
            case 'Локнянский': return 'Loknyanskiy';
            case 'Лотошинский': return 'Lotoshinskiy';
            case 'Лысогорский': return 'Lysogorskiy';
            case 'Михаиловский': return 'Mikhailovskiy';
            case 'Молоковский': return 'Molokovskiy';
            case 'Мясниковский': return 'Myasnikovskiy';
            case 'Надеждинский': return 'Nadezhdinskiy';
            case 'Нанайский': return 'Nanaiskiy';
            case 'Некоузский': return 'Nekouzskiy';
            case 'Нефтеюганский': return 'Nefteyuganskiy';
            case 'Нижневартовский': return 'Nizhnevartovskiy';
            case 'Новолялинский': return 'Novolyalinskiy';
            case 'Новоорский': return 'Novoorskiy';
            case 'Нязепетровский': return 'Nyazepetrovskiy';
            case 'Облиевский': return 'Obliyevskiy';
            case 'Октябрский': return 'Oktyabrskiy';
            case 'Оленинский': return 'Oleninskiy';
            case 'Омутинский': return 'Omutinskiy';
            case 'Ордынский': return 'Ordynskiy';
            case 'Пестравский': return 'Pestravskiy';
            case 'Подольский': return 'Podolskiy';
            case 'Пожарский': return 'Pozharskiy';
            case 'Похвистневский': return 'Pokhvistnevskiy';
            case 'Предгорный': return 'Predgornyy';
            case 'Приволжский': return 'Privolzhskiy';
            case 'Приуралский': return 'Priuralskiy';
            case 'Промышленновский': return 'Promyshlennovskiy';
            case 'Пуровский': return 'Purovskiy';
            case 'Пустошкинский': return 'Pustoshkinskiy';
            case 'Пыщугский': return 'Pyschugskiy';
            case 'Ржевский': return 'Rzhevskiy';
            case 'Родниковский': return 'Rodnikovskiy';
            case 'Романовский': return 'Romanovskiy';
            case 'Светлоярский': return 'Svetloyarskiy';
            case 'Серпуховский': return 'Serpukhovskiy';
            case 'Советский': return 'Sovetskiy';
            case 'Сосновский': return 'Sosnovskiy';
            case 'Становлянский': return 'Stanovlyanskiy';
            case 'Сургутский': return 'Surgutskiy';
            case 'Сызранский': return 'Syzranskiy';
            case 'Тазовский': return 'Tazovskiy';
            case 'Талитский': return 'Talitskiy';
            case 'Тисулский': return 'Tisulskiy';
            case 'Томаринский': return 'Tomarinskiy';
            case 'Турковский': return 'Turkovskiy';
            case 'Увельский': return 'Uvelskiy';
            case 'Уиский': return 'Uiskiy';
            case 'Усть-Камчатский': return 'Ust-Kamchatskiy';
            case 'Устянский': return 'Ustyanskiy';
            case 'Ханкаиский': return 'Khankaiskiy';
            case 'Хворостянский': return 'Khvorostyanskiy';
            case 'Челно-Вершинский': return 'Chelno-Vershinskiy';
            case 'Чесменский': return 'Chesmenskiy';
            case 'Шалинский': return 'Shalinskiy';
            case 'Шекснинский': return 'Sheksninskiy';
            case 'Шенталинский': return 'Shentalinskiy';
            case 'Шурышкарский': return 'Shuryshkarskiy';
            case 'Южский': return 'Yuzhskiy';
            case 'Яльчикский': return 'Yalchikiy';
            case 'Ямалский': return 'Yamalskiy';

            #Области
            case 'Московская': return 'Moscow';
            case 'Орловская': return 'Oryol';
            case 'Костромская': return 'Kostroma';
            case 'Вологодская': return 'Vologda';
            case 'Новгородская': return 'Novgorod';
            case 'Свердловская': return 'Sverdlovsk';
            case 'Владимирская': return 'Vladimir';
            case 'Сахалинская': return 'Sakhalin';
            case 'Ярославская': return 'Yaroslavl';
            case 'Астраханская': return 'Astrakhan';
            case 'Нижегородская': return 'Nizhny Novgorod';
            case 'Ленинградская': return 'Leningrad';
            case 'Калининградская': return 'Kaliningrad';

            #Края
            case 'Краснодарский': return 'Krasnodar';
            case 'Ставропольский': return 'Stavropol';
            case 'Пермский': return 'Perm';
            case 'Хабаровский': return 'Khabarovsk';
            case 'Красноярский': return 'Krasnoyarsk';
            case 'Камчатский': return 'Kamchatka';

            #Республики
            case 'Адыгея': return 'Rep.of Adygea';
            case 'Алтай': return 'Rep.of Altai';
            case 'Башкортостан': return 'Rep.of Bashkortostan';
            case 'Бурятия': return 'Rep.of Buryatia';
            case 'Дагестан': return 'Rep.of Daghestan';
            case 'Ингушетия': return 'Rep.of Ingushetia';
            case 'КБР (Кабардино-Балкария)': return 'KBR';
            case 'Калмыкия': return 'Rep.of Kalmykia';
            case 'КЧР (Карачаево-Черкесcия)': return 'KCHR';
            case 'Карелия': return 'Rep.of Karelia';
            case 'Коми': return 'Komi Rep.';
            case 'Марий Эл': return 'Mari El Rep.';
            case 'Мордовия': return 'Rep.of Mordovia';
            case 'Саха (Якутия)': return 'Rep.of Sakha';
            case 'РСО (Северная Осетия)': return 'RSO';
            case 'Татарстан': return 'Rep.of Tatarstan';
            case 'Тыва': return 'Rep.of Tuva';
            case 'Удмуртская': return 'Udmurtian Rep.';
            case 'Хакасия': return 'Rep.of Khakassia';
            case 'Чеченская': return  'Chechen Rep.';
            case 'Чувашская': return  'Chuvash Rep.';

            #Спец Слова'
            case 'город': return '';
            case 'село': return 'co.';
            case 'посёлок': return 'sett.';
            case 'деревня': return 'vil';
            case 'станица': return 'vil';
            case 'область': return 'reg.';
            case 'район': return 'dist';
            case 'Край': return 'ter.';
            case 'Республика': return 'Rep.';
            case 'проспект': return 'ave';
            case 'переулок': return 'I';
            case 'бульвар': return 'вlvd';
            case 'улица': return 'st.';
            case 'проезд': return 'p.w.';
            case 'шоссе': return 'hwy';
            case 'строение': return  'bdg';
            case 'площадь': return 'sq.';
            case 'квартал': return 'bl.';
            case 'корпус': return 'bl.';
            case 'офис': return 'of';
            case 'набережная': return 'emb';
            case 'микрорайон': return 'microdist';
            case 'аллея': return 'alleya';
            case 'тупик': return 'tupik';
            #Спец Слова'
            case 'Город': return '';
            case 'Село': return 'co.';
            case 'Посёлок': return 'sett.';
            case 'Деревня': return 'vil';
            case 'Станица': return 'vil';
            case 'Область': return 'reg.';
            case 'Район': return 'dist';
            case 'Проспект': return 'ave';
            case 'Переулок': return 'I';
            case 'Бульвар': return 'вlvd';
            case 'Улица': return 'st.';
            case 'Проезд': return 'p.w.';
            case 'Шоссе': return 'hwy';
            case 'Строение': return  'bdg';
            case 'Площадь': return 'sq.';
            case 'Квартал': return 'bl.';
            case 'Корпус': return 'bl.';
            case 'Офис': return 'of';
            case 'Набережная': return 'emb';
            case 'Микрорайон': return 'microdist';
            case 'Аллея': return 'alleya';
            case 'Тупик': return 'tupik';


            default : return $this->charRusToEn($rus);
        }
    }


    function mbStringToArray ($string) {
        $strlen = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string,0,1,"UTF-8");
            $string = mb_substr($string,1,$strlen,"UTF-8");
            $strlen = mb_strlen($string);
        }
        return $array;
    }
}