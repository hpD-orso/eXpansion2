<?php
/**
 * File GuiHandlerTest.php
 *
 * @author    de Cramer Oliver<oldec@smile.fr>
 * @copyright 2017 Smile
 */

namespace Tests\eXpansion\Framework\Core\Plugins;

use eXpansion\Framework\Core\Model\Gui\Manialink;
use eXpansion\Framework\Core\Model\UserGroups\Group;
use eXpansion\Framework\Core\Plugins\GuiHandler;
use Tests\eXpansion\Framework\Core\TestCore;
use Tests\eXpansion\Framework\Core\TestHelpers\ManialinkDataTrait;

class GuiHandlerTest extends TestCore
{
    use ManialinkDataTrait;

    public function testSendManialink()
    {
        $logins = ['test1', 'test2'];
        $manialink = $this->getManialink($logins);


        /** @var \PHPUnit_Framework_MockObject_MockObject $dedicatedConnection */
        $dedicatedConnection = $this->container->get('expansion.framework.core.services.dedicated_connection');
        $dedicatedConnection->expects($this->once())
            ->method('sendDisplayManialinkPage')
            ->with($logins, $manialink->getXml());

        /** @var GuiHandler $guiHanlder */
        $guiHanlder = $this->container->get('expansion.framework.core.plugins.gui_handler');
        $guiHanlder->addToDisplay($manialink);

        $guiHanlder->onPostLoop();
    }

    public function testHideManialink()
    {
        $logins = ['test1', 'test2'];
        $manialink = $this->getManialink($logins);


        /** @var \PHPUnit_Framework_MockObject_MockObject $dedicatedConnection */
        $dedicatedConnection = $this->container->get('expansion.framework.core.services.dedicated_connection');
        $dedicatedConnection->expects($this->once())
            ->method('sendDisplayManialinkPage')
            ->with($logins, '<manialink id="' . $manialink->getId() . '" />');

        /** @var GuiHandler $guiHanlder */
        $guiHanlder = $this->container->get('expansion.framework.core.plugins.gui_handler');
        $guiHanlder->addToHide($manialink);

        $guiHanlder->onPostLoop();
    }

    public function testShowHideShow()
    {

        $logins = ['test1', 'test2'];
        $manialink = $this->getManialink($logins);


        /** @var \PHPUnit_Framework_MockObject_MockObject $dedicatedConnection */
        $dedicatedConnection = $this->container->get('expansion.framework.core.services.dedicated_connection');
        $dedicatedConnection->expects($this->once())
            ->method('sendDisplayManialinkPage')
            ->with($logins, $manialink->getXml());

        /** @var GuiHandler $guiHanlder */
        $guiHanlder = $this->container->get('expansion.framework.core.plugins.gui_handler');
        $guiHanlder->addToDisplay($manialink);
        $guiHanlder->addToHide($manialink);
        $guiHanlder->addToDisplay($manialink);

        $guiHanlder->onPostLoop();
    }

    public function testShowPostHide()
    {

        $logins = ['test1', 'test2'];
        $manialink = $this->getManialink($logins);


        /** @var \PHPUnit_Framework_MockObject_MockObject $dedicatedConnection */
        $dedicatedConnection = $this->container->get('expansion.framework.core.services.dedicated_connection');
        $dedicatedConnection->expects($this->exactly(2))
            ->method('sendDisplayManialinkPage')
            ->withConsecutive([$logins, $manialink->getXml()], [$logins, '<manialink id="' . $manialink->getId() . '" />']);

        /** @var GuiHandler $guiHanlder */
        $guiHanlder = $this->container->get('expansion.framework.core.plugins.gui_handler');
        $guiHanlder->addToDisplay($manialink);
        $guiHanlder->onPostLoop();

        $guiHanlder->addToHide($manialink);
        $guiHanlder->onPostLoop();
    }

    public function testConnect()
    {
        $logins = ['test1', 'test2'];
        $manialink = $this->getManialink($logins);


        /** @var \PHPUnit_Framework_MockObject_MockObject $dedicatedConnection */
        $dedicatedConnection = $this->container->get('expansion.framework.core.services.dedicated_connection');
        $dedicatedConnection->expects($this->exactly(2))
            ->method('sendDisplayManialinkPage')
            ->withConsecutive([$logins, $manialink->getXml()], ['test3', $manialink->getXml()]);

        /** @var GuiHandler $guiHanlder */
        $guiHanlder = $this->container->get('expansion.framework.core.plugins.gui_handler');
        $guiHanlder->addToDisplay($manialink);

        $guiHanlder->onPostLoop();
        $guiHanlder->onExpansionGroupAddUser($manialink->getUserGroup(), 'test3');
        $guiHanlder->onPostLoop();
    }

    public function testDisconnectConnect()
    {
        $logins = ['test1', 'test2'];
        $manialink = $this->getManialink($logins);


        /** @var \PHPUnit_Framework_MockObject_MockObject $dedicatedConnection */
        $dedicatedConnection = $this->container->get('expansion.framework.core.services.dedicated_connection');
        $dedicatedConnection->expects($this->exactly(2))
            ->method('sendDisplayManialinkPage')
            ->withConsecutive([$logins, $manialink->getXml()], ['test1', '<manialink id="' . $manialink->getId() . '" />']);

        /** @var GuiHandler $guiHanlder */
        $guiHanlder = $this->container->get('expansion.framework.core.plugins.gui_handler');
        $guiHanlder->addToDisplay($manialink);

        $guiHanlder->onPostLoop();
        $guiHanlder->onExpansionGroupRemoveUser($manialink->getUserGroup(), 'test1');
        $guiHanlder->onPostLoop();
    }

    public function testDestroy()
    {
        $logins = ['test1', 'test2'];
        $manialink = $this->getManialink($logins);


        /** @var \PHPUnit_Framework_MockObject_MockObject $dedicatedConnection */
        $dedicatedConnection = $this->container->get('expansion.framework.core.services.dedicated_connection');
        $dedicatedConnection->expects($this->once())
            ->method('sendDisplayManialinkPage')
            ->with($logins, $manialink->getXml());

        /** @var GuiHandler $guiHanlder */
        $guiHanlder = $this->container->get('expansion.framework.core.plugins.gui_handler');
        $guiHanlder->addToDisplay($manialink);

        $guiHanlder->onPostLoop();
        $guiHanlder->onExpansionGroupDestroy($manialink->getUserGroup(), 'test1');

        $this->assertEmpty($guiHanlder->getDisplayeds());
    }

    public function testExtreme()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $dedicatedConnection */
        $dedicatedConnection = $this->container->get('expansion.framework.core.services.dedicated_connection');
        $dedicatedConnection->expects($this->exactly(2))
            ->method('sendDisplayManialinkPage')
            ->withAnyParameters();
        $dedicatedConnection->expects($this->exactly(2))
            ->method('executeMulticall')
            ->withAnyParameters();
        $logins = ['test1', 'test2'];

        $guiHanlder = $this->container->get('expansion.framework.core.plugins.gui_handler');
        $guiHanlder->setCharLimit(160);

        for ($i = 0; $i < 2; $i++) {
            $manialink = $this->getManialink($logins);
            $guiHanlder->addToDisplay($manialink);
        }

        $guiHanlder->onPostLoop();
    }

    public function testError()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $dedicatedConnection */
        $dedicatedConnection = $this->container->get('expansion.framework.core.services.dedicated_connection');
        $dedicatedConnection->expects($this->exactly(2))
            ->method('sendDisplayManialinkPage')
            ->withAnyParameters();
        $dedicatedConnection->method('executeMulticall')
            ->will($this->throwException(new \Exception));
        $logins = ['test1', 'test2'];

        /** @var \PHPUnit_Framework_MockObject_MockObject $loggerMock */
        $loggerMock = $this->container->get('expansion.framework.core.services.console');
        $loggerMock->expects($this->exactly(2))->method('writeln');

        $guiHanlder = $this->container->get('expansion.framework.core.plugins.gui_handler');
        $guiHanlder->setCharLimit(160);

        for ($i = 0; $i < 2; $i++) {
            $manialink = $this->getManialink($logins);
            $guiHanlder->addToDisplay($manialink);
        }

        $guiHanlder->onPostLoop();
    }

    public function testEmptyMethods()
    {
        /** @var GuiHandler $guiHanlder */
        $guiHanlder = $this->container->get('expansion.framework.core.plugins.gui_handler');

        $guiHanlder->onPreLoop();
        $guiHanlder->onEverySecond();
    }
}
