/**
 * My Plugins page: tabs, search, install and activate.
 * No dependencies. Data comes from wp_localize_script (wpfMyPlugins).
 */
( function () {
	'use strict';

	var root = document.querySelector( '.wpf-myplugins' );
	if ( ! root || ! window.wpfMyPlugins ) {
		return;
	}
	var cfg = window.wpfMyPlugins;
	var t = cfg.i18n;

	// ------------------------------------------------------------------ Tabs and search.
	var tabs = Array.prototype.slice.call( root.querySelectorAll( '.wpf-mp-tabs [role="tab"]' ) );
	var rows = Array.prototype.slice.call( root.querySelectorAll( '.wpf-mp-row' ) );
	var list = root.querySelector( '#wpf-mp-catalog-list' );
	var search = root.querySelector( '.wpf-mp-search input' );
	var empty = root.querySelector( '.wpf-mp-empty' );
	var current = tabs.length ? tabs[ 0 ].getAttribute( 'data-job' ) : '';

	function inTab( row, job ) {
		return 'new' === job ? '' !== ( row.getAttribute( 'data-highlight' ) || '' ) : job === row.getAttribute( 'data-job' );
	}

	function applyFilter() {
		var q = search ? search.value.trim().toLowerCase() : '';
		var visible = [];
		rows.forEach( function ( row ) {
			var show = q ? row.getAttribute( 'data-search' ).indexOf( q ) > -1 : inTab( row, current );
			row.hidden = ! show;
			row.classList.remove( 'is-last' );
			if ( show ) {
				visible.push( row );
			}
		} );
		if ( visible.length ) {
			visible[ visible.length - 1 ].classList.add( 'is-last' );
		}
		if ( empty ) {
			empty.hidden = visible.length > 0;
		}
		// While searching, results come from every tab, so no tab is selected.
		tabs.forEach( function ( tab ) {
			var on = ! q && tab.getAttribute( 'data-job' ) === current;
			tab.classList.toggle( 'on', on );
			tab.setAttribute( 'aria-selected', on ? 'true' : 'false' );
			tab.tabIndex = ( tab.getAttribute( 'data-job' ) === current ) ? 0 : -1;
		} );
		if ( list ) {
			var active = root.querySelector( '#wpf-mp-tab-' + current );
			if ( active ) {
				list.setAttribute( 'aria-labelledby', active.id );
			}
		}
	}

	tabs.forEach( function ( tab, index ) {
		tab.addEventListener( 'click', function () {
			current = tab.getAttribute( 'data-job' );
			if ( search ) {
				search.value = '';
			}
			applyFilter();
		} );
		tab.addEventListener( 'keydown', function ( e ) {
			var next = null;
			if ( 'ArrowRight' === e.key ) {
				next = tabs[ ( index + 1 ) % tabs.length ];
			} else if ( 'ArrowLeft' === e.key ) {
				next = tabs[ ( index - 1 + tabs.length ) % tabs.length ];
			} else if ( 'Home' === e.key ) {
				next = tabs[ 0 ];
			} else if ( 'End' === e.key ) {
				next = tabs[ tabs.length - 1 ];
			}
			if ( next ) {
				e.preventDefault();
				next.focus();
				next.click();
			}
		} );
	} );
	if ( search ) {
		search.addEventListener( 'input', applyFilter );
	}
	if ( tabs.length ) {
		applyFilter();
	}

	// ------------------------------------------------------------------ Install and activate.
	function post( action, id ) {
		var body = new URLSearchParams();
		body.append( 'action', action );
		body.append( 'nonce', cfg.nonce );
		body.append( 'id', id );
		return fetch( cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } )
			.then( function ( res ) {
				return res.json().catch( function () {
					return { success: false, data: {} };
				} );
			} );
	}

	function showError( button, data ) {
		var box = button.closest( '[data-id]' );
		var el = box ? box.querySelector( '.wpf-mp-error' ) : null;
		if ( ! el ) {
			return;
		}
		el.textContent = ( data && data.message ) ? data.message + ' ' : t.error + ' ';
		if ( data && data.wporg_url ) {
			var a = document.createElement( 'a' );
			a.href = data.wporg_url;
			a.target = '_blank';
			a.rel = 'noopener noreferrer';
			a.textContent = t.wporg;
			var sr = document.createElement( 'span' );
			sr.className = 'screen-reader-text';
			sr.textContent = ' ' + t.newTab;
			a.appendChild( sr );
			el.appendChild( a );
		}
		el.hidden = false;
	}

	function clearError( button ) {
		var box = button.closest( '[data-id]' );
		var el = box ? box.querySelector( '.wpf-mp-error' ) : null;
		if ( el ) {
			el.hidden = true;
			el.textContent = '';
		}
	}

	function setBusy( button, label ) {
		button.disabled = true;
		button.setAttribute( 'aria-busy', 'true' );
		button.textContent = label;
	}

	function setIdle( button, label ) {
		button.disabled = false;
		button.removeAttribute( 'aria-busy' );
		button.textContent = label;
	}

	function activated( button, data ) {
		var done = document.createElement( 'span' );
		done.className = 'wpf-mp-done';
		done.setAttribute( 'role', 'status' );
		done.textContent = t.activated;
		var link = document.createElement( 'a' );
		link.className = 'ak-a';
		link.href = ( data && data.settings_url ) ? data.settings_url : 'plugins.php';
		link.textContent = t.settings;
		var wrap = document.createElement( 'span' );
		wrap.className = 'wpf-mp-actions';
		wrap.appendChild( done );
		wrap.appendChild( link );
		button.replaceWith( wrap );
		link.focus();
	}

	function activate( button ) {
		var id = button.getAttribute( 'data-id' );
		clearError( button );
		setBusy( button, t.activating );
		post( 'wpf_myplugins_activate', id ).then( function ( res ) {
			if ( res && res.success ) {
				activated( button, res.data );
			} else {
				setIdle( button, t.activate );
				showError( button, res && res.data );
			}
		} ).catch( function () {
			setIdle( button, t.activate );
			showError( button, null );
		} );
	}

	function install( button ) {
		var id = button.getAttribute( 'data-id' );
		var label = button.textContent;
		clearError( button );
		setBusy( button, t.installing );
		post( 'wpf_myplugins_install', id ).then( function ( res ) {
			if ( res && res.success ) {
				button.classList.remove( 'wpf-mp-install' );
				button.classList.add( 'wpf-mp-activate' );
				setIdle( button, t.activate );
				button.focus();
			} else {
				setIdle( button, label );
				showError( button, res && res.data );
			}
		} ).catch( function () {
			setIdle( button, label );
			showError( button, null );
		} );
	}

	root.addEventListener( 'click', function ( e ) {
		var button = e.target.closest( 'button.wpf-mp-install, button.wpf-mp-activate' );
		if ( ! button || button.disabled ) {
			return;
		}
		if ( button.classList.contains( 'wpf-mp-install' ) ) {
			install( button );
		} else {
			activate( button );
		}
	} );
}() );
