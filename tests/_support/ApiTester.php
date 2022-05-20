<?php

namespace App\Tests;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    /**
     * Define custom actions here
     */

    public function amAuthenticated(): void
    {
        $this->sendPost('/login',
            ['email' => 'dmitry.matytsin@mediadirectiongroup.ru', 'password' => 'dimon770']);
        [$token] = $this->grabDataFromResponseByJsonPath('$.token');
        $this->amBearerAuthenticated($token);
    }
}
