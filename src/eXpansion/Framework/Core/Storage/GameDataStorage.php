<?php

namespace eXpansion\Framework\Core\Storage;
use Maniaplanet\DedicatedServer\Structures\GameInfos;
use Maniaplanet\DedicatedServer\Structures\Version;
use oliverde8\AssociativeArraySimplified\AssociativeArray;

/**
 * Class GameDataStorage
 *
 * @author    de Cramer Oliver<oldec@smile.fr>
 * @copyright 2017 Smile
 * @package eXpansion\Framework\Core\Storage
 */
class GameDataStorage
{
    /**
     * Constant used for unknown game modes.
     */
    const GAME_MODE_CODE_UNKNOWN = 'unknown';

    /**
     * Constant used for unknown titles.
     */
    const TITLE_UNKNOWN = 'unknown';

    /** @var  GameInfos */
    protected $gameInfos;

    /** @var Version */
    protected $version;

    /**
     * @var AssociativeArray
     */
    protected $gameModeCodes;

    /**
     * @var AssociativeArray
     */
    protected $titles;

    /**
     * GameDataStorage constructor.
     *
     * @param array $gameModeCodes
     */
    public function __construct(array $gameModeCodes, array $titles)
    {
        $this->gameModeCodes = new AssociativeArray($gameModeCodes);
        $this->titles = new AssociativeArray($titles);
    }


    /**
     * @return GameInfos
     */
    public function getGameInfos()
    {
        return $this->gameInfos;
    }

    /**
     * @param GameInfos $gameInfos
     */
    public function setGameInfos($gameInfos)
    {
        $this->gameInfos = $gameInfos;
    }

    /**
     * @return Version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param Version $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Get code of the game mode.
     *
     * @return mixed
     */
    public function getGameModeCode()
    {
        return $this->gameModeCodes->get($this->getGameInfos()->gameMode, self::GAME_MODE_CODE_UNKNOWN);
    }

    /**
     * Get the title name, this returns a simplified title name such as TM
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->titles->get($this->getVersion()->titleId, self::TITLE_UNKNOWN);
    }
}