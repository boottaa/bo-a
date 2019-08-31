<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Events;

use App\Entity\Comments;
use Symfony\Contracts\EventDispatcher\Event;

class CommentCreatedEvent extends Event
{
    protected $comment;

    public function __construct(Comments $comment)
    {
        $this->comment = $comment;
    }

    public function getComment(): Comments
    {
        return $this->comment;
    }
}
