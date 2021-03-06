<?php

namespace AdvancedMeta;

class Factory {

	/**
	 *
	 * @var MetaHandler[]
	 */
	protected $instances = [];

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	protected $lb = null;

	/**
	 * @param \Config $config
	 * @return Factory
	 */
	public function __construct( $config, $lb = null ) {
		$this->config = $config;
		$this->lb = $lb;
	}

	protected function getDB() {
		if ( !$this->lb ) { // only on mw < 1.28
			return wfGetDB( DB_MASTER );
		}
		return $this->lb->getConnection( DB_MASTER );
	}

	/**
	 *
	 * @param \Title|null $title
	 * @return MetaHandler | null
	 */
	public function newFromTitle( \Title $title = null ) {
		$instance = null;
		if ( !$title ) {
			return $instance;
		}

		if ( !$instance = $this->fromCache( $title ) ) {
			$instance = $this->appendCache(
				new MetaHandler(
					$this->config,
					$title,
					$this->getDB()
				)
			);
		}
		return $instance;
	}

	/**
	 *
	 * @param \Title $title
	 * @return GlobalMetaKeys
	 */
	public function newGlobalKeywordsFromTitle( \Title $title ) {
		return new GlobalMetaKeys( $this->config, $title );
	}

	/**
	 * TODO: real object cache!
	 * @param \Title $title
	 * @return MetaHandler | null
	 */
	protected function fromCache( \Title $title ) {
		if ( !isset( $this->instances[$title->getArticleID()] ) ) {
			return null;
		}
		return $this->instances[$title->getArticleID()];
	}

	/**
	 * @param MetaHandler $instance
	 * @return MetaHandler
	 */
	protected function appendCache( MetaHandler $instance ) {
		if ( $instance->getTitle()->getArticleId() < 1 ) {
			return $instance;
		}
		$this->instances[$instance->getTitle()->getArticleId()] = $instance;
		return $instance;
	}

	/**
	 *
	 * @param MetaHandler $instance
	 * @return bool
	 */
	public function invalidateCache( MetaHandler $instance ) {
		if ( !isset( $this->instances[$instance->getTitle()->getArticleId()] ) ) {
			return false;
		}
		unset( $this->instances[$instance->getTitle()->getArticleId()] );
		return true;
	}
}
