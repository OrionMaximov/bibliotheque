<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Faker\Factory;
use App\Entity\Livre;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LivreFixture extends Fixture
{   
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
    }
    
        
    
    public function load(ObjectManager $manager): void
    {   
        $faker = Factory::create('fr_FR');
        for($i=0;$i<5;$i++){
            $user=new User();
            $user->setNom($faker->lastName())
                 ->setPrenom($faker->firstName())
                 ->setBirthDate($faker->dateTimeBetween($startDate = '-60 years', $endDate = 'now', $timezone = null))
                    ->setAdresse($faker->city())
                    ->setCodePostale($faker->postcode())
                    ->setEmail($faker->email())
                    ->setPassword($this->passwordEncoder->encodePassword($user,"azerty"))
                    ->setAvatar("https://picsum.photos/id/100/200/300");
                    
                    $manager->persist($user);
                    for($k=0;$k<3;$k++){
                            $category= new Categorie();
                            $category->setNom($faker->safeColorName());
                            $manager->persist($category);
                            for($j=0;$j<rand(2,5);$j++ ){
                                $livre= new Livre();
                                $livre  ->setAuteur($faker->name())
                                        ->setTitre($faker->word())
                                        ->setDateSortie($faker->dateTimeBetween($startDate = '-60 years', $endDate = 'now', $timezone = null));
                                        $livre->setUser($user);
                                        $livre->setCategorie($category);
                                $manager->persist($livre);
                            }
                }
        }
        $manager->flush();
    }
}
