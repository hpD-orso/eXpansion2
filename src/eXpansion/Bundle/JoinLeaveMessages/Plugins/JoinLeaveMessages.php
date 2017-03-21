<?php

namespace eXpansion\Bundle\JoinLeaveMessages\Plugins;

use eXpansion\Core\DataProviders\Listener\PlayerDataListenerInterface;
use eXpansion\Core\Services\Console;
use eXpansion\Core\Storage\Data\Player;
use Maniaplanet\DedicatedServer\Connection;

class JoinLeaveMessages implements PlayerDataListenerInterface
{
    /** @var Connection  */
    protected $connection;
    /** @var Console  */
    protected $console;
    /** @var bool  */
    private $enabled = true;

    /**
     * JoinLeaveMessages constructor.
     *
     * @param Connection $connection
     * @param Console $console
     */
    function __construct(Connection $connection, Console $console)
    {
        $this->connection = $connection;
        $this->console = $console;
    }

    /**
     * @inheritdoc
     */
    public function onRun()
    {
        // @todo make this callback work!
        $this->enabled = true;
    }

    /**
     * @inheritdoc
     */
    public function onPlayerConnect(Player $player)
    {
        $msg = '$fffHello, ' . $player->getNickName() . '  $n$fff($888' . $player->getLogin() . '$fff)';
        if ($this->enabled) {
            $this->connection->chatSendServerMessage($msg);
        }
    }

    /**
     * @inheritdoc
     */
    public function onPlayerDisconnect(Player $player, $disconnectionReason)
    {
        $msg = '$fffSee you, ' . $player->getNickName() . '  $n$fff($888' . $player->getLogin() . '$fff)';
        if ($this->enabled) {
            $this->connection->chatSendServerMessage($msg);
        }
    }

    /**
     * @inheritdoc
     */
    public function onPlayerInfoChanged(Player $oldPlayer, Player $player)
    {
    }

    /**
     * @inheritdoc
     */
    public function onPlayerAlliesChanged(Player $oldPlayer, Player $player)
    {
    }
}
