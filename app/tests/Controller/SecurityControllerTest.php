<?php

namespace App\tests\Controller;


use App\Entity\User;
use PHPUnit\Framework\TestCase;

class SecurityControllerTest extends TestCase
{
    /*
     * Just the simplest UNIT test to check if we can properly set the vars
     */
    public function testRegister()
    {
        $user = new User();
        $user->setFullName('Name Surname');

        $this->assertEquals('Name Surname', $user->getFullName());
    }

}