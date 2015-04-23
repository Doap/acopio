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
define('DB_NAME', 'wp');

/** MySQL database username */
define('DB_USER', 'acopio');

/** MySQL database password */
define('DB_PASSWORD', 'GoGreenElSalvador9987');

/** MySQL hostname */
define('DB_HOST', 'burkedb.doap.com');

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
define('AUTH_KEY',         '5,J2b]Sr;*j/nCAH#rc0L`8yP|JE>6OuvrYzi)UgJ._2aU<k0F^g[O63~]7Wq?Kc');
define('SECURE_AUTH_KEY',  '{=Kw6:mwqIfU@[!bx4}=!^NJ@:+OJms,=tt#Q7Gh~F>Z7.`Fv5CcMg.Cc^`~VBcR');
define('LOGGED_IN_KEY',    '(9GUdc{DM+$+E3o_!M$/o|0yq7|{2(0C8G@(Et1^(m>}:^0SyH^obx{i1 T*7Et]');
define('NONCE_KEY',        '=gL1J!5T[lj[9SR~:FWgxM;X!;*~B k 8x%05Is16Z:)bJE:%@.-$ `wg#BrE5cD');
define('AUTH_SALT',        '4o*O88[I=cB4YPtr>r>*S;DgJ&Tm19ZC,_DAWwwVzBAxP7E2^t:`q{~{2ktb@lz[');
define('SECURE_AUTH_SALT', 'Dam&4Oi#j)DRU:KJ@I|i6YaZ}IJt%Y@</K9b6UuPb*OiIWxK<Xw`$L.R{9Neg)z&');
define('LOGGED_IN_SALT',   'uRwcsv[!d.A= 9o6N(#}B!+7CKp-fMSQpVz8[ztTN!/dI/X*1NUV7C2p6$h%j]G!');
define('NONCE_SALT',       '/:bLo&Qzrz}y?Ifhd4WxA$L}xYR4mIXzhEL[C#R%aH9..1,ZG4jl21_e  R{.>f;');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

define('WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST']);
define('WP_HOME', 'http://' . $_SERVER['HTTP_HOST']);

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
# Disables all core updates:
define( 'WP_AUTO_UPDATE_CORE', false );
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

