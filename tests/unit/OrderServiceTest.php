<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Form\OrderFormType;
use App\Domain\Order\Service\EmailService;
use App\Domain\Order\Service\OrderService;
use App\Tests\Unit\api\_data\FakeEntity\Orders;
use Codeception\Stub\Expected;
use Codeception\Test\Unit;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormInterface;

class OrderServiceTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before(): void
    {
    }

    protected function _after(): void
    {
    }

    public function testCreate(): void
    {
        $this->tester->loginAs();

        $order = new Order();
        $this->validateOrder($order);

        $mockedEmailService = $this->make(EmailService::class, [
            'sendCreatedOrderMessage' => Expected::once(static function () {
            })
        ]);
        $this->tester->replaceServiceWithMock(EmailService::class, $mockedEmailService);
        /* @var OrderService $orderService */
        $orderService = $this->tester->grabService(OrderService::class);

        foreach ($this->notifyProvider() as $item) {
            $orderService->create($order, $item['sendNotify']);

            $this->tester->seeInRepository(Order::class, ['id' => $order->getId()]);
        }
        $this->tester->restoreMockedService(EmailService::class);
    }

    public function testUpdate(): void
    {
        $this->tester->loginAs();

        /* @var Order $order */
        $order = $this->tester->getEntityManager()->getRepository(Order::class)->findOneBy([], ['id' => 'desc']);
        $this->assertNotEmpty($order);

        $mockedEmailService = $this->make(EmailService::class, [
            'sendSuccessOrderMessage' => Expected::once(static function () {
            }),
            'sendUpdatedOrderMessage' => Expected::once(static function () {
            }),
        ]);
        $this->tester->replaceServiceWithMock(EmailService::class, $mockedEmailService);
        /* @var OrderService $orderService */
        $orderService = $this->tester->grabService(OrderService::class);

        // check only save order
        $orderService->update($order, false);
        // check sendUpdatedOrderMessage()
        $orderService->update($order, true);

        //check sendSuccessOrderMessage()
        $order->setStatusId(Order::STATUS_SUCCESS);
        $orderService->update($order, true);
        /* @var Order $updatedOrder */
        $updatedOrder = $this->tester->grabEntityFromRepository(Order::class, ['id' => $order->getId()]);
        $this->assertEquals(Order::STATUS_SUCCESS, $updatedOrder->getStatusId());

        $this->tester->restoreMockedService(EmailService::class);
    }

    public function createGroups(): void
    {

    }

    public function testGenerateFormFields(): void
    {

    }

    public function testExport(): void
    {

    }

    public function testLinkGroups(): void
    {

    }

    public function testGetFilters(): void
    {

    }

    public function testGetActiveErrors(): void
    {

    }

    private function validateOrder(Order $order): void
    {
        /** @var Container */
        $container = $this->getModule('Symfony')->_getContainer();
        /* @var FormInterface $form */
        $form      = $container->get('form.factory')->create(OrderFormType::class, $order);
        $orderData = Orders::getAll()[0];
        $form->submit($orderData);
    }

    private function notifyProvider(): array
    {
        return [
            ['sendNotify' => false],
            ['sendNotify' => true],
        ];
    }

}