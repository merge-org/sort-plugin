<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Core;

final class Namer {

	/**
	 * @return string
	 */
	public function getOrderRecordedMetaKey(): string {
		return '_merge-org-sort-recorded';
	}

	/**
	 * @return string
	 */
	public function getProductSalesMetaKey(): string {
		return 'merge-org-sort-sales';
	}

	/**
	 * @param int $days
	 *
	 * @return string
	 */
	public function getProductSalesPeriodPurchaseMetaKey( int $days ): string {
		return "merge-org-sort-sales_period_purchase-$days";
	}

	/**
	 * @param int $days
	 *
	 * @return string
	 */
	public function getProductSalesPeriodQuantityMetaKey( int $days ): string {
		return "merge-org-sort-sales_period_quantity-$days";
	}

	/**
	 * @return string
	 */
	public function getProductSalesPeriodsLastUpdateMetaKey(): string {
		return 'merge-org-sort-sales_periods_last_update';
	}

	/**
	 * @return string
	 */
	public function getNonUpdatedProductsSalesPeriodsDateFilterName(): string {
		return 'non_updated_products_sales_period_date_filter';
	}
}
