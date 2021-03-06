<?php
/**
 * Created by PhpStorm.
 * User: olive
 * Date: 12/03/2017
 * Time: 11:56
 */

namespace eXpansion\Framework\Core\Plugins;

/**
 * Interface StatusAwarePluginInterface is to be implemented by plugins that needs to be aware of their status.
 *
 * @package eXpansion\Framework\Core\Plugins
 */
interface StatusAwarePluginInterface
{
    /**
     * Set the status of the plugin
     *
     * @param boolean $status
     *
     * @return null
     */
    public function setStatus($status);
}