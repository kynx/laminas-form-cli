<?php

declare(strict_types=1);

namespace KynxTest\Laminas\FormShape\Form;

use Kynx\Laminas\FormShape\Form\FormVisitor;
use Kynx\Laminas\FormShape\InputFilter\CollectionInputVisitor;
use Kynx\Laminas\FormShape\InputFilter\InputFilterVisitor;
use Kynx\Laminas\FormShape\InputFilter\InputVisitor;
use Laminas\Form\Element\Collection;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psalm\Type;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Atomic\TNull;
use Psalm\Type\Atomic\TString;
use Psalm\Type\Union;

#[CoversClass(FormVisitor::class)]
final class FormVisitorTest extends TestCase
{
    private FormVisitor $visitor;

    protected function setUp(): void
    {
        parent::setUp();

        $inputVisitor      = new InputVisitor([], []);
        $collectionVisitor = new CollectionInputVisitor($inputVisitor);
        $this->visitor     = new FormVisitor(new InputFilterVisitor([$collectionVisitor, $inputVisitor]));
    }

    public function testVisitSingleElement(): void
    {
        $expected = new Union([
            new TKeyedArray([
                'foo' => new Union([new TString(), new TNull()], ['possibly_undefined' => true]),
            ]),
        ], ['possibly_undefined' => true]);

        $form = new Form();
        $form->add(new Text('foo'));

        $actual = $this->visitor->visit($form);
        self::assertEquals($expected, $actual);
    }

    public function testVisitCollectionWithFieldsetTargetElement(): void
    {
        $expected = new Union([
            new TKeyedArray([
                'foo' => new Union([
                    new TArray([
                        Type::getArrayKey(),
                        new Union([
                            new TKeyedArray([
                                'baz' => new Union([new TString(), new TNull()], ['possibly_undefined' => true]),
                            ]),
                        ], ['possibly_undefined' => true]),
                    ]),
                ], ['possibly_undefined' => true]),
            ]),
        ], ['possibly_undefined' => true]);

        $form          = new Form();
        $collection    = new Collection('foo');
        $targetElement = new Fieldset('bar');
        $targetElement->add(new Text('baz'));
        $collection->setTargetElement($targetElement);
        $form->add($collection);

        $clone = clone $form;
        $clone->setData([]);
        self::assertTrue($clone->isValid());

        $actual = $this->visitor->visit($form);
        self::assertEquals($expected, $actual);

        $inputFilter = $form->getInputFilter();
        $inputFilter->setData([]);
        self::assertTrue($inputFilter->isValid());
    }

    public function testVisitNonEmptyCollection(): void
    {
        $expected = new Union([
            new TKeyedArray([
                'foo' => new Union([
                    new TNonEmptyArray([
                        Type::getArrayKey(),
                        new Union([
                            new TKeyedArray([
                                'baz' => new Union([new TString(), new TNull()]),
                            ]),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $form       = new Form();
        $collection = new Collection('foo');
        $collection->setCount(3);
        $collection->setAllowRemove(false);
        $targetElement = new Fieldset('bar');
        $targetElement->add(new Email('baz'));
        $collection->setTargetElement($targetElement);
        $form->add($collection);

        $actual = $this->visitor->visit($form);
        self::assertEquals($expected, $actual);
    }

    public function testVisitCollectionWithTextTargetElement(): void
    {
        $expected = new Union([
            new TKeyedArray([
                'foo' => new Union([
                    new TArray([
                        Type::getArrayKey(),
                        new Union([new TString(), new TNull()]),
                    ]),
                ], ['possibly_undefined' => true]),
            ]),
        ], ['possibly_undefined' => true]);

        $form       = new Form();
        $collection = new Collection('foo');
        $collection->setTargetElement(new Text());
        $form->add($collection);

        $actual = $this->visitor->visit($form);
        self::assertEquals($expected, $actual);
    }
}
