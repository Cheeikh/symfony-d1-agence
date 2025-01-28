<?php

namespace App\Form;

use App\Entity\Agence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AgenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Détermine si le formulaire est utilisé pour la recherche
        $isSearch = $options['method'] === 'GET';

        $builder
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'placeholder' => $isSearch ? 'Rechercher par adresse' : 'Entrez l\'adresse de l\'agence',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'
                ],
                'label_attr' => [
                    'class' => 'block text-sm font-medium text-gray-700'
                ],
                'constraints' => $isSearch ? [] : [
                    new Assert\Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => 'L\'adresse doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'L\'adresse ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => $isSearch ? 'Rechercher par téléphone' : 'Entrez le numéro de téléphone',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'
                ],
                'label_attr' => [
                    'class' => 'block text-sm font-medium text-gray-700'
                ],
                'constraints' => $isSearch ? [] : [
                    new Assert\Regex([
                        'pattern' => '/^[0-9\s]{9,}$/',
                        'message' => 'Le numéro de téléphone doit contenir au moins 9 chiffres'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Agence::class,
            'validation_groups' => ['Default'],
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
