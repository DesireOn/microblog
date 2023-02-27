<?php

namespace App\DataMapper;

use App\Entity\Post;

/** Maps data from post request to a Post entity. */
class BlogPostMapper
{
    private Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * @param array $data
     * @return Post
     */
    public function toBlogPost(array $data): Post
    {
        if (isset($data['title'])) {
            $this->post->setTitle($data['title']);
        }
        if (isset($data['content'])) {
            $this->post->setContent($data['content']);
        }
        if (isset($data['isPublished']) && $data['isPublished'] === 'on') {
            $this->post->setIsPublished(true);
        } else {
            $this->post->setIsPublished(false);
        }

        return $this->post;
    }
}