<?php

/**
 * транслитерация по ГОСТ 7.79-2000 http://transliteration.ru/gost-7-79-2000/
 * @param string $string
 * @param int $params
 * параметры транслитерации
 * TRANSLIT_I_MODE2 - использовать второй вариант транслитерации для буквы "и" в "i'"
 * TRANSLIT_С_ONLY - использовать только транслитерацию буквы "ц" в "c"
 * TRANSLIT_CZ_ONLY - использовать только транслитерацию буквы "ц" в "cz"
 * по умолчанию используется рекомендованый вариант транслитерации "ц" в "c" перед буквами I, Е, Y, J,
 * в остальных случаях - "cz"
 * @return string
 */
class Translit {

    const TRANSLIT_I_VARIANT2 = 1;
    const TRANSLIT_C_ONLY     = 2;
    const TRANSLIT_CZ_ONLY    = 4;

    public static function transliterate($string, $params = 0) {

        // обычные буквы (односимвольные транслитерации)

        $cyr = 'абвгдезйклмнопрстуфхьАБВГДЕЗЙКЛМНОПРСТУФХЬ';
        $lat = 'abvgdezjklmnoprstufx`ABVGDEZJKLMNOPRSTUFX`';

        $result = strtr($string, $cyr, $lat);

        // многобуквенные транслитерации (кроме Ц)

        $cyr2lat = array(
            'ё' => 'yo', 'Ё' => 'Yo',
            'ж' => 'zh', 'Ж' => 'Zh',
            'ч' => 'ch', 'Ч' => 'Ch',
            'ш' => 'sh', 'Ш' => 'Sh',
            'щ' => 'shh', 'Щ' => 'Shh',
            'ъ' => '``', 'Ъ' => '``',
            'ы' => 'y\'', 'Ы' => 'Y\'',
            'э' => 'e`', 'Э' => 'E`',
            'ю' => 'yu', 'Ю' => 'Yu',
            'я' => 'ya', 'Я' => 'Ya',
            'и' => $params & self::TRANSLIT_I_VARIANT2 ? 'i\'' : 'i',
            'И' => $params & self::TRANSLIT_I_VARIANT2 ? 'И\'' : 'И',
        );

        $result = strtr($result, $cyr2lat);

        // поведение для Ц

        if (($params & self::TRANSLIT_C_ONLY) && ($params & self::TRANSLIT_CZ_ONLY)) {
            trigger_error('Using both TRANSLIT_C_ONLY and TRANSLIT_CZ_ONLY params. Using rule TRANSLIT_C_ONLY.', E_NOTICE);
            $result = strtr($result, 'цЦ', 'cC');
        }
        elseif ($params & self::TRANSLIT_C_ONLY) {
            $result = strtr($result, 'цЦ', 'cC');
        }
        elseif ($params & self::TRANSLIT_CZ_ONLY) {
            $result = strtr($result, array('ц' => 'cz', 'Ц' => 'Cz',));
        }
        else {
            // используем рекомендованые правила
            $result = strtr($result, array(
                'цi' => 'ci', 'Цi' => 'Ci',
                'цe' => 'ce', 'Цe' => 'Ce',
                'цy' => 'cy', 'Цy' => 'Cy',
                'цj' => 'cj', 'Цj' => 'Cj',
                'ц'  => 'cz', 'Ц'  => 'Cz'
            ));
        }

        return $result;
    }

}
