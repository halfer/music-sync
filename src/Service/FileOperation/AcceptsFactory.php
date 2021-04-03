<?php

namespace MusicSync\Service\FileOperation;

trait AcceptsFactory
{
    protected ?Factory $factory = null;

    /**
     * Sets a custom object factory if required
     *
     * @param Factory $factory
     */
    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;
    }

    protected function getFactory()
    {
        // Only create a default one once
        static $factory = null;
        if (!$this->factory) {
            $factory = new Factory();
        }

        // Prefer the custom one, but use the default one otherwise
        return $this->factory ?: $factory;
    }
}
