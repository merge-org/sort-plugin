<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Core;

interface NamerInterface {

	/**
	 * @return string
	 */
	public function getOrderRecordedMetaKey(): string;

	/**
	 * @return string
	 */
	public function getProductSalesMetaKey(): string;

	/**
	 * @param int $days
	 * @return string
	 */
	public function getProductSalesPeriodPurchaseMetaKey( int $days ): string;

	/**
	 * @param int $days
	 * @return string
	 */
	public function getProductSalesPeriodQuantityMetaKey( int $days ): string;

	/**
	 * @return string
	 */
	public function getProductSalesPeriodsLastUpdateMetaKey(): string;

	/**
	 * @return string
	 */
	public function getNonUpdatedProductsSalesPeriodsDateFilterName(): string;
}
