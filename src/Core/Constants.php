<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Core;

final class Constants {

	/**
	 *
	 */
	public const SALES_PURCHASE_KEY = 'purchase';

	/**
	 *
	 */
	public const SALES_QUANTITY_KEY = 'quantity';

	/**
	 *
	 */
	public const SALES_PERIODS_IN_DAYS = array(
		1,
		7,
		15,
		30,
		90,
		180,
		365,
	);
}
