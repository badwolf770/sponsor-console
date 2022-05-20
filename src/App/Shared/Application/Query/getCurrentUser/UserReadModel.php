<?php
declare(strict_types=1);

namespace App\Shared\Application\Query\getCurrentUser;

use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;

class UserReadModel
{
    /**
     * @OA\Property (
     *     description="Id",
     *     type="int",
     *     example=1
     *  )
     * @Serializer\Groups({"default"})
     */
    public int $id;
    /**
     * @OA\Property (
     *     description="Имя",
     *     type="string",
     *     example="Владимир"
     *  )
     * @Serializer\Groups({"default"})
     */
    public string $name;
    /**
     * @OA\Property (
     *     description="Фамилия",
     *     type="string",
     *     example="Путини"
     *  )
     * @Serializer\Groups({"default"})
     */
    public string $surname;
    /**
     * @OA\Property (
     *     description="Почта",
     *     type="string",
     *     example="putini@ua.ru"
     *  )
     * @Serializer\Groups({"default"})
     */
    public string $email;

    /**
     * @OA\Property(description="Роли", type="array",
     *      @OA\Items(
     *          type="string",
     *          example="ROLE_USER"
     *      )
     * )
     * @Serializer\Groups({"default"})
     */
    public array $roles;

    public function __construct(int $id, string $name, string $surname, string $email, array $roles)
    {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->roles = $roles;
    }


}