<?php
/**
 * Created by PhpStorm.
 * User: olive
 * Date: 13/05/2017
 * Time: 12:24
 */

namespace Tests\eXpansion\Framework\AdminGroups\Helpers;

use eXpansion\Framework\AdminGroups\Exceptions\UnknownGroupException;
use Tests\eXpansion\Framework\AdminGroups\TestAdminGroups;


/**
 * Class AdminGroups
 *
 * @package Tests\eXpansion\Framework\AdminGroups\Helpers;
 * @author oliver de Cramer <oliverde8@gmail.com>
 */
class AdminGroupsTest extends TestAdminGroups
{

    public function testGetUserGroups()
    {
        $helper = $this->getAdminGroupHelper();

        $this->assertCount(count($this->getAdminGroupConfiguration()), $helper->getUserGroups());
    }

    public function testGetLoginUserGroup()
    {
        $helper = $this->getAdminGroupHelper();

        $this->assertEquals('admin:master_admin', $helper->getLoginUserGroups('toto1')->getName());
        $this->assertEquals('admin:operator', $helper->getLoginUserGroups('toto21')->getName());
        $this->assertEquals('admin:guest', $helper->getLoginUserGroups('toto_invalid')->getName());
    }

    public function testHasPermission()
    {
        $helper = $this->getAdminGroupHelper();

        $this->assertTrue($helper->hasPermission('toto1', 'p1'));
    }

    public function testHasGroupPermission()
    {
        $helper = $this->getAdminGroupHelper();

        $this->assertTrue($helper->hasGroupPermission('master_admin', 'wrong_permission'));
        $this->assertTrue($helper->hasGroupPermission('admin:master_admin', 'wrong_permission'));
        $this->assertFalse($helper->hasGroupPermission('admin:empty', 'wrong_permission'));
    }

    public function testHasGroupPermissionsException()
    {
        $helper = $this->getAdminGroupHelper();

        $this->expectException(UnknownGroupException::class);

        $helper->hasGroupPermission('yoyo', 'p20');
    }
}
