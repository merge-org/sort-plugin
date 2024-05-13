<?php
declare(strict_types=1);

namespace MergeOrg\Sort\WordPress;

use WP_Post;
use WC_Order;
use WC_Product;
use MergeOrg\Sort\Core\NamerInterface;
use MergeOrg\Sort\Data\Product\SalesPeriod;
use MergeOrg\Sort\Service\Product\SalesIncrementerInterface;
use MergeOrg\Sort\Service\Product\SalesPeriodManagerInterface;

final class Api implements ApiInterface {

	/**
	 * @var NamerInterface
	 */
	private NamerInterface $namer;

	/**
	 * @var SalesIncrementerInterface
	 */
	private SalesIncrementerInterface $salesIncrementer;

	/**
	 * @var SalesPeriodManagerInterface
	 */
	private SalesPeriodManagerInterface $salesPeriodManager;

	/**
	 * @param NamerInterface              $namer
	 * @param SalesIncrementerInterface   $salesIncrementer
	 * @param SalesPeriodManagerInterface $salesPeriodManager
	 */
	public function __construct(
		NamerInterface $namer,
		SalesIncrementerInterface $salesIncrementer,
		SalesPeriodManagerInterface $salesPeriodManager
	) {
		$this->namer              = $namer;
		$this->salesIncrementer   = $salesIncrementer;
		$this->salesPeriodManager = $salesPeriodManager;
	}

	/**
	 * @param int $maxOrders
	 */
	public function findAndRecordUnrecordedOrders( int $maxOrders = 5 ): void {
		$isDevelopment = wp_get_environment_type() === 'development';
		$daysInPast    = 1;
		$isDevelopment && ( $daysInPast = 0 );
		$date = date( 'Y-m-d 23:59:59', strtotime( "-$daysInPast days" ) );

		$statuses = array_diff(
			array_keys( wc_get_order_statuses() ),
			array(
				'trash',
				'wc-pending',
				'wc-on-hold',
				'wc-refunded',
				'wc-failed',
				'wc-checkout-draft',
				'wc-cancelled',
			)
		);

		$args = array(
			'type'         => 'shop_order',
			'limit'        => $maxOrders,
			'orderby'      => 'ID',
			'order'        => 'DESC',
			'status'       => $statuses,
			'meta_key'     => $this->namer->getOrderRecordedMetaKey(),
			'meta_compare' => 'NOT EXISTS',
			'date_query'   => array(
				array(
					'before'    => $date,
					'inclusive' => true,
				),
			),
		);

		$orders = wc_get_orders( $args );

		/**
		 * @var WP_Post $order
		 */
		foreach ( $orders as $order ) {
			if ( ! $this->isOrderRecorded( $order->get_id() ) ) {
				$this->setOrderRecorded( $order->get_id() );
				foreach ( $order->get_items() as $item ) {
					$this->setProductSales(
						$productId    = $item->get_data()['product_id'],
						$this->salesIncrementer->increment(
							$this->getProductSales( $productId ),
							$quantity = $item->get_quantity()
						)
					);
					$variationId = $item->get_data()['variation_id'] ?? null;
					if ( $variationId ) {
						$this->setProductSales(
							$variationId,
							$this->salesIncrementer->increment( $this->getProductSales( $variationId ), $quantity )
						);
					}
				}
			}
		}
	}

	/**
	 * @param int $orderId
	 * @return bool|null
	 */
	private function isOrderRecorded( int $orderId ): ?bool {
		if ( ! ( $order = $this->getOrder( $orderId ) ) ) {
			return null;
		}

		return $order->get_meta( $this->namer->getOrderRecordedMetaKey() ) === 'yes';
	}

	/**
	 * @param int $id
	 * @return WC_Order|null
	 */
	private function getOrder( int $id ): ?WC_Order {
		return wc_get_order( $id ) ?: null;
	}

	/**
	 * @param int $orderId
	 * @return void
	 */
	private function setOrderRecorded( int $orderId ): void {
		if ( ! $this->isOrderRecorded( $orderId ) && ( $order = $this->getOrder( $orderId ) ) ) {
			$order->update_meta_data( $this->namer->getOrderRecordedMetaKey(), 'yes' );
			$order->save();
		}
	}

	/**
	 * @param int                               $productId
	 * @param array<string, array<string, int>> $sales
	 * @return void
	 */
	private function setProductSales( int $productId, array $sales ): void {
		if ( $product = $this->getProduct( $productId ) ) {
			$product->update_meta_data( $this->namer->getProductSalesMetaKey(), $sales );
			$product->save();
		}
	}

	/**
	 * @param int $productId
	 * @return WC_Product|null
	 */
	private function getProduct( int $productId ): ?WC_Product {
		return wc_get_product( $productId ) ?: null;
	}

	/**
	 * @param int $productId
	 * @return array<string, array<string, int>>
	 */
	private function getProductSales( int $productId ): array {
		if ( $product = $this->getProduct( $productId ) ) {
			return $product->get_meta( $this->namer->getProductSalesMetaKey() ) ?: array();
		}

		return array();
	}

	/**
	 * @param int $maxProducts
	 * @return void
	 */
	public function findAndUpdateNonUpdatedProductsSalesPeriod( int $maxProducts = 5 ): void {
		$isDevelopment = wp_get_environment_type() === 'development';
		$daysInPast    = 5;
		$isDevelopment && ( $daysInPast = 1 );
		$isDevelopment && ( $maxProducts = 100 );
		$date = date( 'Y-m-d 23:59:59', strtotime( "-$daysInPast days" ) );

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => $maxProducts,
			'post_status'    => array(
				'publish',
				'draft',
			),
			'orderby'        => 'ID',
			'order'          => 'ASC',
			$this->namer->getNonUpdatedProductsSalesPeriodsDateFilterName() => $date,
		);

		$products = wc_get_products( $args );

		/**
		 * @var WC_Product $product
		 */
		foreach ( $products as $product ) {
			$this->setProductSalesPeriods(
				$product->get_id(),
				$this->salesPeriodManager->getAllSalesPeriods( $this->getProductSales( $product->get_id() ) )
			);
		}
	}

	/**
	 * @param int           $productId
	 * @param SalesPeriod[] $salesPeriods
	 * @return void
	 */
	private function setProductSalesPeriods( int $productId, array $salesPeriods ): void {
		if ( ! $product = $this->getProduct( $productId ) ) {
			return;
		}

		foreach ( $salesPeriods as $salesPeriod ) {
			$product->update_meta_data(
				$this->namer->getProductSalesPeriodPurchaseMetaKey( $salesPeriod->getDays() ),
				$salesPeriod->getPurchaseSales()
			);
			$product->update_meta_data(
				$this->namer->getProductSalesPeriodQuantityMetaKey( $salesPeriod->getDays() ),
				$salesPeriod->getQuantitySales()
			);
		}

		$product->update_meta_data( $this->namer->getProductSalesPeriodsLastUpdateMetaKey(), date( 'Y-m-d H:i:s' ) );
		$product->save();
	}
}
