<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

interface ApiInterface {

	/**
	 * @param int $maxOrders
	 */
	public function findAndRecordUnrecordedOrders( int $maxOrders = 5 ): void;

	/**
	 * @param int $maxProducts
	 * @return void
	 */
	public function findAndUpdateNonUpdatedProductsSalesPeriod( int $maxProducts = 5 );
}
