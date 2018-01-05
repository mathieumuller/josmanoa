<?php

namespace App\Entity;

use App\Model\RoleInterface;
use App\Traits\TimeAwareEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface, \Serializable, RoleInterface
{
    use TimeAwareEntity;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=80, unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=80, unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive = true;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    protected $roles = [RoleInterface::ROLE_USER];

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Media", mappedBy="owner")
     */
    protected $medias;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Album", mappedBy="owner")
     */
    protected $albums;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->albums = new ArrayCollection();
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password
        ) = unserialize($serialized);
    }

    /**
     * @return ArrayCollection
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @param ArrayCollection $medias
     *
     * @return self
     */
    public function setMedias(ArrayCollection $medias)
    {
        $this->medias = $medias;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * @param ArrayCollection $albums
     *
     * @return self
     */
    public function setAlbums(ArrayCollection $albums)
    {
        $this->albums = $albums;

        return $this;
    }
}
