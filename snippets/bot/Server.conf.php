<?php
$nameserv_ranks[0]  = "---";
$nameserv_ranks[1]  = "Public";
$nameserv_ranks[2]  = "Registered";
$nameserv_ranks[22] = "h";
$nameserv_ranks[21] = "h";
$nameserv_ranks[23] = "s";
$nameserv_ranks[16] = "a";
$nameserv_ranks[24] = "h";
$nameserv_ranks[8]  = "a";
$nameserv_ranks[15] = "Guest";
$nameserv_ranks[18] = "m";
$nameserv_ranks[19] = "m";
$nameserv_ranks[26] = "b";
$nameserv_ranks[27] = "m";
$nameserv_ranks[28] = "v";
Config::set_parameter_pair(array ('nameserv_ranks', $nameserv_ranks));


Config::set_parameter_pair (array ('connport', 6667) );
Config::set_parameter_pair (array ('connip', '127.0.0.1') );
Config::set_parameter_pair (array ('bindip', '127.0.0.1') );
//Config::set_parameter_pair (array ('linkpassword', 'tankingyouz') );
//Config::set_parameter_pair (array ('bindserver', 'roaroodododraor') );
//Config::set_parameter_pair (array ('bindid', 33) );
//Config::set_parameter_pair (array ('binddescription', "Where anything is possible") );

/* database configuration */
Config::set_parameter_pair (array ('database_engine', "MySQL") );
Config::set_parameter_pair (array ('database_hostname', "127.0.0.1") );
Config::set_parameter_pair (array ('database_username', "joomla_roar") );
Config::set_parameter_pair (array ('database_database', "joomla_roar") );
Config::set_parameter_pair (array ('database_password', "woooooo") );
Config::set_parameter_pair (array ('database_joomla_prefix', "jos_") );
//Config::set_parameter_pair (array ('database_ariel_prefix', "Ariel2_") );
// setup channels to join and monitor


Config::set_parameter_pair(array ('channels',array(
'20s-30s',
'40s-50s',
'lobby',
'aberdeen',
'asian',
'belfast',
'birmingham',
'brighton',
'bristol',
'cafe',
'cambridge',
'cardiff',
'countdown',
'dating',
'dublin',
'east-anglia',
'edinburgh',
'england',
'essex',
'gay',
'geek',
'glasgow',
'hull',
'ireland',
'kent',
'leeds',
'leicester',
'lesbians',
'lgbt',
'liverpool',
'london',
'manchester',
'newcastle',
'northern-ireland',
'norwich',
'nottingham',
'oxford',
'plymouth',
'reading',
'scotland',
'sheffield',
'southampton',
'swansea',
'trivia',
'usa',
'wales',
'sportsbar',
'youngatheart',
'help',
'staff',
'training',
'senior',
'monitoring',
'entertainment',
)));
