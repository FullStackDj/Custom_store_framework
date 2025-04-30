<?php

namespace libraries;

class TextModify {
    protected $translitArr = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'h', 'ґ' => 'g', 'д' => 'd', 'е' => 'e',
        'є' => 'ye', 'ж' => 'zh', 'з' => 'z', 'и' => 'y', 'і' => 'i', 'ї' => 'yi', 'й' => 'y',
        'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'shch', 'ь' => '', 'ю' => 'yu', 'я' => 'ya', ' ' => '-',
    ];

    public function translit($str) {
        $str = mb_strtolower($str);
        $temp_arr = [];

        for ($i = 0; $i < mb_strlen($str); $i++) {
            $temp_arr[] = mb_substr($str, $i, 1);
        }

        $link = '';

        if ($temp_arr) {
            foreach ($temp_arr as $char) {
                if (array_key_exists($char, $this->translitArr)) {
                    $link .= $this->translitArr[$char];
                } else {
                    $link .= $char;
                }
            }
        }

        if ($link) {
            $link = preg_replace('/[^a-z0-9_-]/iu', '', $link);
            $link = preg_replace('/-{2,}/iu', '-', $link);
            $link = preg_replace('/_{2,}/iu', '_', $link);
            $link = preg_replace('/(^[-_]+)|([-_]+$)/iu', '', $link);
        }

        return $link;
    }
}