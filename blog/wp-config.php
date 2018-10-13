<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'live_upgrade');

/** MySQL database username */
define('DB_USER', 'live_upgrade');

/** MySQL database password */
//define('DB_PASSWORD', 'QZDD6bCzw6uqYWtY');
define('DB_PASSWORD', '!=Uh_0FR4yL+p');

/** MySQL hostname */
//define('DB_HOST', '10.16.16.1');
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'm[ywnikX4QQA5nJ*D_tgsjd&S7mco}iY@o~xt]P# 18m`6.o8mq[nKEzu+J11>;y');
define('SECURE_AUTH_KEY',  '/,(vuz{Eksk%ksSW>wH`vG!9U?TE2|lZtsK!3.:BiL-j~0aB{GL6InQR03;0p.1q');
define('LOGGED_IN_KEY',    'e8N|JY;Q50e_XJPn.C@*0LO@sE[^fDj~ 3{zdZS_@p(SntT:@-Wao;-tV6Sslg~r');
define('NONCE_KEY',        '$7V|tLR Y<yKQVT5Y`n,~EIx-PTX4ZGw?WI|mNT=4RmpC4;$C/1qnQPK,K.+t;uk');
define('AUTH_SALT',        '}ltyIZPxw3btvgB,moE+&ku.#Q*&H<+e;rtE2$F}`-qN2p:~Qc96CWelv219EnO ');
define('SECURE_AUTH_SALT', 'D)xA(a%gZ0}hH|]I^<xG^?F4>DUm^V#[Kuul$h!|(j~jB4,d-jVvJvw660@Ny+*l');
define('LOGGED_IN_SALT',   'DM0^;]0/#B}T.-|@caJ%BCg9vX,Ay|-B[K_jWYeGF6|Fn24;H+E OhH[SO`8w(WX');
define('NONCE_SALT',       '.I=8+p87VNBr!CeXi_tc]#xl8V$CiWo8+:Tv&[^qQi8*-=!0>%L5L PW/JJ+2p M');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
 
