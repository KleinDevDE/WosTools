<?php

return [
    // Page
    'title' => 'Profil Ayarları',
    'description' => 'Kişisel bilgilerinizi ve tercihlerinizi yönetin',

    // Display Name Section
    'display_name_section' => 'Görünen Ad',
    'display_name' => 'Görünen Ad',
    'display_name_placeholder' => 'Görünen adınızı girin',
    'display_name_help' => 'Bu ad diğer kullanıcılara gösterilecek. Kullanıcı adınızı kullanmak için boş bırakın.',

    // Username Section
    'username_section' => 'Hesap Ayarları',
    'username' => 'Kullanıcı Adı',
    'username_placeholder' => 'Kullanıcı adınızı girin',
    'username_help' => 'Bu, giriş kullanıcı adınızdır. Benzersiz olmalıdır.',

    // Password Section
    'password_section' => 'Şifre Değiştir',
    'current_password' => 'Mevcut Şifre',
    'new_password' => 'Yeni Şifre',
    'password_help' => 'Şifrenizi değiştirmek istemiyorsanız her iki alanı da boş bırakın.',

    // Messages
    'updated' => 'Profil başarıyla güncellendi.',
    'password_incorrect' => 'Mevcut şifre yanlış.',

    // Validation Messages
    'validation' => [
        'username_unique' => 'Bu kullanıcı adı zaten alınmış.',
        'username_required' => 'Kullanıcı adı gereklidir.',
        'display_name_max' => 'Görünen ad 255 karakteri geçemez.',
        'current_password_required_with' => 'Şifreyi değiştirmek için mevcut şifre gereklidir.',
        'new_password_required_with' => 'Yeni şifre gereklidir.',
        'new_password_different' => 'Yeni şifre mevcut şifreden farklı olmalıdır.',
        'current_password_size' => 'Geçersiz şifre formatı.',
        'new_password_size' => 'Geçersiz şifre formatı.',
    ],
];
