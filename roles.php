<?php
const ROLE_ADMIN = 'admin';
const ROLE_AUTHOR = 'author';
const ROLE_USER = 'user';

function getRoleLabel($role) {
    switch ($role) {
        case ROLE_ADMIN: return 'Administrator';
        case ROLE_AUTHOR: return 'Autor';
        case ROLE_USER: return 'Użytkownik';
        default: return 'Gość';
    }
}
