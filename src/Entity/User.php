<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 * @UniqueEntity(fields={"email"})
 */
class User implements UserInterface
{
    // Ce trait permet de rendre une entité supprimable en douce.
    // Mais pour qu'il soit fonctionnel, il faut activer
    // la fonctionnalité dans le fichier de configuration
    // config/packages/stof_doctrine_extensions.yaml en
    // ajoutant le code suivant :
    //    orm:
    //        default:
    //            softdeleteable: true
    // dans la section stof_doctrine_extensions.
    // Et pour que les objets supprimés en douce ne soient plus
    // visible il faut activer le filtrage dans le fichier
    // de configuration config/packages/doctrine.yaml
    // en ajoutant le code suivant :
    //         filters:
    //             softdeleteable:
    //                 class: Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter
    //                 enabled: true
    // dans la section orm.
    use SoftDeleteableEntity;

    // Ce trait permet de rendre une entité horodatable.
    // Mais pour qu'il soit fonctionnel, il faut activer
    // la fonctionnalité dans le fichier de configuration
    // config/packages/stof_doctrine_extensions.yaml en
    // ajoutant le code suivant :
    //    orm:
    //        default:
    //            timestampable: true
    // dans la section stof_doctrine_extensions.
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @Assert\Length(
     *   min = 6,
     *   max = 180
     * )
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Assert\Choice(
     *   {"ROLE_ADMIN", "ROLE_USER", "ROLE_STUDENT", "ROLE_TEACHER", "ROLE_CLIENT"},
     *   multiple=true,
     *   multipleMessage="Seules les valeurs suivantes sont valides."
     * )
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
