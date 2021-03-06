<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;

use Genemu\Bundle\FormBundle\Form\EventListener\JQueryFileListener;

/**
 * JQueryFileType
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class JQueryFileType extends AbstractType
{
    protected $options;
    protected $rootDir;

    /**
     * Construct
     *
     * @param array $options
     */
    public function __construct(array $options, $rootDir)
    {
        $this->options = $options;
        $this->rootDir = $rootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $configs = array_merge($this->options, $options['configs']);

        if (
            (isset($options['multiple']) && $options['multiple']) or
            (isset($configs['multi']) && $configs['multi'])
        ) {
            $options['multiple'] = true;
        } else {
            $options['multiple'] = false;
        }

        $builder
            ->addEventSubscriber(new JQueryFileListener($this->rootDir, $options['multiple']))
            ->setAttribute('configs', $configs)
            ->setAttribute('rootDir', $this->rootDir)
            ->setAttribute('multiple', $options['multiple']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $data = $form->getClientData();
        $configs = $form->getAttribute('configs');
        $value = '';

        if ($data) {
            if ($form->getAttribute('multiple')) {
                if (is_array($data)) {
                    $values = array();
                    foreach ($data as $path) {
                        if (!$path instanceof File) {
                            $path = new File($this->rootDir . $path);
                        }

                        $values[] = $configs['folder'] . '/' . $path->getFilename();
                    }

                    $value = implode(',', $values);
                } else {
                    $value = $data;
                }
            } else {
                if (!$data instanceof File) {
                    $data = new File($this->rootDir . $data);
                }

                $value = $configs['folder'] . '/' .$data->getFilename();
            }
        }

        $view
            ->set('value', $value)
            ->set('configs', $configs);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'required' => false,
            'configs' => array()
        );

        return array_replace($defaultOptions, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'genemu_jqueryfile';
    }
}
