<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\Product;

interface SalesIncrementerInterface {

	/**
	 * @param array<string, array<string, int>> $sales
	 * @param int                               $quantity
	 * @param string                            $date
	 * @return array<string, array<string, int>>
	 */
	public function increment( array $sales, int $quantity = 1, string $date = 'TODAY' ): array;
}
