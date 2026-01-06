<?php

namespace App\Helpers;

use App\Models\Character;
use Modules\Puzzles\Models\PuzzlesAlbum;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzle;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzlePiece;
use Modules\Puzzles\Models\PuzzlesUserPuzzlePiece;
use Silber\Bouncer\Bouncer;

class PermissionHelper
{
    public const ROLE_USER = 'user';
    public const ROLE_R4 = 'r4';
    public const ROLE_R5 = 'r5';
    public const ROLE_DEVELOPER = 'developer';

    public static function defaultSetup(Character $character, string $role = self::ROLE_USER)
    {
        $bouncer = app(Bouncer::class);
        $character->disallow()->everything(); //Zero-Trust TODO: Can this work?

        $character->assign($role);

        //Basics
        //TODO Allow updating profile and characters

        //Module: Puzzles
        //TODO This should be assigned to the role, not the users itself
        $bouncer->allow($character)->to('view', PuzzlesAlbum::class);
        $bouncer->allow($character)->to('view', PuzzlesAlbumPuzzle::class);
        $bouncer->allow($character)->to('view', PuzzlesAlbumPuzzlePiece::class);
        $bouncer->allow($character)->to('view', PuzzlesAlbumPuzzle::class);
        $bouncer->allow($character)->toOwn(PuzzlesUserPuzzlePiece::class);
    }

    public static function canAny(string ...$abilities): bool
    {
        $bouncer = app(Bouncer::class);
        return $bouncer->gate()->forUser(Character::getActiveCharacter())->any($abilities);


//        $character = Character::getActiveCharacter();
//        if (!$character) {
//            return false;
//        }
//
//        foreach ($abilities as $ability) {
//            if ($character->can($ability)) {
//                return true;
//            }
//        }
//
//        return false;
    }
}
