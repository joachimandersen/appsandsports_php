<?php

namespace Faucon\DataProviders;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DataTables
{
    protected $request;
    protected $repository;
    //protected $parameters;
    //protected $qb;

    protected $fresults;
    protected $offset;
    protected $echo;
    protected $amount;
    protected $search;

    protected $limit;
    protected $container;
    protected $options;
    
    public function __construct(Request $request, EntityRepository $repository, ContainerInterface $container, $options = array())
    {
        $this->request = $request;
        $this->repository = $repository;
        $this->container = $container;
        //$this->metadata = $repository->
        //$this->parameters = $this->getParameters();
        //$this->qb = $repository->createQueryBuilder('d');
        $this->echo = $this->request->get('sEcho');
        $this->search = $this->request->get('sSearch');
        $this->offset = $this->getOffset();
        $this->amount = $this->getAmount();
        $this->options = $options;
    }

    public function getJsonResult()
    {
        $this->fresults = $this->repository->getDataTablesResult($this->container, $this->offset, $this->amount, $this->options);
        $output = array("aaData" => $this->fresults);
        $rowFiltered = count($this->fresults);
        $outputHeader = array(
            "sEcho" => (int) $this->echo, 
            "iTotalRecords" => count($this->fresults), 
            "iTotalDisplayRecords" => $this->repository->getDataTablesRowsCount($this->container)
        );
        return array_merge($outputHeader, $output);
    }

    public function getOffset()
    {
        return $this->request->get('iDisplayStart');
    }

    public function getEcho()
    {
        return $this->request->get('sEcho');
    }

    public function getAmount()
    {
        return $this->request->get('iDisplayLength') ?: 5;
    }

    public function getSearch()
    {
        return  "%" . $this->request->get('sSearch') . "%";
    }
}
