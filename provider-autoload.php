<?php

require_once __DIR__ . '/src/ProjectCascade/Billing/PaymentSystem/BillingBundle/PaymentSystemProviderInterface.php';

$autoload = static function (string $pattern) {
    $classes = glob(__DIR__ . $pattern);

    foreach ($classes as $class) {
        require_once $class;
    }
};

$autoload('/src/ProjectCascade/Billing/PaymentSystem/*/*.php');

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;

$classList = get_declared_classes();
$providerList = [];


/** @var PaymentSystemProviderInterface $class */
foreach ($classList as $class) {
    if (in_array(
        PaymentSystemProviderInterface::class,
        class_implements($class),
        true
    )) {
        $providerList[$class::getName()] = $class;
    }
}

return $providerList;
