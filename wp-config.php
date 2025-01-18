<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'mo' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',         'n5X;Pk(S[2*]QkDDZ|dM1~C]I{4v&j{|P)6X<mT{Ab%h?,Cr0GN<:ufm(9*|c_jQ' );
define( 'SECURE_AUTH_KEY',  '@,*4{9{jqx?X)8XquC)c631S$%;%N@:qut!~vSm<N9N.mLpJCbfI-u|FG6Q,*$sF' );
define( 'LOGGED_IN_KEY',    'ZNkXi({1aJ]fSJQWG))/UUP;E(u/Tw93CMfIAkG:L:dbL+bXGADnV!J*T4`..e.3' );
define( 'NONCE_KEY',        'oEYsRiOE(oD.t02odS}N2KML7lh:nR&A1yCd~OGw;[9:#pKX,)<R=(w?V|j=KJ#P' );
define( 'AUTH_SALT',        '&Kg35>pmsNWz-5{3iEf/B_@l6gR$B*t{0FX!;eRg*ESeE:zk*.G&;kTQgi5FgP!9' );
define( 'SECURE_AUTH_SALT', 'f4WC2a$U EMXqEqawUc.-^+OV!{>O<fUFk[h&3Upg7O_.0L+R-WJ9Xw:FB_{ZuLT' );
define( 'LOGGED_IN_SALT',   ')VxR>E8hF;>Ppkw}Ky;Dbr|}547Vwjd9f9$C{d`$&dh*x Qc=KPwOFxQgD<<MZ$E' );
define( 'NONCE_SALT',       'V)^M~Z8UWta4.9}O&8u3Hp>i{$Z&cpY@:cV67iL{3?J~,rrK]?~cn9IF(ydV)A3q' );

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
