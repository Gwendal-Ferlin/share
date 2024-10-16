<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class FichierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomOriginal',TextType::class, ['attr' => ['class' => 'form-control'], 'label_attr' => ['class' =>
            'fw-bold']])
            ->add('nomServeur',TextType::class, ['attr' => ['class' => 'form-control'], 'label_attr' => ['class' =>
            'fw-bold']])
            ->add('dateEnvoi',DateType::class, ['attr' => ['class' => 'form-control'], 'label_attr' => ['class' =>
            'fw-bold']])
            ->add('extension',TextType::class, ['attr' => ['class' => 'form-control'], 'label_attr' => ['class' =>
            'fw-bold']])
            ->add('taille',IntegerType::class, ['attr' => ['class' => 'form-control'], 'label_attr' => ['class' =>
            'fw-bold']])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($user) {
                    return $user->getName() . ' ' . $user->getPrenom();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC')
                        ->addOrderBy('u.prenom', 'ASC');
                },
            ])
            ->add('envoyer', SubmitType::class, ['attr' => ['class' => 'btn bg-primary text-white m-4'],
                'row_attr' => ['class' => 'text-center']]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
