<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Categorie;
use App\Entity\Scategorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SupprimerSousCategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('categories', EntityType::class, [
            'class' => Categorie::class,
            'choices' => $options['categories'],
            'choice_label' => 'libelle',
            'expanded' => true,
            'multiple' => true,
            'label' => false, 'mapped' => false])
            ->add('supprimer', SubmitType::class, ['attr' => ['class' => 'btn bg-primary text-white m-4'],
                'row_attr' => ['class' => 'text-center']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'scategories' => Scategorie::class,
        ]);
    }
}
