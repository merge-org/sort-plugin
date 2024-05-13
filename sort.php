<?php
declare(strict_types=1);

namespace MergeOrg\Sort;

/*
 * Plugin Name: Sort
 * Plugin URI: https://sort.joinmerge.gr
 * Description: 📊Sort - Sales Order Ranking Tool | Powered by Merge
 * Author: Merge
 * Author URI: https://github.com/merge-org
 * Version: 0.4.7
 * Text Domain: merge-org-sort
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Tested up to: 6.4.3
 * WC requires at least: 7.0
 * WC tested up to: 8.7.0
 */

use MergeOrg\Sort\Core\Namer;
use MergeOrg\Sort\WordPress\Api;

require_once __DIR__ . '/vendor/autoload.php';

$container = new Container();

file_exists( $devBootstrap = __DIR__ . '/dev/dev.php' ) && require_once $devBootstrap;

add_action(
	'init',
	function () use ( $container ) {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return;
		}

		if ( wp_doing_cron() ) {
			if ( time() % 2 === 0 ) {
				return;
			}

			/**
			 * @var Api $api
			 */
			$api = $container->get( Api::class );

			$api->findAndRecordUnrecordedOrders();
			$api->findAndUpdateNonUpdatedProductsSalesPeriod();
		}
	}
);

add_filter(
	'woocommerce_product_data_store_cpt_get_products_query',
	function ( array $query, array $queryVars ) use ( $container ) {
		/**
		 * @var Namer $namer
		 */
		$namer = $container->get( Namer::class );
		if ( $queryVars[ $namer->getNonUpdatedProductsSalesPeriodsDateFilterName() ] ?? false ) {
			$query['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => $namer->getProductSalesPeriodsLastUpdateMetaKey(),
					'value'   => $queryVars[ $namer->getNonUpdatedProductsSalesPeriodsDateFilterName() ],
					'compare' => '<',
					'type'    => 'DATE',
				),
				array(
					'key'     => $namer->getProductSalesPeriodsLastUpdateMetaKey(),
					'value'   => '',
					'compare' => 'NOT EXISTS',
				),
			);
		}

		return $query;
	},
	10,
	2
);
