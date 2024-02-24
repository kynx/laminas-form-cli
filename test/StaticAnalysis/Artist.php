<?php

declare(strict_types=1);

namespace KynxTest\Laminas\FormShape\StaticAnalysis;

use Laminas\Form\Element\Collection;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

final class Artist extends Form
{
    /**
     * @param int|null|string $name
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->add(new Hidden('id'));
        $this->add(new Text('name'));
        $collection = new Collection('albums');
        $collection->setTargetElement(new Album());
        $this->add($collection);
    }
}