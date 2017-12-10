<?php

namespace Guzzle\Inflection;

/**
 * Decorator used to add memoization to previously inflected words
 */
class MemoizingInflector implements InflectorInterface {
	/** @var array Array of cached inflections */
	protected $cache = [
		'snake' => [],
		'camel' => [],
	];

	/** @var InflectorInterface Decorated inflector */
	protected $decoratedInflector;

	/** @var int Max entries per cache */
	protected $maxCacheSize;

	/**
	 * @param InflectorInterface $inflector    Inflector being decorated
	 * @param int                $maxCacheSize Maximum number of cached items to hold per cache
	 */
	public function __construct(InflectorInterface $inflector, $maxCacheSize = 500) {
		$this->decoratedInflector = $inflector;
		$this->maxCacheSize = $maxCacheSize;
	}

	/**
	 * Converts strings from snake_case to upper CamelCase
	 *
	 * @param string $word Value to convert into upper CamelCase
	 *
	 * @return string
	 */
	public function camel($word) {
		if (!isset($this->cache['camel'][$word])) {
			$this->pruneCache('camel');
			$this->cache['camel'][$word] = $this->decoratedInflector->camel($word);
		}

		return $this->cache['camel'][$word];
	}

	public function snake($word) {
		if (!isset($this->cache['snake'][$word])) {
			$this->pruneCache('snake');
			$this->cache['snake'][$word] = $this->decoratedInflector->snake($word);
		}

		return $this->cache['snake'][$word];
	}

	/**
	 * Prune one of the named caches by removing 20% of the cache if it is full
	 *
	 * @param string $cache Type of cache to prune
	 */
	protected function pruneCache($cache) {
		if (count($this->cache[$cache]) == $this->maxCacheSize) {
			$this->cache[$cache] = array_slice($this->cache[$cache], $this->maxCacheSize * 0.2);
		}
	}
}