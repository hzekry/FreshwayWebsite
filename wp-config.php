<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'freshway' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'P47upbBs$0`Ux>(Vb3l?#c{{:OS^m%NoNr4Jg[mcs4C{d0`9gcDX9E;ej/_i]L,O' );
define( 'SECURE_AUTH_KEY',  '?_AK#Gw7k86Xrx9rN9^ImK2y*6?.Lqj/{Dc-bM*0{Gm+(?_$rFj,|`6Z@K;1~ZI(' );
define( 'LOGGED_IN_KEY',    'w8Z2;vW#OPISC83rkYa/T=7l0~)D~EJ G9/gJ<VF#nnXjWsVVZz64a81~vq! K}X' );
define( 'NONCE_KEY',        'nGBdq adSjHz,o|Y)V(`dSIh!g=<LfpF5:w97-z$/)H)Qv6s5K1_/+//{!-Sqk27' );
define( 'AUTH_SALT',        'dy1hSWtMFS:c{+NH2*mNr:uHr]{ei MlubqO)b{ri.0_^Qyxw^{ oRvt5r]NaY|A' );
define( 'SECURE_AUTH_SALT', '~N|aSOi~=IjQxroblUU|=)Er&!W8W2kMrkA1MS SAgd<Kasv!A)cbl945WVzF{_Q' );
define( 'LOGGED_IN_SALT',   '`8uZ3!oa-#kAN%+4;>&/WSja3vbEHciSi[Sn8:&W}.37N*-(h!k<)%#q-DogvG ,' );
define( 'NONCE_SALT',       'Ts1GW&{7nQPT8IcZ-G/:n4G=-~2$, 41}T=OTJ4yafU%|u>wRQ^d$K9s&Bg<$v/a' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
