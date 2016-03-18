<?php

/**
 * Index interface for the ke_search extension to index the events
 */

namespace HDNET\Calendarize\Features;

use HDNET\Calendarize\Domain\Model\Index;

/**
 * Index interface for the ke_search extension to index the events
 */
interface KeSearchIndexInterface
{

    /**
     * Get the title
     *
     * @param Index $index
     *
     * @return string
     */
    public function getKeSearchTitle(Index $index);

    /**
     * Get the abstract
     *
     * @param Index $index
     *
     * @return string
     */
    public function getKeSearchAbstract(Index $index);

    /**
     * Get the content
     *
     * @param Index $index
     *
     * @return string
     */
    public function getKeSearchContent(Index $index);
}
