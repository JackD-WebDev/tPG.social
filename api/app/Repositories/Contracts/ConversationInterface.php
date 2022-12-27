<?php

namespace App\Repositories\Contracts;

interface ConversationInterface extends BaseInterface
{
    public function createParticipants($conversationId, array $data);
    public function getUserConversations();
}
