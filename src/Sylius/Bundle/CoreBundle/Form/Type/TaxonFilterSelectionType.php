<?php

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\DataTransformer\TaxonSelectionToCollectionTransformer;
use Sylius\Bundle\TaxonomiesBundle\Model\Taxonomy;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JMS\TranslationBundle\Annotation\Ignore;

class TaxonFilterSelectionType extends AbstractType
{
    protected $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->repository->findAll() as $taxonomy) {
            /* @var $taxonomy Taxonomy*/
            $builder->add($taxonomy->getId(), 'choice', array(
                'choice_list' => new ObjectChoiceList($taxonomy->getTaxonsAsList()),
                'multiple'    => $options['multiple'],
                'label'       => /** @Ignore */ $taxonomy->getName()
            ));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'   => null,
                'multiple'     => true,
                'render_label' => false
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_taxon_filter_selection';
    }
}
