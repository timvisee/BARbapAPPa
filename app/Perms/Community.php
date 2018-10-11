<?php

namespace App\Perms;

class CommunityRoles {
    /**
    * A nobody role.
    * Users that aren't signed in get this role.
    */
    const NOBODY = -1;

    /**
    * A normal user role.
    * The default role for signed in users.
    */
    const USER = 0;

    /**
    * The manager role.
    * Includes permissions from `NORMAL`.
    */
    const MANAGER = 10;

    /**
    * The administrator role.
    * Includes permissions from `MANAGER`.
    */
    const ADMIN = 20;
}
