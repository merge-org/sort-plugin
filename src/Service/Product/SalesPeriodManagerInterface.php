<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\Product;

use MergeOrg\Sort\Data\Product\SalesPeriod;

interface SalesPeriodManagerInterface {

	/**
	 * @param array<string, array<string, int>> $sales
	 * @return SalesPeriod[]
	 */
	public function getAllSalesPeriods( array $sales ): array;
}
