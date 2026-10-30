<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/auth.php';

demarrer_session();
fermer_session();
rediriger('login.php?deconnecte=1');
