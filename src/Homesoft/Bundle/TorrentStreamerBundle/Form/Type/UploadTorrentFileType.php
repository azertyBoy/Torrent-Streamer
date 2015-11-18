<?php

namespace Homesoft\Bundle\TorrentStreamerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UploadTorrentFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('torrentFile', 'file')
            ->add('upload', 'submit')
        ;
    }

    public function getName()
    {
        return 'uploadTorrentFile';
    }
}