<?php

namespace Faucon\DataProviders;
use Symfony\Component\DependencyInjection\ContainerInterface;

interface IDataTablesRepository
{
    public function getDataTablesResult(ContainerInterface $container, $pageoffset, $pagesize, $options);
    public function getDataTablesRowsCount(ContainerInterface $container);
}
