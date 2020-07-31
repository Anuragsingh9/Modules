<?php

namespace Modules\Cocktail\Services;

//use App\Services\Service;
use Modules\Events\Entities\Event;

class KctEventService extends Service {
    public static function getInstance()
    {
        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static();
        }
        return $instance;
    }
    /**
     * to set the fields in given column like
     * column = [
     *      $oldFieldName = $value,
     *      $oldFieldName = $value,
     *      $oldFieldName = $value,
     *      $fieldName = $value,
     * ]
     * This will keep the old values and if update/add field name already there then it will update its value
     * otherwise it will add extra key to array so previous will be persists and add new column
     *
     * @param null $event
     * @param null $eventUuid
     * @param string $columnName
     * @param array $values
     * @return Event|null
     */
    public function addOrUpdateEventJsonFields($columnName, $values, $event = NULL, $eventUuid = NULL) {
        if ($eventUuid) {
            $event = Event::where('event_uuid', $eventUuid)->first();
        }
        if ($event) {
            $oldData = $event->$columnName;
            foreach ($values as $k => $v)
                $oldData[$k] = $v;
            // uncomment following if shows array to string conversion
            // $oldData = json_encode($oldData, true);
            $event->update([$columnName => $oldData,]);
        }
        return $event;
    }

    public function AddEventUser($param) {
        $user = Event::create($param);

        if (!$user){
            throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
        }
        return $user;
    }
    public function RemoveEventUser($eventId,$userId) {
        $showEvent=Event::where([['user_id',$userId],['event_uuid',$userId]]);
        $showEvent->delete();

        if (!$showEvent){
            throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
        }
    }

    public function UpdateEventUserRole($user_id) {
        $event = Event::where('user_id','=',$user_id)->first();
        if($event['is_presenter'] == 1){
            $newDetail=$event['is_presenter'] = 0;
            $event->update(['is_presenter' => $newDetail]);
        }
        elseif($event['is_presenter'] == 0){

            $newDetail=$event['is_presenter'] = 1;
            $event->update(['is_presenter' => $newDetail]);
        }
        if($event['is_moderator'] == 2){
            $newDetail=$event['is_moderator'] = 0;
            $event->update(['is_moderator' => $newDetail]);
        }
        elseif($event['is_moderator'] == 0){
            $newDetail=$event['is_moderator'] = 2;
            $event->update(['is_moderator' => $newDetail]);
        }
        return $event;
    }

}