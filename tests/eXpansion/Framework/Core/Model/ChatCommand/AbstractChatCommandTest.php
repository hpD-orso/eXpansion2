<?php
/**
 * Created by PhpStorm.
 * User: olive
 * Date: 01/04/2017
 * Time: 17:41
 */

namespace Tests\eXpansion\Framework\Core\Model\ChatCommand;

use eXpansion\Framework\Core\Model\ChatCommand\AbstractChatCommand;
use eXpansion\Framework\Core\Model\Helpers\ChatNotificationInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\eXpansion\Framework\Core\TestCore;
use Tests\eXpansion\Framework\Core\TestHelpers\Model\TestChatCommand;

class AbstractChatCommandTest extends TestCore
{
    protected function setUp()
    {
        parent::setUp();

        $notification = $this->getMockBuilder(ChatNotificationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->container->set('expansion.framework.core.helpers.chat_notification', $notification);
    }

    public function testModel()
    {
        $cmd2 = new TestChatCommand('test', ['t']);

        $this->assertEquals('test', $cmd2->getCommand());
        $this->assertEquals(['t'], $cmd2->getAliases());
        $this->assertEquals('expansion_core.chat_commands.no_description', $cmd2->getDescription());
        $this->assertEquals('expansion_core.chat_commands.no_help', $cmd2->getHelp());

        $cmd2->run('toto', $this->getChatOutputHelper(), '--help');
    }

    public function testHelp()
    {
        $cmd2 = new TestChatCommand('test', ['t']);

        $this->getChatNotificationMock()
            ->expects($this->at(0))
            ->method('sendMessage')->with($cmd2->getDescription(), 'toto');

        $cmd2->run('toto', $this->getChatOutputHelper(), '--help');
        $this->assertFalse($cmd2->executed);
    }

    public function testExecute()
    {
        $cmd2 = new TestChatCommand('test', ['t']);

        $cmd2->run('toto', $this->getChatOutputHelper(), '');
        $this->assertTrue($cmd2->executed);
    }

    public function testExecuteError()
    {
        $cmd2 = new TestChatCommand('test', ['t']);

        $this->expectException(RuntimeException::class);
        $cmd2->run('toto', $this->getChatOutputHelper(), 'test');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getChatNotificationMock()
    {
        return $this->container->get('expansion.framework.core.helpers.chat_notification');
    }
}
