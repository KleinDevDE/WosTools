<?php

namespace App\Helpers;

class Permissions
{
    /*
     *                 'pages:dashboard:view',
                'puzzles::view', 'puzzles::own.view', 'puzzles::own.manage',
                'puzzles::albums.view', 'puzzles::albums.create', 'puzzles::albums.edit', 'puzzles::albums.delete',
                'puzzles::puzzles.view', 'puzzles::puzzles.create', 'puzzles::puzzles.edit', 'puzzles::puzzles.delete',
                'puzzles::pieces.view', 'puzzles::pieces.create', 'puzzles::pieces.edit', 'puzzles::pieces.delete',
                'users.show', 'users.edit', 'users.delete', 'users.invite'
     */
    public const PAGES_DASHBOARD_VIEW = 'pages:dashboard:view';
    public const PUZZLES_VIEW = 'puzzles::view';
    public const PUZZLES_OWN_VIEW = 'puzzles::own.view';
    public const PUZZLES_OWN_MANAGE = 'puzzles::own.manage';
    public const PUZZLES_ALBUMS_VIEW = 'puzzles::albums.view';
    public const PUZZLES_ALBUMS_CREATE = 'puzzles::albums.create';
    public const PUZZLES_ALBUMS_EDIT = 'puzzles::albums.edit';
    public const PUZZLES_ALBUMS_DELETE = 'puzzles::albums.delete';
    public const PUZZLES_PUZZLES_VIEW = 'puzzles::puzzles.view';
    public const PUZZLES_PUZZLES_CREATE = 'puzzles::puzzles.create';
    public const PUZZLES_PUZZLES_EDIT = 'puzzles::puzzles.edit';
    public const PUZZLES_PUZZLES_DELETE = 'puzzles::puzzles.delete';
    public const PUZZLES_PIECES_VIEW = 'puzzles::pieces.view';
    public const PUZZLES_PIECES_CREATE = 'puzzles::pieces.create';
    public const PUZZLES_PIECES_EDIT = 'puzzles::pieces.edit';
    public const PUZZLES_PIECES_DELETE = 'puzzles::pieces.delete';
    public const USERS_SHOW = 'users.show';
    public const USERS_EDIT = 'users.edit';
    public const USERS_DELETE = 'users.delete';
    public const USERS_LOCK = 'users.lock';
    public const USERS_INVITE = 'users.invite';
}
