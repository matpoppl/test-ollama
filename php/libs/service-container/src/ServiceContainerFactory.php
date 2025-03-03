<?php

declare(strict_types=1);

namespace Lib\ServiceContainer;

class ServiceContainerFactory
{
    public function create(array $options): ServiceContainer
    {
        $smOptions = $options['service_container'] ?? [];
        unset($options['service_container']);

        $smOptions['aliases']['config'] = ServicesConfigs::class;
        $sm = new ServiceContainer($smOptions);
        $sm->set(ServicesConfigs::class, new ServicesConfigs($options));

        return $sm;
    }
}
