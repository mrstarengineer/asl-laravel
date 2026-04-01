<?php


namespace App\Services\Conversation;


use App\Models\Conversation;
use App\Services\BaseService;

class ConversationService extends BaseService
{
    public function all ( array $filters = [] )
    {
        $query = Conversation::with([ 'sender' ]);

        if ( isset( $filters[ 'model_id' ] ) ) {
            $query->where( 'model_id', $filters[ 'model_id' ] );
        }

        if ( isset( $filters[ 'sender_id' ] ) ) {
            $query->where( 'sender_id', $filters[ 'sender_id' ] );
        }

        $query->get();
    }

    public function getById ( $id )
    {
        return Conversation::with([ 'sender' ])->find( $id );
    }

    public function store ( array $data )
    {
        return $this->saveConversation( $data );
    }

    public function update ( $id, array $data )
    {
        return $this->saveConversation( $data, $id );
    }

    public function destroy ( $id )
    {
        return Conversation::find( $id )->delete();
    }

    private function saveConversation ( $data, $id = null )
    {
        $msg = Conversation::findOrNew( $id );
        $msg->fill( $data );
        $msg->save();

        return $msg;
    }
}
