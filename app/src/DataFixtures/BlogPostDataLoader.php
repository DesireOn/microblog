<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BlogPostDataLoader implements FixtureInterface
{
    private const TITLES = [
        "10 Tips for a More Productive Workday",
        "The Benefits of Mindfulness Meditation: A Beginner's Guide",
        "The Top 5 Destinations for Budget Travelers",
        "The Impact of Social Media on Mental Health",
        "The Importance of Self-Care: Practicing Mindfulness and Gratitude"
    ];

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $title = self::TITLES[$i];

            $blogPost = new Post();
            $blogPost->setTitle($title);
            $blogPost->setContent("Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.");
            $blogPost->setIsPublished(array_rand([true, false]));
            $blogPost->setFeaturedImage('test.jpg');

            $manager->persist($blogPost);
            $manager->flush();
        }
    }
}