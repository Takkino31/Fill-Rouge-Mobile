<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProfileFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $profil_tab=["ADMIN_SYSTEM","ADMIN_AGENCE","CAISSIER"];

        foreach ($profil_tab as $lib_profil) {
          
            $profil=new Profile();
            $profil->setLibelle($lib_profil);
            $manager->persist($profil);
            $manager->flush();

            if ($lib_profil=="ADMIN_SYSTEM") {
                $this->setReference("adminsystem",$profil);
            }
            elseif ($lib_profil=="ADMIN_AGENCE") {
                $this->setReference("adminagence",$profil);
            }  
            elseif ($lib_profil=="CAISSIER") {
                $this->setReference("caissier",$profil);
            }
     
        }
    }
}
