<?php

namespace Fashiongroup\Swiper;

use Webmozart\KeyValueStore\Api\KeyValueStore;

interface StoreAwareInterface
{
    /**
     * @return KeyValueStore
     */
    public function getStore();

    /**
     * @param KeyValueStore $store
     * @return $this
     */
    public function setStore(KeyValueStore $store);
}
