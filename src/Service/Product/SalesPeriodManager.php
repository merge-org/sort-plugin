<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\Product;

use MergeOrg\Sort\Core\Constants;
use MergeOrg\Sort\Data\Product\SalesPeriod;

final class SalesPeriodManager implements SalesPeriodManagerInterface {

	/**
	 * @param array<string, array<string, int>> $sales
	 * @return SalesPeriod[]
	 */
	public function getAllSalesPeriods( array $sales ): array {
		$salesPeriods = array();
		foreach ( Constants::SALES_PERIODS_IN_DAYS as $days ) {
			$salesPeriods[] = $this->getSalesPeriodFromDays( $sales, $days );
		}

		return $salesPeriods;
	}

	/**
	 * @param array<string, array<string, int>> $sales
	 * @param int                               $days
	 * @return SalesPeriod
	 */
	private function getSalesPeriodFromDays( array $sales, int $days ): SalesPeriod {
		$today              = date( 'Y-m-d' );
		$furthestDateInPast = date( 'Y-m-d', strtotime( "-$days days" ) );

		$purchaseSales = 0;
		$quantitySales = 0;
		foreach ( $sales as $date => $dailySales ) {
			if ( $date < $furthestDateInPast || $date >= $today ) {
				continue;
			}

			$purchaseSales += $dailySales[ Constants::SALES_PURCHASE_KEY ];
			$quantitySales += $dailySales[ Constants::SALES_QUANTITY_KEY ];
		}

		return new SalesPeriod( $days, $purchaseSales, $quantitySales );
	}
}
