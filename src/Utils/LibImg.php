<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Img;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class LibImg
 * @package App\Utils
 */
class LibImg
{

    private const
        URL = '/img/',
        DIR = __DIR__ . '/../../public/img/';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AddedNews constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UploadedFile $file
     *
     * @return Img|null
     * @throws Exception
     */
    public function upload(UploadedFile $file): ?Img
    {
        $extension = $file->guessExtension();
        if (!$extension) {
            return null;
        }

        $name = $file->getClientOriginalName();
        $size = $file->getSize();

        $fileUploaded = $file->move(self::DIR, md5(random_bytes(10)) . '.' . $extension);
        $url = self::URL . $fileUploaded->getFilename();
        $path = $fileUploaded->getRealPath();
        $type = $fileUploaded->getMimeType();

        $img = (new Img())
            ->setName($name)
            ->setType($type)
            ->setSize($size)
            ->setPath($path)
            ->setUrl($url);

        $this->entityManager->persist($img);
        $this->entityManager->flush();

        return $img;
    }

    /**
     * @param Img $img
     */
    public function delete(Img $img): void
    {
        $this->entityManager->remove($img);
        $this->entityManager->flush();
        unlink($img->getPath());
    }
}
