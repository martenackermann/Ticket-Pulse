<?php

namespace App\Actions\Comments;

use App\Events\CommentCreated;
use App\Models\Card;
use App\Models\Comment;
use App\Models\User;

class CreateCommentAction
{
    public function execute(Card $card, User $user, array $data): Comment
    {
        $comment = $card->comments()->create([
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);

        $card->board->activities()->create([
            'user_id' => $user->id,
            'event_type' => 'comment_created',
            'payload' => [
                'card_title' => $card->title,
                'comment_id' => $comment->id,
            ],
        ]);

        broadcast(new CommentCreated($comment))->toOthers();

        return $comment;
    }
}
