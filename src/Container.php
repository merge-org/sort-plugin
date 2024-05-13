<?php
declare(strict_types=1);

namespace MergeOrg\Sort;

use MergeOrg\Sort\Core\Namer;
use MergeOrg\Sort\WordPress\Api;
use MergeOrg\Sort\Core\NamerInterface;
use MergeOrg\Sort\WordPress\ApiInterface;
use MergeOrg\Sort\Service\Product\SalesIncrementer;
use MergeOrg\Sort\Service\Product\SalesPeriodManager;
use MergeOrg\Sort\Service\Product\SalesIncrementerInterface;
use MergeOrg\Sort\Service\Product\SalesPeriodManagerInterface;

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
				NamerInterface::class              => $namer                           = new Namer(),
				SalesIncrementerInterface::class   => $salesIncrementer     = new SalesIncrementer(),
				SalesPeriodManagerInterface::class => $salesPeriodManager = new SalesPeriodManager(),
				ApiInterface::class                => new Api( $namer, $salesIncrementer, $salesPeriodManager ),
			);
		}

		return $this->container[ $key ];
	}
}
