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
define( 'DB_NAME', 'i8696650_wp1' );

/** Database username */
define( 'DB_USER', 'i8696650_wp1' );

/** Database password */
define( 'DB_PASSWORD', 'H.kqlq2ygk3QFKMYOY398' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('FORCE_SSL_ADMIN', true);

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
define('AUTH_KEY',         'gpEsYJYrCK5JK8wfnxR5w7kMknIDPFIFvny6SIlkz0vFwzO2L6Gn5IscLF3L4XS7');
define('SECURE_AUTH_KEY',  '7ciQ50ES7MWWkEG9xPHMRqXc6jZupv7CoedCOkrUPeNxcsOStyCtzpenUefJVd38');
define('LOGGED_IN_KEY',    'Z26jgxsdmi034diUJVxX6t2azhKVbTsxRe3ef448OkGMuwYetk6xlU2eysoPoeSP');
define('NONCE_KEY',        'l0N84N5Zqr0Fr8YsuXRz7itnazs1VOP8AoIK8lMbZyAyh7W5wZesOpe461YyCy6v');
define('AUTH_SALT',        '10fIMLzfve3BAzL8jkBuE8IDGf2NN4pYCnfjGpEygFWA1PkqCkcZt8xexK2QZY8w');
define('SECURE_AUTH_SALT', 'OfuaFrxZl4ENQbDDHRIwrMLQF11c2jTeqlzn5u8iZ8oMJvbuZ72MF7SUbyVv2K1n');
define('LOGGED_IN_SALT',   'R92y0x0XFKkN6gT1yRBY6fHALMBDEyKG2QmtQvvurBP4GxWRgpeAGYgItsn79M3M');
define('NONCE_SALT',       'GWH2fjMLwLPPAPBaDYlqsMfVKl6r3CUughu6W81sY4Th3QYB2TV9tLiYXIklarmM');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');
define('FS_CHMOD_DIR',0755);
define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');


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
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
