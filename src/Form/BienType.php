<?php

namespace App\Form;

use App\Entity\Bien;
use App\Entity\Option;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('surface')
            ->add('rooms')
            ->add('bedrooms')
            ->add('floor')
            ->add('price')
             ->add('heat', ChoiceType::class, [
                'choices' => $this->getChoices()
            ])
            //->add('options', EntityType::class, [
            //    'class' => Option::class,
            //    'required' => false,
            //    'choice_label' => 'name',
            //    'multiple' => true
           // ])
            ->add('pictureFiles', FileType::class, [
                'required' => false,
				'help' => 'Veuillez selectionner jusqu\'à trois images à la fois !!',
                'multiple' => true
				
            ])
            ->add('city')
            ->add('address')
            ->add('postal_code')
            //->add('lat', HiddenType::class)
            //->add('lng', HiddenType::class)
            ->add('sold')
			 ->add('user')
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bien::class,
			'translation_domain' => 'forms'
        ]);
    }
	
	private function getChoices()
    {
        $choices = Bien::HEAT;
        $output = [];
        foreach($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }
}
