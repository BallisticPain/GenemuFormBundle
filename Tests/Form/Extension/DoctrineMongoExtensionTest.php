<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Tests\Form\Extension;

use Symfony\Bundle\DoctrineMongoDBBundle\Form\DoctrineMongoDBExtension;

use Genemu\Bundle\FormBundle\Form\Type;

/**
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class DoctrineMongoExtensionTest extends DoctrineMongoDBExtension
{
    protected function loadTypes()
    {
        return array_merge(parent::loadTypes(), array(
            new Type\JQueryAutocompleterType($this->registry)
        ));
    }
}
