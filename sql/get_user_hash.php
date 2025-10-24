<?php

$query = '
SELECT "Password"
	FROM public."User"
    WHERE
        "Username" = :username
    LIMIT 1;
';