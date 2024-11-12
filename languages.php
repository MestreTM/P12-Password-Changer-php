<?php
session_start();

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? 'en';

$strings = [
    'en' => [
        'title' => 'Certificate .p12 Password Changer',
        'change_password' => 'Change Password',
        'select_file' => 'Select .p12 File',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'change' => 'Change',
        'result' => 'Result',
        'language' => 'Language',
    ],
    'pt' => [
        'title' => 'Alterador de Senha para Certificado .p12',
        'change_password' => 'Alterar Senha',
        'select_file' => 'Selecionar Arquivo .p12',
        'current_password' => 'Senha Atual',
        'new_password' => 'Nova Senha',
        'change' => 'Alterar',
        'result' => 'Resultado',
        'language' => 'Idioma',
    ],
    'ru' => [
        'title' => 'Изменение пароля сертификата .p12',
        'change_password' => 'Изменить пароль',
        'select_file' => 'Выберите файл .p12',
        'current_password' => 'Текущий пароль',
        'new_password' => 'Новый пароль',
        'change' => 'Изменить',
        'result' => 'Результат',
        'language' => 'Язык',
    ],
    'zh' => [
        'title' => '证书 .p12 密码更改器',
        'change_password' => '更改密码',
        'select_file' => '选择 .p12 文件',
        'current_password' => '当前密码',
        'new_password' => '新密码',
        'change' => '更改',
        'result' => '结果',
        'language' => '语言',
    ]
];
?>
