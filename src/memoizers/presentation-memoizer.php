<?php
/**
 * The presentation memoizer.
 *
 * @package Yoast\YoastSEO\Memoizers
 */

namespace Yoast\WP\Free\Memoizer;

use Yoast\WP\Free\Context\Meta_Tags_Context;
use Yoast\WP\Free\Models\Indexable;
use Yoast\WP\Free\Presentations\Indexable_Presentation;
use YoastSEO_Vendor\Symfony\Component\DependencyInjection\ContainerInterface;

class Presentation_Memoizer {

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @var Indexable_Presentation[]
	 */
	private $cache = [];

	/**
	 * Presentation_Memoizer constructor.
	 *
	 * @param ContainerInterface $service_container
	 */
	public function __construct( ContainerInterface $service_container ) {
		$this->container = $service_container;
	}

	/**
	 * Gets the presentation of an indexable for a specific page type.
	 * This function is memoized by the indexable so every call with the same indexable will yield the same result.
	 *
	 * @param Indexable         $indexable The indexable to get a presentation of.
	 * @param Meta_Tags_Context $context   The current meta tags context.
	 * @param string            $page_type The page type.
	 *
	 * @return Indexable_Presentation The indexable presentation.
	 */
	public function get( Indexable $indexable, Meta_Tags_Context $context, $page_type ) {
		if ( ! isset( $this->cache[ $indexable->id ] ) ) {
			$presentation = $this->container->get( "Yoast\WP\Free\Presentations\Indexable_{$page_type}_Presentation", ContainerInterface::NULL_ON_INVALID_REFERENCE );

			if ( ! $presentation ) {
				$presentation = $this->container->get( Indexable_Presentation::class );
			}

			$this->cache[ $indexable->id ] = $context->presentation = $presentation->of( [ 'model' => $indexable, 'context' => $context ] );
		}

		return $this->cache[ $indexable->id ];
	}

	/**
	 * Clears the memoization of either a specific indexable or all indexables.
	 *
	 * @param Indexable|int $indexable Optional. The indexable or indexable id to clear the memoization of.
	 */
	public function clear( $indexable = null ) {
		if ( $indexable instanceof Indexable) {
			unset( $this->cache[ $indexable->id ] );
			return;
		}
		if ( is_int( $indexable ) ) {
			unset( $this->cache[ $indexable ] );
		}
		if ( $indexable === null ) {
			$this->cache = [];
		}
	}
}
