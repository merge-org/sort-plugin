<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Data\Product;

use JsonSerializable;

final class SalesPeriod implements JsonSerializable {

	/**
	 * @var int
	 */
	private int $days;

	/**
	 * @var int
	 */
	private int $purchaseSales;

	/**
	 * @var int
	 */
	private int $quantitySales;

	/**
	 * @param int $days
	 * @param int $purchaseSales
	 * @param int $quantitySales
	 */
	public function __construct( int $days, int $purchaseSales, int $quantitySales ) {
		$this->days          = $days;
		$this->purchaseSales = $purchaseSales;
		$this->quantitySales = $quantitySales;
	}

	/**
	 * @return array<string, int>
	 */
	public function jsonSerialize(): array {
		return array(
			'days'          => $this->getDays(),
			'purchaseSales' => $this->getPurchaseSales(),
			'quantitySales' => $this->getQuantitySales(),
		);
	}

	/**
	 * @return int
	 */
	public function getDays(): int {
		return $this->days;
	}

	/**
	 * @return int
	 */
	public function getPurchaseSales(): int {
		return $this->purchaseSales;
	}

	/**
	 * @return int
	 */
	public function getQuantitySales(): int {
		return $this->quantitySales;
	}
}
