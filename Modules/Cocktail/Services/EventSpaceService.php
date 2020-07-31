<?php

namespace Modules\Cocktail\Services;

use App\Services\Service;
use Exception;
use Modules\Cocktail\Entities\EventSpace;

class EventSpaceService extends Service {
//    public static function getInstance()
//    {
//        static $instance = NULL;
//        if (NULL === $instance) {
//            $instance = new static();
//        }
//        return $instance;
//    }
    /**
     * @param $param
     * @return mixed
     * @throws Exception
     */
    public function create($param,$userId,$spaceUuid) {
        $event = EventSpace::create($param);
        foreach($param['hosts'] as $host) {
            $hosts[] = [
                'user_id' => $host,
                'space_uuid' => $spaceUuid,
                'role' => 1
            ];
        }
        SpaceUser::insert($hosts);
        if (!$event)
            throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
        return $event;
    }
    
    /**
     * @param $param
     * @param $space_uuid
     * @return mixed
     * @throws Exception
     */
    public function update($param, $space_uuid) {
        $updated = EventSpace::find($space_uuid)->update($param);
        if (!$updated)
            throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
        return EventSpace::find($space_uuid);
    }
    
    /**
     * @param $event_uuid
     * @return mixed
     * @throws Exception
     */
    public
    function getEventSpaces($event_uuid) {
        $spaces = EventSpace::where('event_uuid', $event_uuid)->get();
        if (!$spaces)
            throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
        return $spaces;
    }
    
    public
    function addUserToSpace($userId, $spaceUuid, $request) {
        $param=[
            'user_id'=>$userId,
            'space_uuid'=>$spaceUuid,
        ];
        $addUser = EventSpace::create($param);

        if (!$addUser)
            throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
        return $addUser;
    }
    
    public
    function removeUserFromSpace($userId, $spaceUuid) {
        $showEvent=EventSpace::where([['user_id',$userId], ['space_uuid',$spaceUuid]]);
        $showEvent->delete();
        if (!$showEvent)
            throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
        return $showEvent;
    }
}