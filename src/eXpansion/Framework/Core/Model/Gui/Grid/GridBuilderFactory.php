<?php

namespace eXpansion\Framework\Core\Model\Gui\Grid;

use eXpansion\Framework\Core\Model\Gui\Factory\LineFactory;
use eXpansion\Framework\Core\Model\Gui\Factory\PagerFactory;
use eXpansion\Framework\Core\Plugins\Gui\ActionFactory;


/**
 * Class GridBuilderFactory
 *
 * @package eXpansion\Framework\Core\Model\Gui\Grid;
 * @author  oliver de Cramer <oliverde8@gmail.com>
 */
class GridBuilderFactory
{
    /** @var string */
    protected $class;

    /** @var ActionFactory */
    protected $actionFactory;

    /** @var LineFactory */
    protected $lineFactory;

    /** @var LineFactory */
    protected $titleLineFactory;

    /** @var PagerFactory */
    protected $pagerFactory;

    /**
     * GridBuilderFactory constructor.
     *
     * @param string        $class
     * @param ActionFactory $actionFactory
     */
    public function __construct(
        $class,
        ActionFactory $actionFactory,
        LineFactory $lineFactory,
        LineFactory $titleLineFactory,
        PagerFactory $pagerFactory
    )
    {
        $this->class = $class;
        $this->actionFactory = $actionFactory;
        $this->lineFactory = $lineFactory;
        $this->titleLineFactory = $titleLineFactory;
        $this->pagerFactory = $pagerFactory;
    }

    /**
     * @return GridBuilder
     */
    public function create()
    {
        $class = $this->class;
        return new $class($this->actionFactory, $this->lineFactory, $this->titleLineFactory, $this->pagerFactory);
    }
}
