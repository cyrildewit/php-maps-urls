<?php

namespace CyrildeWit\MapsUrls\Actions;

/*
 * This file is part of the Maps URLs package.
 *
 * (c) Cyril de Wit <github@cyrildewit.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface ActionInterface
{
    /**
     * Get the action's parameters.
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * Get the action endpoint.
     *
     * @return string
     */
    public function getEndpoint(): string;
}
