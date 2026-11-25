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
function _n( $single, $plural, $n, $domain = '' ) { return 1 === (int) $n ? $single : $plural; }
function esc_html( $s ) { return htmlspecialchars( $s, ENT_QUOTES, 'UTF-8' ); }
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
require $src . 'class-page.php';

use WPFactory\WPFactory_Cross_Selling\My_Plugins\Catalog;
use WPFactory\WPFactory_Cross_Selling\My_Plugins\Site_State;
use WPFactory\WPFactory_Cross_Selling\My_Plugins\Rules_Engine;
use WPFactory\WPFactory_Cross_Selling\My_Plugins\Page;

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
function pick_of( $picks, $id ) {
	$found = array_values( array_filter( $picks, function ( $x ) use ( $id ) { return $id === $x['id']; } ) );
	return $found ? $found[0] : null;
}
$generic = 'Works with any WooCommerce store';

// ---------------------------------------------------------------- Catalog data.
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
	check( "$id: no release or highlight fields", ! isset( $item['release'] ) && ! isset( $item['highlight'] ) );
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
$top_expected = array( 'product-filter', 'download-plugins', 'ean', 'order-min-max-amount', 'cost-of-goods', 'email-verification', 'eu-vat', 'payment-methods', 'min-max-quantity', 'wishlist' );
check( 'Top plugins: 9 WPFactory plus the WBW filter', $top_expected === Catalog::TOP, implode( ', ', Catalog::TOP ) );
check( 'Top plugins: unique ids', count( Catalog::TOP ) === count( array_unique( Catalog::TOP ) ) );
foreach ( Catalog::TOP as $id ) {
	check( "Top plugin $id is in the catalog and can be offered", isset( $items[ $id ] ) && Catalog::is_available( $items[ $id ] ) && true === $items[ $id ]['top'] );
}
$top_ids = array_keys( array_filter( $items, function ( $i ) { return $i['top']; } ) );
check( 'top flag only on TOP ids', 10 === count( $top_ids ) && ! array_diff( $top_ids, Catalog::TOP ), implode( ', ', $top_ids ) );
check( 'Top plugins: 9 by WPFactory, 1 by WBW', 9 === count( array_filter( Catalog::TOP, function ( $id ) use ( $items ) { return 'wpfactory' === $items[ $id ]['vendor']; } ) ) && 'wbw' === $items['product-filter']['vendor'] );
check( 'tab label', 'Top plugins' === Catalog::get_jobs()['top'], Catalog::get_jobs()['top'] );
check( 'tab order', array( 'top', 'sell', 'display', 'rules', 'numbers', 'support', 'tidy', 'manage' ) === $jobs, implode( ',', $jobs ) );
$ordered = array_keys( Page::top_first( $items ) );
check( 'list order: top plugins first, in TOP order', Catalog::TOP === array_slice( $ordered, 0, 10 ) && count( $items ) === count( $ordered ), implode( ', ', array_slice( $ordered, 0, 12 ) ) );
$rest = array_values( array_diff( array_keys( $items ), Catalog::TOP ) );
check( 'list order: the rest keep catalog order', $rest === array_slice( $ordered, 10 ) );
$some = array_intersect_key( $items, array_flip( array( 'msrp', 'wishlist', 'eu-vat', 'popups', 'product-filter' ) ) );
check( 'list order with some top plugins missing (installed)', array( 'product-filter', 'eu-vat', 'wishlist', 'popups', 'msrp' ) === array_keys( Page::top_first( $some ) ), implode( ', ', array_keys( Page::top_first( $some ) ) ) );
$display = array_keys( array_filter( $items, function ( $i ) { return 'display' === $i['job']; } ) );
sort( $display );
check( 'Store display tab: 7 plugins', array( 'add-to-cart-labels', 'currency-switcher', 'product-table', 'product-tabs', 'products-per-page', 'related-categories', 'variation-swatches' ) === $display, implode( ', ', $display ) );
$sell = count( array_filter( $items, function ( $i ) { return 'sell' === $i['job']; } ) );
check( 'Sell more per order: 13 plugins', 13 === $sell, $sell );
check( 'WBW Pro-only items', Catalog::is_pro_only( $items['currency-switcher'] ) && Catalog::is_pro_only( $items['product-table'] ) );
check( 'AI Assistant is not offered until it has a listing', ! Catalog::is_available( $items['ai-assistant'] ) );
check( 'AI Product Image Generator is Pro only', Catalog::is_pro_only( $items['ai-image'] ) );
foreach ( array( 'ai-image', 'sales-bump', 'weight-based-shipping', 'review-replies', 'products-excel', 'store-migration-excel', 'stock-manager-excel', 'content-importer-excel' ) as $id ) {
	$size = @getimagesize( dirname( __DIR__, 2 ) . '/assets/img/icons/' . $items[ $id ]['icon_file'] );
	check( "$id: bundled 128x128 PNG icon", $id . '.png' === $items[ $id ]['icon_file'] && $size && 128 === $size[0] && 128 === $size[1] && 'image/png' === $size['mime'] );
}
check( 'instant-checkout-chatgpt: no live Pro page, so no Get Pro', '' === $items['instant-checkout-chatgpt']['pro_url'] && Catalog::is_available( $items['instant-checkout-chatgpt'] ) && ! Catalog::is_pro_only( $items['instant-checkout-chatgpt'] ) );
check( 'Admin Bar Addition removed', ! isset( $items['admin-bar'] ) );
check( '60 catalog entries (59 in the copy file plus AI Assistant)', 60 === count( $items ), count( $items ) );

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
check( 'EU reason', 'Your store is in the EU' === Rules_Engine::get_reason( $p[0], $base ) );

// 3. Toggle EU off: fallback fills the third card.
$p = Rules_Engine::pick( array_merge( $base, array( 'eu' => false ) ), state() );
check( 'EU off', 'coupons-by-url, helpdesk, cost-of-goods' === ids( $p ), ids( $p ) );
check( 'fallback source', 'fallback' === $p[2]['source'] && 'Works with any WooCommerce store' === Rules_Engine::get_reason( $p[2], $base ) );
check( 'Helpdesk reason', 'Handle support inside WooCommerce' === Rules_Engine::get_reason( $p[1], $base ), Rules_Engine::get_reason( $p[1], $base ) );

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

// 8. Reasons: rule picks use their own reason, rules without one and fallback picks the generic one.
$p = Rules_Engine::pick( woo( array( 'coupons' => true ) ), state() );
$h = pick_of( $p, 'helpdesk' );
check( 'Helpdesk: its own reason', $h && 'Handle support inside WooCommerce' === Rules_Engine::get_reason( $h, woo() ), ids( $p ) );
check( 'AI Image (no reason of its own): generic reason', $generic === Rules_Engine::get_reason( array( 'id' => 'ai-image', 'source' => 'rule' ), woo() ) );
$p  = Rules_Engine::pick( woo(), state() );
$cg = pick_of( $p, 'cost-of-goods' );
check( 'fallback pick: generic reason', $cg && 'fallback' === $cg['source'] && $generic === Rules_Engine::get_reason( $cg, woo() ), ids( $p ) );
check( 'picks carry no highlight flag', ! array_filter( $p, function ( $x ) { return isset( $x['highlight'] ); } ) );
$p = Rules_Engine::pick( woo( array( 'products' => 30 ) ), state( array( 'wish-list-for-woocommerce/wish-list-for-woocommerce.php', 'cost-of-goods-for-woocommerce/cost-of-goods-for-woocommerce.php' ) ) );
check( 'Helpdesk and AI Image can both be picked (no New card limit any more)', null !== pick_of( $p, 'helpdesk' ) && null !== pick_of( $p, 'ai-image' ), ids( $p ) );

// Helpdesk never gets the generic text: not a fallback, and its rule always has a reason.
check( 'Helpdesk is not a fallback pick', ! in_array( 'helpdesk', array_merge( Rules_Engine::get_fallback( true ), Rules_Engine::get_fallback( false ) ), true ) );
foreach ( array( woo(), woo( array( 'eu' => true ) ), woo( array( 'products' => 200, 'coupons' => true ) ), woo( array( 'registration' => true ) ) ) as $n => $sig ) {
	$h = pick_of( Rules_Engine::pick( $sig, state() ), 'helpdesk' );
	if ( $h ) {
		$r = Rules_Engine::get_reason( $h, $sig );
		check( "Helpdesk reason is never generic ($n)", $generic !== $r && 'For your WordPress site' !== $r, $r );
	}
}

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

// ---------------------------------------------------------------- Page helpers.
// 13. My Plugins is moved to the end of the WPFactory submenu.
$menu = array(
	0  => array( 'WPFactory', 'manage_woocommerce', 'wpfactory' ),
	2  => array( 'My Plugins', 'manage_woocommerce', Page::SLUG ),
	3  => array( 'Order Min/Max', 'manage_woocommerce', 'admin.php?page=wc-settings&tab=alg_wc_oma' ),
	30 => array( 'Cost of Goods', 'manage_woocommerce', 'admin.php?page=wc-settings&tab=alg_wc_cog' ),
);
$moved = Page::move_last( $menu, Page::SLUG );
$slugs = array_column( $moved, 2 );
check( 'My Plugins last', Page::SLUG === end( $slugs ) && 4 === count( $moved ), implode( ' | ', $slugs ) );
check( 'other items keep their order', array( 'wpfactory', 'admin.php?page=wc-settings&tab=alg_wc_oma', 'admin.php?page=wc-settings&tab=alg_wc_cog' ) === array_slice( $slugs, 0, 3 ) );
end( $moved );
check( 'My Plugins has the highest key (WordPress prints by array order)', key( $moved ) > 30 );
check( 'no My Plugins entry: unchanged', array_slice( $menu, 0, 1, true ) === Page::move_last( array_slice( $menu, 0, 1, true ), Page::SLUG ) );

// 14. Owned list: active first, inactive last, catalog order inside each group.
$owned_state = state(
	array(
		'ai-product-image-generator/ai-product-image-generator.php',
		'cost-of-goods-for-woocommerce/cost-of-goods-for-woocommerce.php',
		'order-minimum-amount-for-woocommerce/order-minimum-amount-for-woocommerce.php',
		'eu-vat-for-woocommerce/eu-vat-for-woocommerce.php',
	),
	array(
		'cost-of-goods-for-woocommerce/cost-of-goods-for-woocommerce.php',
		'order-minimum-amount-for-woocommerce/order-minimum-amount-for-woocommerce.php',
	)
);
$owned  = array_keys( Page::sort_owned( $items, $owned_state ) );
$active = array_values( array_filter( $owned, function ( $id ) use ( $items, $owned_state ) { return Page::is_owned_active( $items[ $id ], $owned_state ); } ) );
check( 'owned: 4 installed', 4 === count( $owned ), implode( ', ', $owned ) );
check( 'owned: active first', $active === array_slice( $owned, 0, count( $active ) ), implode( ', ', $owned ) );
check( 'owned: inactive last', 2 === count( $active ) && ! Page::is_owned_active( $items[ $owned[2] ], $owned_state ) && ! Page::is_owned_active( $items[ $owned[3] ], $owned_state ), implode( ', ', $owned ) );
$cat_order = array_keys( $items );
check( 'owned: catalog order inside each group', array_search( $owned[2], $cat_order, true ) < array_search( $owned[3], $cat_order, true ) && array_search( $owned[0], $cat_order, true ) < array_search( $owned[1], $cat_order, true ) );
$pro_off = state( array( 'ean-for-woocommerce/ean-for-woocommerce.php', 'ean-for-woocommerce-pro/ean-for-woocommerce-pro.php' ), array( 'ean-for-woocommerce/ean-for-woocommerce.php' ) );
check( 'free active next to an inactive Pro counts as inactive', ! Page::is_owned_active( $items['ean'], $pro_off ) );

// 15. Subline counts installed plugins, active or not.
check( 'subline: plural', "You have 4 WPFactory plugins. Here's what else fits how you sell." === Page::get_subline( count( $owned ) ), Page::get_subline( count( $owned ) ) );
check( 'subline: singular', "You have 1 WPFactory plugin. Here's what else fits how you sell." === Page::get_subline( 1 ) );
check( 'subline: none', "Here's what fits how you sell." === Page::get_subline( 0 ) );

// 16. Sidebar item: grid icon before the label, no dot, CSS scoped to the My Plugins link.
$title = Page::menu_title( 'My Plugins' );
check( 'menu title: 14px grid icon then the label', 0 === strpos( $title, '<svg class="wpf-mp-menu-icon" width="14" height="14"' ) && 'My Plugins' === substr( $title, -10 ), $title );
check( 'menu title: 4 rounded squares in currentColor', 4 === substr_count( $title, '<rect ' ) && 4 === substr_count( $title, 'rx="1.5" fill="currentColor"' ) );
check( 'menu title: icon hidden from screen readers', false !== strpos( $title, 'aria-hidden="true" focusable="false"' ) );
check( 'menu title: no dot or badge', false === strpos( $title, 'dot' ) && false === strpos( $title, 'screen-reader-text' ) && false === strpos( $title, 'count' ) );
check( 'menu title: label escaped', false !== strpos( Page::menu_title( 'A & B' ), 'A &amp; B' ) );
check( 'top bar label stays plain text', 'My Plugins' === trim( strip_tags( $title ) ) );
$css   = Page::menu_css();
$scope = '#adminmenu .wp-submenu a[href$="page=wpfactory-my-plugins"]';
preg_match_all( '/(^|\})([^{]+)\{/', $css, $sel );
$selectors = array_map( 'trim', explode( ',', implode( ',', $sel[2] ) ) );
check( 'menu CSS: every selector is scoped to the My Plugins link', $selectors && ! array_filter( $selectors, function ( $x ) use ( $scope ) { return 0 !== strpos( $x, $scope ); } ), implode( ' | ', $selectors ) );
check( 'menu CSS: tool blue, 600 weight', false !== strpos( $css, $scope . '{position:relative;margin-top:13px;color:#6BBFFF;font-weight:600}' ) );
check( 'menu CSS: white on hover, focus and current', false !== strpos( $css, $scope . ':hover,' . $scope . ':focus,' . $scope . '.current{color:#FFFFFF}' ) );
check( 'menu CSS: 1px divider, rgba(255,255,255,.15), 6px above and below', false !== strpos( $css, 'top:-7px;height:1px;background:rgba(255,255,255,.15)' ) && false !== strpos( $css, 'margin-top:13px' ) );
check( 'menu CSS: 14px icon', false !== strpos( $css, '.wpf-mp-menu-icon{display:inline-block;width:14px;height:14px' ) );

echo ( 0 === $fails ? "OK  $total checks passed\n" : "$fails of $total checks failed\n" );
exit( $fails ? 1 : 0 );
