<?php
/**
 * My Plugins: catalog and rules engine tests. No WordPress needed.
 *
 * Run: php tests/my-plugins/run-tests.php
 *
 * @package WPFactory\WPFactory_Cross_Selling
 */

// phpcs:ignoreFile

define( 'ABSPATH', __DIR__ . '/' );

// Minimal WordPress stubs.
$GLOBALS['wpf_test_transients'] = array();
function __( $text, $domain = '' ) { return $text; }
function number_format_i18n( $n, $d = 0 ) { return number_format( $n, $d ); }
function wp_normalize_path( $p ) { return str_replace( '\\', '/', $p ); }
function wp_list_pluck( $list, $field ) { return array_map( function ( $x ) use ( $field ) { return $x[ $field ]; }, $list ); }
function wp_strip_all_tags( $s ) { return strip_tags( $s ); }
function add_action() {}
function get_transient( $k ) { return isset( $GLOBALS['wpf_test_transients'][ $k ] ) ? $GLOBALS['wpf_test_transients'][ $k ] : false; }
function set_transient( $k, $v, $t = 0 ) { $GLOBALS['wpf_test_transients'][ $k ] = $v; return true; }
function delete_transient( $k ) { unset( $GLOBALS['wpf_test_transients'][ $k ] ); return true; }

$src = dirname( __DIR__, 2 ) . '/src/php/my-plugins/';
require $src . 'class-catalog.php';
require $src . 'class-site-state.php';
require $src . 'class-signals.php';
require $src . 'class-rules-engine.php';

use WPFactory\WPFactory_Cross_Selling\My_Plugins\Catalog;
use WPFactory\WPFactory_Cross_Selling\My_Plugins\Site_State;
use WPFactory\WPFactory_Cross_Selling\My_Plugins\Rules_Engine;

$fails = 0;
$total = 0;
function check( $label, $ok, $detail = '' ) {
	global $fails, $total;
	$total++;
	if ( ! $ok ) {
		$fails++;
		echo "FAIL  $label" . ( '' !== $detail ? "\n      $detail" : '' ) . "\n";
	}
}
function ids( $picks ) { return implode( ', ', array_column( $picks, 'id' ) ); }
function state( $installed = array(), $active = array() ) { return new Site_State( $installed, $active ); }
function woo( $extra = array() ) {
	return array_merge( array( 'woo' => true, 'countries' => 1, 'gateways' => 1 ), $extra );
}

// ---------------------------------------------------------------- Catalog data.
Catalog::set_now( strtotime( '2026-11-24 12:00:00 UTC' ) );
$items = Catalog::get_items();
$jobs  = array_keys( Catalog::get_jobs() );
$seen_files = array();
$seen_abbr  = array();
foreach ( $items as $id => $item ) {
	check( "$id: name and desc", '' !== $item['name'] && '' !== $item['desc'] );
	check( "$id: job", in_array( $item['job'], $jobs, true ) || 'ai' === $item['job'], $item['job'] );
	check( "$id: no em or en dash", ! preg_match( '/[\x{2013}\x{2014}]/u', $item['name'] . $item['desc'] ) );
	check( "$id: abbr", 2 === strlen( $item['abbr'] ) && ! isset( $seen_abbr[ $item['abbr'] ] ), $item['abbr'] );
	$seen_abbr[ $item['abbr'] ] = $id;
	check( "$id: has a file to detect", '' !== $item['free_file'] || '' !== $item['pro_file'] );
	check( "$id: release date", '' === $item['release'] ? 'ai-assistant' === $id : (bool) preg_match( '/^20\d\d-\d\d-\d\d$/', $item['release'] ), $item['release'] );
	if ( '' !== $item['wporg'] ) {
		check( "$id: free_file in wp.org folder", 0 === strpos( $item['free_file'], $item['wporg'] . '/' ), $item['free_file'] );
		check( "$id: icon from ps.w.org", 0 === strpos( $item['icon_url'], 'https://ps.w.org/' . $item['wporg'] . '/' ), $item['icon_url'] );
	} else {
		check( "$id: no ps.w.org icon without listing", '' === $item['icon_url'] );
	}
	if ( '' !== $item['pro_url'] ) {
		check( "$id: pro_url host", (bool) preg_match( '#^https://(wpfactory\.com/item/|woobewoo\.com/)[a-z0-9/-]+/$#', $item['pro_url'] ), $item['pro_url'] );
	}
	if ( 'wbw' === $item['vendor'] ) {
		check( "$id: WBW links to woobewoo.com", 0 === strpos( $item['pro_url'], 'https://woobewoo.com/' ) );
	}
	if ( '' !== $item['icon_file'] ) {
		check( "$id: bundled icon exists", is_file( dirname( __DIR__, 2 ) . '/assets/img/icons/' . $item['icon_file'] ) );
	}
	foreach ( array( 'free_file', 'pro_file' ) as $key ) {
		if ( '' === $item[ $key ] ) {
			continue;
		}
		$base = strtolower( basename( $item[ $key ] ) );
		check( "$id: $key basename unique", ! isset( $seen_files[ $base ] ), $base . ' also in ' . ( isset( $seen_files[ $base ] ) ? $seen_files[ $base ] : '' ) );
		$seen_files[ $base ] = $id;
	}
	if ( 'tidy' === $item['job'] ) {
		check( "$id: tidy items don't need WooCommerce", false === $item['store'] );
	}
}
foreach ( array_keys( Rules_Engine::get_rules() ) as $id ) {
	check( "rule $id has a catalog item", isset( $items[ $id ] ) );
}
foreach ( array_merge( Rules_Engine::get_fallback( true ), Rules_Engine::get_fallback( false ) ) as $id ) {
	check( "fallback $id has a catalog item", isset( $items[ $id ] ) );
}
$new_ids = array_keys( array_filter( $items, function ( $i ) { return $i['new']; } ) );
check( 'New tab on 2026-11-24 (6 months)', array( 'catalog-health-scanner' ) === $new_ids, implode( ', ', $new_ids ) );
check( 'new: 6 months minus a day', Catalog::is_new( '2026-05-25' ) );
check( 'not new: 6 months plus a day', ! Catalog::is_new( '2026-05-23' ) );
check( 'not new: future date', ! Catalog::is_new( '2026-12-01' ) );
check( 'not new: no date', ! Catalog::is_new( '' ) );
check( 'WBW Pro-only items', Catalog::is_pro_only( $items['currency-switcher'] ) && Catalog::is_pro_only( $items['product-table'] ) );
check( 'AI Assistant is not offered until it has a listing', ! Catalog::is_available( $items['ai-assistant'] ) );
check( 'AI Product Image Generator is Pro only', Catalog::is_pro_only( $items['ai-image'] ) );

// ---------------------------------------------------------------- Site state.
$st = state(
	array(
		'cost-of-goods-for-woocommerce-main/cost-of-goods-for-woocommerce.php', // GitHub zip folder.
		'woo-filter-pro/woofilter-pro.php',                                     // Pro in another folder.
		'remove-old-slugspermalinks/some-other-main-file.php',                  // wp.org folder, other file.
		'yith-woocommerce-wishlist/init.php',
		'ean-for-woocommerce-pro/ean-for-woocommerce-pro.php',
	),
	array( 'cost-of-goods-for-woocommerce-main/cost-of-goods-for-woocommerce.php', 'yith-woocommerce-wishlist/init.php' )
);
check( 'free found by main file in another folder', $st->is_installed( $items['cost-of-goods'] ) && $st->is_active( $items['cost-of-goods'] ) );
$pf = $st->find( $items['product-filter'] );
check( 'Pro found by main file in another folder', 'woo-filter-pro/woofilter-pro.php' === $pf['pro'] && '' === $pf['free'] && ! $pf['pro_active'] );
check( 'free found by wp.org folder', $st->is_installed( $items['slugs-manager'] ) );
$ean = $st->find( $items['ean'] );
check( 'Pro only install is not counted as free', '' === $ean['free'] && '' !== $ean['pro'] );
check( 'competitor active by folder', $st->any_folder_active( array( 'ti-woocommerce-wishlist', 'yith-woocommerce-wishlist' ) ) );
check( 'competitor installed but inactive is ignored', ! $st->any_folder_active( array( 'woo-filter-pro' ) ) );
check( 'not installed', ! $st->is_installed( $items['eu-vat'] ) );

// ---------------------------------------------------------------- Rules.
// 1. No WooCommerce: utilities only (AI Assistant has no listing yet).
$p = Rules_Engine::pick( array( 'woo' => false ), state() );
check( 'no Woo: utilities', 'slugs-manager, file-renaming, download-plugins' === ids( $p ), ids( $p ) );

// 2. EU store with coupons.
$base = woo( array( 'eu' => true, 'coupons' => true, 'products' => 10 ) );
$p    = Rules_Engine::pick( $base, state() );
check( 'EU + coupons', 'eu-vat, coupons-by-url, helpdesk' === ids( $p ), ids( $p ) );
check( 'EU reason', 'You sell to EU countries' === Rules_Engine::get_reason( $p[0], $base ) );

// 3. Toggle EU off: fallback fills the third card.
$p = Rules_Engine::pick( array_merge( $base, array( 'eu' => false ) ), state() );
check( 'EU off', 'coupons-by-url, helpdesk, cost-of-goods' === ids( $p ), ids( $p ) );
check( 'fallback source', 'fallback' === $p[2]['source'] && 'Works with any WooCommerce store' === Rules_Engine::get_reason( $p[2], $base ) );
check( 'Helpdesk (2019) is not called new', 'Works with any WooCommerce store' === Rules_Engine::get_reason( $p[1], $base ), Rules_Engine::get_reason( $p[1], $base ) );

// 4. Cost of Goods active: Price by Quantity, and Cost of Goods is not offered.
$cog = state( array( 'cost-of-goods-for-woocommerce/cost-of-goods-for-woocommerce.php' ), array( 'cost-of-goods-for-woocommerce/cost-of-goods-for-woocommerce.php' ) );
$p   = Rules_Engine::pick( $base, $cog );
check( 'Cost of Goods active', 'eu-vat, price-by-quantity, helpdesk' === ids( $p ), ids( $p ) );
$p = Rules_Engine::pick( array_merge( $base, array( 'eu' => false, 'coupons' => false ) ), $cog );
check( 'Cost of Goods active, no fallback repeat', ! in_array( 'cost-of-goods', array_column( $p, 'id' ), true ), ids( $p ) );
$cog_inactive = state( array( 'cost-of-goods-for-woocommerce/cost-of-goods-for-woocommerce.php' ), array() );
$p = Rules_Engine::pick( $base, $cog_inactive );
check( 'Cost of Goods installed but inactive: no Price by Quantity', ! in_array( 'price-by-quantity', array_column( $p, 'id' ), true ), ids( $p ) );

// 5. Free shipping with a minimum amount.
$fs = array_merge( $base, array( 'free_ship' => true, 'free_ship_min' => 50.0 ) );
$p  = Rules_Engine::pick( $fs, state() );
check( 'free shipping min', 'eu-vat, free-shipping-bar, helpdesk' === ids( $p ), ids( $p ) );
check( 'free shipping reason', 'You offer free shipping over 50' === Rules_Engine::get_reason( $p[1], $fs ), Rules_Engine::get_reason( $p[1], $fs ) );
$p = Rules_Engine::pick( array_merge( $base, array( 'free_ship' => true, 'free_ship_min' => null ) ), state() );
check( 'free shipping without min: no bar', ! in_array( 'free-shipping-bar', array_column( $p, 'id' ), true ), ids( $p ) );

// 6. Coupons off.
$p = Rules_Engine::pick( array_merge( $base, array( 'coupons' => false ) ), state() );
check( 'coupons off', ! in_array( 'coupons-by-url', array_column( $p, 'id' ), true ), ids( $p ) );

// 7. Product filter.
$big = woo( array( 'products' => 150 ) );
$p   = Rules_Engine::pick( $big, state() );
check( 'big catalog: filter first', 'product-filter' === $p[0]['id'] && 'Your shop has 150 products' === Rules_Engine::get_reason( $p[0], $big ), ids( $p ) );
$husky = state( array( 'woocommerce-products-filter/index.php' ), array( 'woocommerce-products-filter/index.php' ) );
$p     = Rules_Engine::pick( $big, $husky );
check( 'filter plugin active: no filter pick', ! in_array( 'product-filter', array_column( $p, 'id' ), true ), ids( $p ) );
$attr = woo( array( 'products' => 10, 'attributes' => 4 ) );
$p    = Rules_Engine::pick( $attr, state() );
check( 'attributes reason', 'product-filter' === $p[0]['id'] && 'Your products use 4 attributes' === Rules_Engine::get_reason( $p[0], $attr ), ids( $p ) );

// 8. "New from WPFactory" follows the release date, and one New card at most.
Catalog::set_now( strtotime( '2019-11-01 12:00:00 UTC' ) );
$p = Rules_Engine::pick( woo( array( 'products' => 30 ) ), state() );
$h = array_values( array_filter( $p, function ( $x ) { return 'helpdesk' === $x['id']; } ) );
check( 'Helpdesk new in Nov 2019', $h && $h[0]['new'] && 'New from WPFactory' === Rules_Engine::get_reason( $h[0], woo() ), ids( $p ) );
check( 'AI Image is not new before its release date', ! in_array( true, array_column( array_filter( $p, function ( $x ) { return 'ai-image' === $x['id']; } ), 'new' ), true ) );
Catalog::set_now( strtotime( '2026-11-24 12:00:00 UTC' ) );
$select = new ReflectionMethod( Rules_Engine::class, 'select' );
$select->setAccessible( true );
$two_new = $select->invoke( null, array(
	array( 'id' => 'a', 'weight' => 50, 'new' => true, 'job' => 'x', 'source' => 'rule' ),
	array( 'id' => 'b', 'weight' => 40, 'new' => true, 'job' => 'y', 'source' => 'rule' ),
	array( 'id' => 'c', 'weight' => 30, 'new' => false, 'job' => 'z', 'source' => 'rule' ),
), array() );
check( 'max one New card', 'a, c' === ids( $two_new ), ids( $two_new ) );

// 9. Job tabs: no repeat while another option is left.
$p = Rules_Engine::pick( woo( array( 'products' => 60 ) ), state() );
check( 'unique jobs', 'wishlist, ean, helpdesk' === ids( $p ), ids( $p ) );

// 10. Everything installed: no picks.
$all = array();
foreach ( $items as $item ) {
	$all[] = '' !== $item['free_file'] ? $item['free_file'] : $item['pro_file'];
}
$p = Rules_Engine::pick( woo( array( 'eu' => true, 'coupons' => true, 'products' => 500 ) ), state( $all, $all ) );
check( 'all installed: nothing picked', array() === $p, ids( $p ) );

// 11. Store-only plugins never picked without WooCommerce.
$p = Rules_Engine::pick( array( 'woo' => false, 'eu' => true, 'products' => 500 ), state() );
foreach ( $p as $pick ) {
	check( "no Woo: {$pick['id']} is not store-only", false === $items[ $pick['id'] ]['store'] );
}

// 12. Cache and invalidation.
$GLOBALS['wpf_test_transients'] = array();
$first = Rules_Engine::get_picks( state() );
check( 'cache written', false !== get_transient( Rules_Engine::TRANSIENT ) );
check( 'cached picks have reasons', '' !== $first[0]['reason'] );
$installed_now = state( array( 'remove-old-slugspermalinks/remove-old-slugs.php' ) );
$second        = Rules_Engine::get_picks( $installed_now );
check( 'plugin installed after caching is dropped', ! in_array( 'slugs-manager', array_column( $second, 'id' ), true ), ids( $second ) );
Rules_Engine::maybe_clear_on_option( 'woocommerce_currency_pos' );
check( 'unrelated option keeps cache', false !== get_transient( Rules_Engine::TRANSIENT ) );
Rules_Engine::maybe_clear_on_option( 'woocommerce_free_shipping_3_settings' );
check( 'free shipping settings clear cache', false === get_transient( Rules_Engine::TRANSIENT ) );

echo ( 0 === $fails ? "OK  $total checks passed\n" : "$fails of $total checks failed\n" );
exit( $fails ? 1 : 0 );
