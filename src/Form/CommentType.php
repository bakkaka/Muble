<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;



class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('authorName', TextType::class)
            ->add('content', TextareaType::class)
            ->add('authorName', EntityType::class, [
                'label' => 'User',
                'class' => User::class,
                'choice_label' => 'username',
                'multiple' => false,
                'expanded' => false,
            ])
			;
			 $imageConstraints = [
            new Image([
                'maxSize' => '5M'
            ])
        ];
        if (!$isEdit || !$article->getImageFilename()) {
            $imageConstraints[] = new NotNull([
                'message' => 'Please upload an image',
            ]);
        }
        $builder
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => $imageConstraints
            ])
        ;

            $builder->add('createdAt', DateTimeType::class)
            ->add('updatedAt', DateTimeType::class )
            // ->add('article')
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
