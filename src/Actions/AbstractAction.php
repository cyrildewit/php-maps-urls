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

class AbstractAction
{
    /**
     * @var array
     */
    protected $setters = [];

    /**
     * Create a new SearchAction instance.
     *
     * @param  array  $options
     * @return self
     */
    public static function make(array $options)
    {
        $action = new static;
        $setters = $action->setters;

        foreach ($options as $key => $value) {
            if (isset($setters[$key])) {
                $setter = $setters[$key];

                if (is_string($value)) {
                    $value = [$value];
                }

                call_user_func_array([$action, $setter], $value);

                // $action->$setter($value);
            }
        }

        return $action;
    }

    /**
     * Get the action's parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return [];
    }

    /**
     * Get the search action's endpoint.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return '';
    }
}
