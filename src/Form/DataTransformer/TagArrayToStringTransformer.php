<?php

namespace App\Form\DataTransformer;

use App\Entity\Tags;
use App\Repository\TagsRepository;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class TagArrayToStringTransformer
 * @package App\Form\DataTransformer
 */
class TagArrayToStringTransformer implements DataTransformerInterface
{
    private $tags;

    /**
     * TagArrayToStringTransformer constructor.
     * @param TagsRepository $tags
     */
    public function __construct(TagsRepository $tags)
    {
        $this->tags = $tags;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($tags): string
    {
        // The value received is an array of Tag objects generated with
        // Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer::transform()
        // The value returned is a string that concatenates the string representation of those objects

        /* @var Tags[] $tags */
        return implode(' ', $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string): array
    {
        if ('' === $string || null === $string) {
            return [];
        }

        $names = array_filter(array_unique(array_map('trim', explode(' ', mb_strtolower($string)))));

        // Get the current tags and find the new ones that should be created.
        $tagsArray = $this->tags->findBy([
            'name' => $names
        ]);

        $newNames = array_diff($names, $tagsArray);
        foreach ($newNames as $name) {
            $tag = new Tags();
            $tag->setName($name);
            $tagsArray[] = $tag;

            // There's no need to persist these new tags because Doctrine does that automatically
            // thanks to the cascade={"persist"} option in the App\Entity\Post::$tags property.
        }

        // Return an array of tags to transform them back into a Doctrine Collection.
        // See Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer::reverseTransform()
        return $tagsArray;
    }
}
