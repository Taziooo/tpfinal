cat > src/DataFixtures/UserFixtures.php <<'PHP'
<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@test.com');
        $user->setRoles(['ROLE_USER']);

        // Mot de passe: test1234
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'test1234');
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}
PHP
