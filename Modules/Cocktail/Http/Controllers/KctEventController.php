<?php

namespace Modules\Cocktail\Http\Controllers;

use App\Http\Requests\EventUserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Entities\EventUser;
use Modules\Cocktail\Http\Requests\EventBlueJeansRequest;
use Modules\Cocktail\Http\Requests\EventKeepContactRequest;
use Modules\Cocktail\Http\Requests\EventRegistaionFormRequest;
use Modules\Cocktail\Services\KctEventService;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Services\DataService;
use Modules\Cocktail\Transformers\EventBlueJeansResource;
use Modules\Cocktail\Transformers\EventGraphicsResource;
use Modules\Events\Entities\Event;

class KctEventController extends Controller {
    
    public function __construct() {
        $this->service = KctEventService::getInstance();
    }
    
    /**
     * To update the event setting, specially for keepContact setting of particular event
     *
     * @param EventKeepContactRequest $request
     * @return EventGraphicsResource|JsonResponse
     */
    public function updateKeepContactSetting(EventKeepContactRequest $request) {
        $dataService = DataService::getInstance();
        try {
            DB::connection('tenant')->beginTransaction();
            $param = $dataService->prepareKeepContactSetting($request);
            // this will add keepContact in json data to eventFields column of event
            // and providing event uuid so event will be also fetched from there
            $event = $this->service->addOrUpdateEventJsonFields('event_fields', $param, NULL, $request->event_uuid);
            DB::connection('tenant')->commit();
            return (new EventGraphicsResource($event))->additional(['status' => TRUE]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 200);
        }
    }
    
    /**
     * To update the event setting, specially for blueJeans setting of particular event
     * TODO add blue jeans event id also here this method will be gonna call for both update and create so
     * first fetch the bluejeans id from event table if there no bluejeans id registered then create a new bluejeans event for it
     *
     * @param EventBlueJeansRequest $request
     * @return EventBlueJeansResource|JsonResponse
     */
    public function updateBlueJeansSetting(EventBlueJeansRequest $request) {
        $dataService = DataService::getInstance()->prepareBlueJeansParam($request);
        try {
            DB::connection('tenant')->beginTransaction();
            $param = $dataService->prepareBlueJenasParam($request);
            $event = $this->service->addOrUpdateEventJsonFields('bluejeans_settings', $param, NULL, $request->event_uuid);
            DB::connection('tenant')->commit();
            return (new EventBlueJeansResource($event))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
        }
    }
    
    /**
     * To update the event setting, specially for registration form details setting of particular event
     *
     * @param EventRegistaionFormRequest $request
     * @return Event|null
     */
    public function updateRegistrationFormDetail(EventRegistaionFormRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $param = [
                'display' => $request->display,
                'title'   => $request->title,
                'points'  => $request->points,
            ];
            $event = $this->service->addOrUpdateEventJsonFields('registration_details', $param, 'event_fields', NULL, $request->event_id);
            DB::connection('tenant')->commit();
            return $event;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
        }
    }
    
    public function getEventGraphicsDetails(Request $request, $event_uuid) {
        $event = Event::where('event_uuid', $event_uuid)->first();
        return (new EventGraphicsResource($event))->additional(['status' => TRUE]);
    }
    
    public function getEventBlueJeansDetails(Request $request, $event_uuid) {
        $event = Event::where('event_key', $event_uuid)->first();
        return (new EventBlueJeansResource($event))->additional(['status' => TRUE]);
    }

    public function addUserToEvent(EventUserRequest $request)
    {
        // DB::connection('tenant')->beginTransaction();
        DB::beginTransaction();

        try{
            $param =[
                'user_id'=>$request->user_id,
                'is_presenter'=>$request->is_presenter,
                'is_moderator'=>$request->is_moderator,
                'state' => $request->state,
            ];
            $this->service->AddEventUser($param);
            //    DB::connection('tenant')->commit();
            DB::commit();
        }catch(\Exception $e){
            // DB::connection('tenant')->rollback();
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }

    public function removeUserFromEvent($eventId,$userId){
        try{
            $this->service->RemoveEventUser($eventId,$userId);
        }catch(\Exception $e){
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);

        }
    }

    public function changeEvenUsertRole(EventUserRequest $request){
        // dd("ok");
        // DB::connection('tenant')->beginTransaction();
        DB::beginTransaction();

        try{
            $event=$this->service->UpdateEventUserRole($request->user_id);
            //    DB::connection('tenant')->commit();
            DB::commit();
            return  new UserEventResource($event);

        }catch(\Exception $e){
            // DB::connection('tenant')->rollback();
            DB::rollback();

            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }
    public function check(Request $request){
        $fname=$request->get('fname');
        $lname=$request->get('lname');
        $email=$request->get('email');
        $us=Auth::user()->id;
        $user= User::where('fname', 'LIKE',"$fname%")->orWhere('lname', 'LIKE',"$lname%")
            ->orWhere('email','LIKE',"$email%")->get();
        dd($user);
    }



    
    
}
