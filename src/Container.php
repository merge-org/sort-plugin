<?php
declare(strict_types=1);

namespace MergeOrg\Sort;

use MergeOrg\Sort\Core\Namer;
use MergeOrg\Sort\WordPress\Api;
use MergeOrg\Sort\Service\Product\SalesIncrementer;
use MergeOrg\Sort\Service\Product\SalesPeriodManager;

final class Container {

	/**
	 * @var array<string, mixed>
	 */
	private array $container = array();

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get( string $key ) {
		if ( ! $this->container ) {
			$this->container = array(
				Namer::class              => $namer                           = new Namer(),
				SalesIncrementer::class   => $salesIncrementer     = new SalesIncrementer(),
				SalesPeriodManager::class => $salesPeriodManager = new SalesPeriodManager(),
				Api::class                => new Api( $namer, $salesIncrementer, $salesPeriodManager ),
			);
		}

		return $this->container[ $key ];
	}
}
