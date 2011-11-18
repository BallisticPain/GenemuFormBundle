<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Tests;

use Symfony\Tests\Component\Form\Extension\Core\Type\TypeTestCase as BaseTypeTestCase;

/**
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
abstract class TypeTestCase extends BaseTypeTestCase
{
    public function setUp()
    {
        parent::setUp();

        \Locale::setDefault('de_DE');
    }

    protected function getExtensions()
    {
        return array(
            new TypeExtensionTest()
        );
    }
}