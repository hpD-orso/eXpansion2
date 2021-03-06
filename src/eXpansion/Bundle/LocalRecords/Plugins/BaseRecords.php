<?php

namespace eXpansion\Bundle\LocalRecords\Plugins;

use eXpansion\Bundle\LocalRecords\Services\RecordHandler;
use eXpansion\Bundle\LocalRecords\Services\RecordHandlerFactory;
use eXpansion\Framework\Core\DataProviders\Listener\PlayerDataListenerInterface;
use eXpansion\Framework\Core\Model\UserGroups\Group;
use eXpansion\Framework\Core\Plugins\StatusAwarePluginInterface;
use eXpansion\Framework\Core\Services\Application\DispatcherInterface;
use eXpansion\Framework\Core\Storage\Data\Player;
use eXpansion\Framework\Core\Storage\MapStorage;
use eXpansion\Framework\GameManiaplanet\DataProviders\Listener\MapDataListenerInterface;
use eXpansion\Framework\GameManiaplanet\DataProviders\Listener\MatchDataListenerInterface;
use Maniaplanet\DedicatedServer\Structures\Map;

/**
 * Class RaceRecords
 *
 * ADD status aware interface and load on activation.
 *
 * @package eXpansion\Bundle\LocalRecords\Plugins;
 * @author  oliver de Cramer <oliverde8@gmail.com>
 */
class BaseRecords implements MapDataListenerInterface, MatchDataListenerInterface, PlayerDataListenerInterface, StatusAwarePluginInterface
{
    /** @var  RecordHandler */
    protected $recordsHandler;

    /** @var Group */
    protected $allPlayersGroup;

    /** @var MapStorage */
    protected $mapStorage;

    /** @var string */
    protected $eventPrefix;

    /** @var DispatcherInterface */
    protected $dispatcher;

    /**
     * BaseRecords constructor.
     *
     * @param RecordHandlerFactory $recordsHandlerFactory
     * @param Group                $allPlayersGroup
     * @param MapStorage           $mapStorage
     * @param                      $eventPrefix
     */
    public function __construct(
        RecordHandlerFactory $recordsHandlerFactory,
        Group $allPlayersGroup,
        MapStorage $mapStorage,
        DispatcherInterface $dispatcher,
        $eventPrefix
    ) {
        $this->recordsHandler = $recordsHandlerFactory->create();
        $this->allPlayersGroup = $allPlayersGroup;
        $this->mapStorage = $mapStorage;
        $this->eventPrefix = $eventPrefix;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get the current record handler.
     *
     * @return RecordHandler|mixed
     */
    public function getRecordsHandler()
    {
        return $this->recordsHandler;
    }

    /**
     * Set the status of the plugin
     *
     * @param boolean $status
     *
     * @return null
     */
    public function setStatus($status)
    {
        if ($status) {
            $map = $this->mapStorage->getCurrentMap();

            // Load firs X records for this map.
            $this->recordsHandler->loadForMap($map->uId, $this->getNbLaps());

            // Load time information for remaining players.
            $this->recordsHandler->loadForPlayers($map->uId, $this->getNbLaps(), $this->allPlayersGroup->getLogins());

            $this->dispatchEvent(['event' => 'loaded', 'records' => $this->recordsHandler->getRecords()]);
        }
    }

    /**
     * Callback sent when the "StartMap" section start.
     *
     * @param int     $count     Each time this section is played, this number is incremented by one
     * @param int     $time      Server time when the callback was sent
     * @param boolean $restarted true if the map was restarted, false otherwise
     * @param Map     $map       Map started with.
     *
     * @return mixed
     */
    public function onStartMapStart($count, $time, $restarted, Map $map)
    {
        // Load firs X records for this map.
        $this->recordsHandler->loadForMap($map->uId, $this->getNbLaps());

        // Load time information for remaining players.
        $this->recordsHandler->loadForPlayers($map->uId, $this->getNbLaps(), $this->allPlayersGroup->getLogins());

        // Let others know that records information is now available.
        $this->dispatchEvent(['event' => 'loaded', 'records' => $this->recordsHandler->getRecords()]);
    }

    /**
     * @param Player $player
     */
    public function onPlayerConnect(Player $player)
    {
        $this->recordsHandler->loadForPlayers($this->mapStorage->getCurrentMap()->uId, [1], [$player->getLogin()]);
    }

    /**
     * Callback sent when the "EndMap" section end.
     *
     * @param int     $count     Each time this section is played, this number is incremented by one
     * @param int     $time      Server time when the callback was sent
     * @param boolean $restarted true if the map was restarted, false otherwise
     * @param Map     $map       Map started with.
     *
     * @return mixed
     */
    public function onEndMapEnd($count, $time, $restarted, Map $map)
    {
        // Nothing to do
    }

    /**
     * Callback sent when the "StartMatch" section start.
     *
     * @param int $count Each time this section is played, this number is incremented by one
     * @param int $time  Server time when the callback was sent
     *
     * @return mixed
     */
    public function onStartMatchStart($count, $time)
    {
        // Nothing to do.
    }

    /**
     * Callback sent when the "StartMatch" section end.
     *
     * @param int $count Each time this section is played, this number is incremented by one
     * @param int $time  Server time when the callback was sent
     *
     * @return mixed
     */
    public function onStartMatchEnd($count, $time)
    {
        $this->recordsHandler->save();
    }

    /**
     * Callback sent when the "StartMap" section end.
     *
     * @param int     $count     Each time this section is played, this number is incremented by one
     * @param int     $time      Server time when the callback was sent
     * @param boolean $restarted true if the map was restarted, false otherwise
     * @param Map     $map       Map started with.
     *
     * @return mixed
     */
    public function onStartMapEnd($count, $time, $restarted, Map $map)
    {
        // Nothing to do.
    }

    /**
     * Callback sent when the "EndMap" section start.
     *
     * @param int     $count     Each time this section is played, this number is incremented by one
     * @param int     $time      Server time when the callback was sent
     * @param boolean $restarted true if the map was restarted, false otherwise
     * @param Map     $map       Map started with.
     *
     * @return mixed
     */
    public function onEndMapStart($count, $time, $restarted, Map $map)
    {
        // Nothing to do.
    }

    public function onPlayerDisconnect(Player $player, $disconnectionReason)
    {
        // Nothing to do.
    }

    public function onPlayerInfoChanged(Player $oldPlayer, Player $player)
    {
        // Nothing to do.
    }

    public function onPlayerAlliesChanged(Player $oldPlayer, Player $player)
    {
        // Nothing to do.
    }

    /**
     * Dispatch a record event.
     *
     * @param $eventData
     */
    protected function dispatchEvent($eventData)
    {
        $event = $this->eventPrefix . '.' . $eventData['event'];
        unset($eventData['event']);

        $this->dispatcher->dispatch($event, [$eventData]);
    }

    /**
     * Get number of laps
     *
     * @return int
     */
    protected function getNbLaps()
    {
        return 1;
    }
}