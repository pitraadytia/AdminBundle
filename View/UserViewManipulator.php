<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\View;

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Controller\UserController;
use Symfonian\Indonesia\AdminBundle\EventListener\AbstractListener;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UserViewManipulator extends AbstractListener
{
    /**
     * @var Configurator
     */
    private $configuration;

    /**
     * @var KernelInterface
     */
    private $kernel;

    private $formClass;
    private $entityClass;
    private $showFields = array();
    private $gridFields = array();
    private $gridFilters = array();

    public function __construct(Configurator $configurator, KernelInterface $kernel)
    {
        $this->configuration = $configurator;
        $this->kernel = $kernel;
    }

    public function setForm($formClass, $entityClass)
    {
        $this->formClass = $formClass;
        $this->entityClass = $entityClass;
    }

    public function setView(array $showFields, array $gridFields, array $gridFilters)
    {
        $this->showFields = $showFields;
        $this->gridFields = $gridFields;
        $this->gridFilters = $gridFilters;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if ('prod' === strtolower($this->kernel->getEnvironment())) {
            return;
        }
        $this->isValidCrudListener($event);
        if (!$this->getController() instanceof UserController) {
            return;
        }

        /** @var Crud $crud */
        $crud = $this->configuration->getConfiguration(Crud::class);
        $crud->setFormClass($this->formClass);
        $crud->setEntityClass($this->entityClass);
        $crud->setShowFields($this->showFields);

        /** @var Grid $grid */
        $grid = $this->configuration->getConfiguration(Grid::class);
        $grid->setColumns($this->gridFields);
        $grid->setFilters($this->gridFilters);
        $grid->setSortable($this->gridFilters);

        $this->configuration->addConfiguration($crud);
        $this->configuration->addConfiguration($grid);
    }
}
