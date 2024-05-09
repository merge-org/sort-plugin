<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Service\Product;

use DateTime;
use MergeOrg\Sort\Core\Constants;

final class SalesIncrementer {

	/**
	 * @param array<string, array<string, int>> $sales
	 * @param int                               $quantity
	 * @param string                            $date
	 * @return array<string, array<string, int>>
	 */
	public function increment( array $sales, int $quantity = 1, string $date = 'TODAY' ): array {
		if ( $date === 'TODAY' ) {
			$date = date( 'Y-m-d' );
		}

		if ( ! $this->dateIsInvalid( $date ) ) {
			$sales[ $date ] = $sales[ $date ] ?? array();

			$sales[ $date ][ Constants::SALES_PURCHASE_KEY ] = $sales[ $date ][ Constants::SALES_PURCHASE_KEY ] ?? 0;
			$sales[ $date ][ Constants::SALES_QUANTITY_KEY ] = $sales[ $date ][ Constants::SALES_QUANTITY_KEY ] ?? 0;

			$sales[ $date ][ Constants::SALES_PURCHASE_KEY ] += 1;
			$sales[ $date ][ Constants::SALES_QUANTITY_KEY ] += $quantity;
		}

		return $this->normalizeSales( $sales );
	}

	/**
	 * @param string $date
	 * @return bool
	 */
	private function dateIsInvalid( string $date ): bool {
		$today      = date( 'Y-m-d' );
		$maxDaysAgo = date( 'Y-m-d', strtotime( '-365 days' ) );

		return ! ( DateTime::createFromFormat( 'Y-m-d', $date ) && $date >= $maxDaysAgo && $date <= $today );
	}

	/**
	 * @param array<string, array<string, int>> $sales
	 * @return array<string, array<string, int>>
	 */
	private function normalizeSales( array $sales ): array {
		$sales_ = array();
		foreach ( $sales as $date => $sale ) {
			if ( ! $this->dateIsInvalid( $date ) ) {
				$sales_[ $date ] = array(
					Constants::SALES_PURCHASE_KEY => $sale[ Constants::SALES_PURCHASE_KEY ],
					Constants::SALES_QUANTITY_KEY => $sale[ Constants::SALES_QUANTITY_KEY ],
				);
			}
		}

		krsort( $sales_, SORT_STRING );

		return $sales_;
	}
}
