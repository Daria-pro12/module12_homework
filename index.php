<?php
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];

foreach ($example_persons_array as $key) {
    $fullname = $key['fullname'];
    $partsFromFullname = getPartsFromFullname($fullname);
    echo "<pre>";
    print_r($partsFromFullname);
    $fullnameFromParts = getFullnameFromParts($partsFromFullname["surname"], $partsFromFullname["name"], $partsFromFullname["patronymic"]);
    echo 'Полное имя: ' . $fullnameFromParts . '<br>';
    echo 'Сокращенное имя: ' . getShortName($fullname) . '.<br>';
    echo 'Пол: ' . getGenderFromName($fullname) . '<br><br><br>';
};

// Функция для получения частей ФИО
function getPartsFromFullname($fullname) {
    $keys_persons_array = [
        'surname',
        'name',
        'patronymic',
    ];
    return array_combine($keys_persons_array, explode(' ', $fullname));
};

// Функция для получения полного ФИО из частей
function getFullnameFromParts($surname, $name, $patronymic) {
    return $surname . ' ' . $name . ' ' . $patronymic;
};

// Функция для получения сокращённого имени
function getShortName($fullname) {
    $partsFromFullname = getPartsFromFullname($fullname);
    return $partsFromFullname["name"] . ' ' . mb_substr($partsFromFullname["surname"], 0, 1);
};

// Функция для определения пола по ФИО 
function getGenderFromName($fullname) {
    $partsFromFullname = getPartsFromFullname($fullname);
    $gender = 0;
    // Проверка по отчеству
    if (mb_substr($partsFromFullname['patronymic'], -3) === 'вна') {
        $gender--;
    } elseif (mb_substr($partsFromFullname['patronymic'], -2) === 'ич') {
        $gender++;
    }

    // Проверка по имени
    if (mb_substr($partsFromFullname['name'], -1) === 'а') {
        $gender--;
    } elseif (mb_substr($partsFromFullname['name'], -1) === 'й' || mb_substr($partsFromFullname['name'], -1) === 'н') {
        $gender++;
    }

    // Проверка по фамилии
    if (mb_substr($partsFromFullname['surname'], -2) === 'ва') {
        $gender--;
    } elseif (mb_substr($partsFromFullname['surname'], -1) === 'в') {
        $gender++;
    }

    if ($gender > 0) {
        return 1;
    } else if ($gender < 0) {
        return -1;
    } else {
        return 0;
    }
};

// Функция для определения полового состава аудитории
function getGenderDescription($persons_array) {
    $total = count($persons_array);
    $men = 0;
    $women = 0;
    $undefined = 0;

    foreach ($persons_array as $key) {
        $gender = getGenderFromName($key['fullname']);
        if ($gender === 1) {
            $men++;
        } elseif ($gender === -1) {
            $women++;
        } else {
            $undefined++;
        }
    }

    $men_percentage = round($men / $total * 100, 1);
    $women_percentage = round($women / $total * 100, 1);
    $undefined_percentage = round($undefined / $total * 100, 1);
    return <<<HEREDOC
    Гендерный состав аудитории:
    ---------------------------
    Мужчины - $men_percentage%
    Женщины - $women_percentage%
    Не удалось определить - $undefined_percentage%
    HEREDOC;
};

echo getGenderDescription($example_persons_array);
echo '<br><br><br>';

// Функция для определения идеального партнера
function getPerfectPartner($surname, $name, $patronymic, $persons_array) {
    // Приводим фамилию, имя и отчество к одному регистру
    $surname = mb_convert_case($surname, MB_CASE_TITLE_SIMPLE);
    $name = mb_convert_case($name, MB_CASE_TITLE_SIMPLE);
    $patronymic = mb_convert_case($patronymic, MB_CASE_TITLE_SIMPLE);

    // Склеиваем ФИО
    $fullName = getFullnameFromParts($surname, $name, $patronymic);

    // Сокращаем ФИО
    $shortName = getShortName($fullName);

    // Определяем пол для ФИО
    $gender = getGenderFromName($fullName);

    do {
        // Случайным образом выбираем человека противоположного пола в массиве
        $randomPerson = $persons_array[array_rand($persons_array)];
        $randomGender = getGenderFromName($randomPerson['fullname']);
    } while ($gender === $randomGender || $randomGender === 0);

    // Генерируем случайный процент совместимости от 50% до 100% с точностью до двух знаков
    $compatibility = round(mt_rand(5000, 10000) / 100, 2);

    // Формируем результат
    $randomPersonName = $randomPerson['fullname'];
    
    // Сокращаем ФИО
    $randomPersonShortName = getShortName($randomPersonName);
  
    echo <<<HEREDOC
    $shortName. + $randomPersonShortName. =
    ♡ Идеально на $compatibility% ♡
    HEREDOC;
};

getPerfectPartner('ИВАНОВ', 'ивАн', 'иванович', $example_persons_array);