<?php

namespace Fashiongroup\Swiper;

use Webmozart\KeyValueStore\Api\KeyValueStore;

trait StoreAwareTrait
{
    protected $store;

    /**
     * @return KeyValueStore
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param KeyValueStore $store
     * @return $this
     */
    public function setStore(KeyValueStore $store)
    {
        $this->store = $store;

        return $this;
    }
}
