<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Categorie;
use App\Entity\Scategorie;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ModifierSousCategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, ['attr' => ['class' => 'form-control'], 'label_attr' => ['class' =>
                'fw-bold']])
                ->add('numero', IntegerType::class, ['attr' => ['class' => 'form-control'], 'label_attr' => ['class' =>
                'fw-bold']])
            ->add('modifier', SubmitType::class, ['attr' => ['class' => 'btn bg-primary text-white m-4'],
                'row_attr' => ['class' => 'text-center']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'scategories' => Scategorie::class,
        ]);
    }
}
