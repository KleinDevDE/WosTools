<?php

return [
    // Page
    'title' => 'Profil-Einstellungen',
    'description' => 'Verwalte deine persönlichen Informationen und Einstellungen',

    // Display Name Section
    'display_name_section' => 'Anzeigename',
    'display_name' => 'Anzeigename',
    'display_name_placeholder' => 'Gib deinen Anzeigenamen ein',
    'display_name_help' => 'Dieser Name wird anderen Benutzern angezeigt. Lass es leer, um deinen Benutzernamen zu verwenden.',

    // Username Section
    'username_section' => 'Konto-Einstellungen',
    'username' => 'Benutzername',
    'username_placeholder' => 'Gib deinen Benutzernamen ein',
    'username_help' => 'Dies ist dein Login-Benutzername. Er muss eindeutig sein.',

    // Password Section
    'password_section' => 'Passwort ändern',
    'current_password' => 'Aktuelles Passwort',
    'new_password' => 'Neues Passwort',
    'password_help' => 'Lass beide Felder leer, wenn du dein Passwort nicht ändern möchtest.',

    // Messages
    'updated' => 'Profil erfolgreich aktualisiert.',
    'password_incorrect' => 'Aktuelles Passwort ist falsch.',

    // Validation Messages
    'validation' => [
        'username_unique' => 'Dieser Benutzername ist bereits vergeben.',
        'username_required' => 'Benutzername ist erforderlich.',
        'display_name_max' => 'Anzeigename darf nicht länger als 255 Zeichen sein.',
        'current_password_required_with' => 'Aktuelles Passwort ist erforderlich um das Passwort zu ändern.',
        'new_password_required_with' => 'Neues Passwort ist erforderlich.',
        'new_password_different' => 'Neues Passwort muss sich vom aktuellen Passwort unterscheiden.',
        'current_password_size' => 'Ungültiges Passwort-Format.',
        'new_password_size' => 'Ungültiges Passwort-Format.',
    ],
];
