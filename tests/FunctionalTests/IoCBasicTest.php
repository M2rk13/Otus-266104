<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests;

use App\Command\BurnFuelCommand;
use App\Command\ChangeVelocityCommand;
use App\Enum\SpaceObjectPropertyEnum;
use App\Exception\ProjectException;
use App\Exception\PropertyNotFoundException;
use App\Maintenance\Ioc\IoC;
use App\Reposition\Coordinates;
use App\Reposition\FuelBurnableAdapter;
use App\SpaceObjects\Ship\DefaultShip;
use PHPUnit\Framework\TestCase;

class IoCBasicTest extends TestCase
{
    private DefaultShip $ship;

    public function testRegisteringDependencies(): void
    {
        $ioC = new IoC();
        $ioC = $this->getIoCWithDependencies($ioC);

        $burnFuelCommand = $ioC->resolve(BurnFuelCommand::class);

        $this->assertEquals(BurnFuelCommand::class, $burnFuelCommand::class);
    }

    public function testDependenceNotFound(): void
    {
        $ioC = new IoC();
        $ioC = $this->getIoCWithDependencies($ioC);

        $this->expectException(ProjectException::class);
        $this->expectExceptionMessage('Dependence [App\Command\ChangeVelocityCommand] is not registered');
        $ioC->resolve(ChangeVelocityCommand::class);
    }

    /**
     * @throws PropertyNotFoundException
     */
    public function testGettingDependencyFromIoc(): void
    {
        $ioC = new IoC();
        $ioC = $this->getIoCWithDependencies($ioC);

        /** @var BurnFuelCommand $burnFuelCommand */
        $burnFuelCommand = $ioC->resolve(BurnFuelCommand::class);
        $burnFuelCommand->execute();

        $this->assertEquals(0, $this->ship->getProperty(SpaceObjectPropertyEnum::FUEL_LEVEL_PROPERTY));
    }

    public function testNewScopeCreate(): void
    {
        $ioC = new IoC();

        $ioC->resolve(IoC::SCOPES_NEW, 'scope1');
        $ioC->resolve(IoC::SCOPES_CURRENT, 'scope1');

        $ioC = $this->getIoCWithDependencies($ioC);
        $burnFuelCommand = $ioC->resolve(BurnFuelCommand::class);

        $this->assertEquals(BurnFuelCommand::class, $burnFuelCommand::class, 'Не удалось найти зависимость в IoC контейнере.');

        $ioC->resolve(IoC::SCOPES_NEW, 'scope2');
        $ioC->resolve(IoC::SCOPES_CURRENT, 'scope2');

        $this->expectException(ProjectException::class);
        $this->expectExceptionMessage('Dependence [App\Command\BurnFuelCommand] is not registered');
        $ioC->resolve(BurnFuelCommand::class);
    }


    private function getIoCWithDependencies(IoC $ioC): IoC
    {
        $position = new Coordinates(12, 5);
        $velocity = new Coordinates(-7, 3);

        $ship = new DefaultShip();
        $ship->setProperty(SpaceObjectPropertyEnum::POSITION, $position);
        $ship->setProperty(SpaceObjectPropertyEnum::VELOCITY, $velocity);
        $ship->setProperty(SpaceObjectPropertyEnum::FUEL_LEVEL_PROPERTY, 1);
        $ship->setProperty(SpaceObjectPropertyEnum::FUEL_CONSUMPTION_PROPERTY, 1);

        $this->ship = $ship;

        $fuelBurnableAdapter = new FuelBurnableAdapter($this->ship);

        $ioC->resolve(IoC::IOC_REGISTER, BurnFuelCommand::class, function () use ($fuelBurnableAdapter) {
            return new BurnFuelCommand($fuelBurnableAdapter);
        });

        return $ioC;
    }
}
