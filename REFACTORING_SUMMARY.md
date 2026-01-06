# Multi-Character & Multi-Tenancy Refactoring - Zusammenfassung

## ✅ Was wurde umgesetzt

### 1. Datenbank-Schema (8 Migrations)

#### Neue Tabellen:
- `states` - State-Verwaltung (IDs: 1234, 5678, etc.)
- `alliances` - Allianzen mit state + alliance_name + alliance_tag
- `characters` - Charaktere mit user_id, player_id, state, alliance_id, status
- `character_alliance_history` - Historie der Allianz-Wechsel
- `character_invitations` - Einladungs-System für neue Charaktere

#### Umbenennungen:
- `player_profiles` → `character_stats` (Statistik-Historie)
- `puzzles_user_puzzle_pieces` → `puzzles_character_puzzle_pieces`

#### Anpassungen:
- `users` Tabelle:
  - NEU: `username` (unique, änderbar)
  - ENTFERNT: `player_id`, `player_name`, `display_name`, `is_virtual`, `invited_by`, `token`
  - BEHALTEN: `email` (nullable), `password`, `status`, `locale`, `last_login_at`

---

### 2. Models (8 Models erstellt/angepasst)

#### Neue Models:
- **State** - State-Management
- **Alliance** - Allianzen-Verwaltung
- **Character** - Spieler-Charakter (mit Spatie Permissions!)
- **CharacterAllianceHistory** - Allianz-Historie
- **CharacterInvitation** - Einladungen

#### Umbenannte Models:
- `PlayerProfile` → **CharacterStats**
- `PlayerInfo` → **CharacterStatsObject**

#### Angepasste Models:
- **User**:
  - `characters()` Relation
  - `activeCharacter()` Method (lädt aus Session)
  - Proxy-Methoden für Spatie Permissions (delegiert an activeCharacter)
  - `hasRole()`, `hasPermissionTo()`, etc.

---

### 3. Security & Permissions

#### BelongsToAlliance Trait:
- Automatische Filterung nach State + Alliance
- God-Mode für Developer (session('god_mode_enabled'))
- Global Scope für sichere Datentrennung

#### Policies:
- **CharacterPolicy** - Umfassende Berechtigungsprüfung für Charaktere
  - view, update, delete, lock, kick, invite
  - Row-Level-Security (prüft State + Alliance)
- **AlliancePolicy** - Allianz-Berechtigungen

#### Rollen-System erweitert:
```php
'user'    => weight 0  - Basis-Berechtigungen
'wos_r4'  => weight 40 - Alliance Management (erbt von user)
'wos_r5'  => weight 50 - Alliance Leader (erbt von wos_r4)
'developer' => weight 100 - Alle Berechtigungen
```

#### Permissions:
- **user**: dashboard.view, puzzles (own), profile
- **wos_r4**: alliance.members (invite, kick, lock, unlock)
- **wos_r5**: alliance.edit, members.promote.r4, members.demote.r4, role.transfer.r5
- **developer**: * (alles)

---

### 4. Übersetzungen

Erstellt für DE, EN, TR:
- `lang/de/roles.php`
- `lang/en/roles.php`
- `lang/tr/roles.php`

Rollen-Namen übersetzbar:
- user → Mitglied / Member / Üye
- wos_r4 → R4 - Management
- wos_r5 → R5 - Allianzführer / Alliance Leader / İttifak Lideri
- developer → Entwickler / Developer / Geliştirici

---

### 5. Services & Jobs angepasst

- **WhiteoutSurvivalApiService**:
  - `PlayerProfile` → `CharacterStats`
  - `PlayerInfo` → `CharacterStatsObject`

- **SyncPlayerProfilesJob** → **SyncCharacterStatsJob** (umbenannt)

---

## 🔧 Was noch zu tun ist

### 1. Controller anpassen/erstellen

#### Auth-Controller:
- [app/Http/Controllers/Auth/LoginController.php](app/Http/Controllers/Auth/LoginController.php)
  - Login mit `username` statt `player_id`
  - Nach Login: Character-Auswahl zeigen (wenn >1 Character)
  - `active_character_id` in Session setzen

- [app/Http/Controllers/Auth/RegisterController.php](app/Http/Controllers/Auth/RegisterController.php)
  - Anpassung für CharacterInvitation-Token
  - Überprüfung: Ist User eingeloggt? → Charakter hinzufügen oder neuen Account erstellen

#### Neue Controller:
- **CharacterSwitchController** - Character-Wechsel
- **CharacterInvitationController** - Einladungen verwalten (accept, decline)

---

### 2. Middleware erstellen

#### CharacterRequired Middleware:
```php
// Prüft ob active_character_id in Session
// Falls nicht → Redirect zu Character-Auswahl
Route::middleware(['auth', 'character.required'])->group(...)
```

#### ValidateAllianceContext Middleware:
```php
// Validiert bei Update/Delete Requests ob Resource zur Allianz gehört
// Verhindert Request-Manipulation
```

---

### 3. Puzzles Modul Refactoring

#### Models:
- [Modules/Puzzles/app/Models/PuzzlesUserPuzzlePiece.php](Modules/Puzzles/app/Models/PuzzlesUserPuzzlePiece.php)
  - Umbenennen zu `PuzzlesCharacterPuzzlePiece`
  - `user_id` → `character_id`
  - Relation anpassen: `belongsTo(Character::class)`

#### Controller:
- Alle Queries umstellen:
  - `auth()->id()` → `auth()->user()->activeCharacter->id`
  - Matches nur innerhalb eigener Allianz anzeigen

#### Views:
- Wenn Character ohne Allianz → Custom-Text anzeigen
- "Keine Tauschpartner, da keine Allianz" Banner

---

### 4. Views / UI Components

#### Character-Switch Modal:
- Anzeige gruppiert nach State (Spoiler/Accordion)
- Character-Cards mit Avatar, Name, Ofen-Level, Rolle
- Aktiver Character hervorgehoben

#### Einladungs-System:
- Sticky Banner im Dashboard (nur wenn Einladungen vorhanden)
- Notifications-Dropdown
- Accept/Decline Buttons

#### God-Mode Toggle (Developer):
```
[Header]
  🔧 [God Mode: OFF]  ← Toggle
```

#### Profil-Einstellungen:
- Username ändern (mit Uniqueness-Check)
- Character/Account löschen Modal
  - Radio: Einzelner Character / Ganzer Account
  - Passwort-Bestätigung

#### Sprach-Auswahl (Login/Register):
```
Language: [🇩🇪 DE ▼] [🇬🇧 EN] [🇹🇷 TR]
```

---

### 5. Test-Daten Seeder

Erstelle Seeder mit:
- 3 States (1234, 5678, 9999)
- 5 Allianzen
- 10 Users
- 15 Characters (manche mit mehreren Characters pro User)
- Rollen zuweisen (R5, R4, User, Developer)

---

### 6. Migrations ausführen

```bash
# WICHTIG: Da nicht Live, am besten Fresh Start
php artisan migrate:fresh --seed

# Falls Fehler:
# - composer dump-autoload
# - php artisan config:clear
# - php artisan cache:clear
```

---

## 📋 Nächste Schritte (Empfohlen)

### Priorität 1 - Basis-Funktionalität:
1. ✅ Auth-Controller anpassen (username Login)
2. ✅ Character-Switch Controller + Middleware
3. ✅ Test-Daten Seeder erstellen
4. ✅ Migrations ausführen
5. ✅ Login testen

### Priorität 2 - Einladungssystem:
6. ✅ CharacterInvitationController erstellen
7. ✅ Notification-System einrichten
8. ✅ Register-Flow anpassen (Token-basiert)

### Priorität 3 - UI:
9. ✅ Character-Switch Modal
10. ✅ Profil-Einstellungen erweitern
11. ✅ God-Mode Toggle für Developer

### Priorität 4 - Puzzles:
12. ✅ Puzzles Modul refactoren
13. ✅ Alliance-Filtering testen

---

## ⚠️ Wichtige Hinweise

### Session-Key:
- `active_character_id` - Enthält aktuell aktiven Character

### God-Mode:
- `session('god_mode_enabled')` - Developer sieht alles
- Toggle in Header implementieren

### Permissions:
- Rollen sind am **Character**, nicht am User!
- `auth()->user()->hasRole()` proxied zu `activeCharacter()->hasRole()`

### Allianz-Wechsel:
- Automatisch in `character_alliance_history` speichern
- Alte Rolle wird entfernt
- Neue Allianz: Startet als "user"

### R5 Übertragung:
- Alter R5 wird zu R4 degradiert
- Neuer R5 wird ernannt
- Historie wird gespeichert

---

## 🔍 Wo finde ich was?

### Models:
- [app/Models/User.php](app/Models/User.php) - Account mit Proxy-Methoden
- [app/Models/Character.php](app/Models/Character.php) - Charakter mit Spatie Permissions
- [app/Models/Alliance.php](app/Models/Alliance.php)
- [app/Models/CharacterStats.php](app/Models/CharacterStats.php)
- [app/Models/CharacterInvitation.php](app/Models/CharacterInvitation.php)

### Traits:
- [app/Traits/BelongsToAlliance.php](app/Traits/BelongsToAlliance.php) - Auto-Filtering

### Policies:
- [app/Policies/CharacterPolicy.php](app/Policies/CharacterPolicy.php)
- [app/Policies/AlliancePolicy.php](app/Policies/AlliancePolicy.php)

### Migrations:
- [database/migrations/2026_01_06_020207_create_states_table.php](database/migrations/2026_01_06_020207_create_states_table.php)
- [database/migrations/2026_01_06_020208_create_alliances_table.php](database/migrations/2026_01_06_020208_create_alliances_table.php)
- [database/migrations/2026_01_06_020209_create_characters_table.php](database/migrations/2026_01_06_020209_create_characters_table.php)
- etc.

### Seeder:
- [database/seeders/Permissions/PermissionsSeeder.php](database/seeders/Permissions/PermissionsSeeder.php) - Erweitert mit wos_r4/wos_r5

### Übersetzungen:
- [lang/de/roles.php](lang/de/roles.php)
- [lang/en/roles.php](lang/en/roles.php)
- [lang/tr/roles.php](lang/tr/roles.php)

---

## 🐛 Bekannte Probleme / TODOs

1. **Puzzles Modul** muss noch vollständig refactored werden
2. **Views** müssen erstellt werden (Templates vorhanden)
3. **Test-Daten Seeder** fehlt noch
4. **Character-Selection Flow** nach Login fehlt
5. **Einladungs-Notifications** UI fehlt

---

## 💡 Tipps

### Debugging:
```php
// Active Character prüfen
dd(auth()->user()->activeCharacter());

// God Mode Status
dd(session('god_mode_enabled'));

// Rollen prüfen
dd(auth()->user()->activeCharacter()->roles);
```

### Testing Permissions:
```php
$character = auth()->user()->activeCharacter();
$character->hasRole('wos_r5'); // true/false
$character->can('alliance.edit'); // true/false
```

---

**Stand:** 2026-01-06
**Nächster Schritt:** Auth-Controller anpassen und testen
